package pt.ulisboa.tecnico.cmov.foodist.server;

import io.vertx.core.AbstractVerticle;
import io.vertx.core.Future;
import io.vertx.core.Promise;
import io.vertx.core.http.HttpServer;
import io.vertx.core.http.HttpServerOptions;
import io.vertx.core.json.JsonObject;
import io.vertx.core.net.PemKeyCertOptions;
import io.vertx.ext.jdbc.JDBCClient;
import io.vertx.ext.sql.SQLConnection;
import io.vertx.ext.web.Router;
import io.vertx.ext.web.handler.BodyHandler;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import pt.ulisboa.tecnico.cmov.foodist.server.resource.CafeteriaResource;
import pt.ulisboa.tecnico.cmov.foodist.server.resource.DishResource;

public class MainVerticle extends AbstractVerticle {
    private static final String SQL_CREATE_DISHES_TABLE = "create table if not exists dishes (id integer identity primary key, cafeteria_id integer, name varchar(255), price double)";
    private static final String SQL_GET_PAGE = "select Id, Content from Pages where Name = ?";
    private static final String SQL_CREATE_PAGE = "insert into Pages values (NULL, ?, ?)";
    private static final String SQL_SAVE_PAGE = "update Pages set Content = ? where Id = ?";
    private static final String SQL_ALL_PAGES = "select Name from Pages";
    private static final String SQL_DELETE_PAGE = "delete from Pages where Id = ?";
    private static final Logger LOGGER = LoggerFactory.getLogger(MainVerticle.class);
    private JDBCClient dbClient;

    private static final int KB = 1024;
    private static final int MB = 1024 * KB;

    @Override
    public void start() throws Exception {
        LOGGER.info("In start");

        Promise<Void> promise = Promise.promise();
        Future<Void> steps = prepareDatabase().compose(v -> startHttpServer());
        steps.onComplete(promise);
    }

    private Future<Void> prepareDatabase() {
        Promise<Void> promise = Promise.promise();

        dbClient = JDBCClient.createShared(vertx, new JsonObject()
                .put("url", "jdbc:hsqldb:file:db/foodist")
                .put("driver_class", "org.hsqldb.jdbcDriver")
                .put("max_pool_size", 30)); // concurrent connections

        dbClient.getConnection(ar -> {
            if (ar.failed()) {
                LOGGER.error("Could not open a database connection", ar.cause());
                promise.fail(ar.cause());
            } else {
                SQLConnection connection = ar.result();
                connection.execute(SQL_CREATE_DISHES_TABLE, create -> {
                    connection.close();
                    if (create.failed()) {
                        LOGGER.error("Database preparation error", create.cause());
                        promise.fail(create.cause());
                    } else {
                        promise.complete();
                    }
                });
            }
        });

        return promise.future();
    }

    private Future<Void> startHttpServer() {
        Promise<Void> promise = Promise.promise();
        PemKeyCertOptions cert = new PemKeyCertOptions().setKeyPath("server-key.pem").setCertPath("server-cert.pem");
        HttpServer server = vertx.createHttpServer(new HttpServerOptions().setSsl(true).setPemKeyCertOptions(cert));

        Router router = Router.router(vertx);

        // Handlers
        router.route().handler(BodyHandler.create().setBodyLimit(50 * MB)); // to prevent ddos with abusive uploads
        router.errorHandler(500, rc -> {
            Throwable failure = rc.failure();
            if (failure != null)
                failure.printStackTrace();
        });

        // Endpoints
        router.get("/").handler(event -> event.response().end("Welcome to FoodIST REST pt.ulisboa.tecnico.cmov.foodist.server.Server!"));

        // Dishes endpoints
        final DishResource dishResource = new DishResource();
        router.mountSubRouter("/api/v1/dishes", dishResource.getSubRouter(vertx));

        // Cafeterias endpoints
        final CafeteriaResource cafeteriaResource = new CafeteriaResource();
        router.mountSubRouter("/api/v1/cafeterias", cafeteriaResource.getSubRouter(vertx));

        //templateEngine = FreeMarkerTemplateEngine.create(vertx);

        server.requestHandler(router).listen(3000, ar -> {
            if (ar.succeeded()) {
                LOGGER.info("HTTP server running on port 3000");
                promise.complete();
            } else {
                LOGGER.error("Could not start a HTTP server", ar.cause());
                promise.fail(ar.cause());
            }
        });

        return promise.future();
    }
}