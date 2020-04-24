package pt.ulisboa.tecnico.cmov.foodist.server;

import io.vertx.core.AbstractVerticle;
import io.vertx.core.Promise;
import io.vertx.core.http.HttpServer;
import io.vertx.core.http.HttpServerOptions;
import io.vertx.core.net.PemKeyCertOptions;
import io.vertx.ext.web.Router;
import io.vertx.ext.web.RoutingContext;
import io.vertx.ext.web.handler.BodyHandler;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import pt.ulisboa.tecnico.cmov.foodist.server.resource.CafeteriaResource;
import pt.ulisboa.tecnico.cmov.foodist.server.resource.DishResource;

public class HttpServerVerticle extends AbstractVerticle {
    private static final Logger LOGGER = LoggerFactory.getLogger(HttpServerVerticle.class);
    private static final int KB = 1024;
    private static final int MB = 1024 * KB;
    public static final String CONFIG_HTTP_SERVER_PORT = "http.server.port";

    @Override
    public void start(Promise<Void> promise) {
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
        router.get("/").handler(this::indexHandler);

        // Dishes endpoints
        final DishResource dishResource = new DishResource();
        router.mountSubRouter("/api/v1/dishes", dishResource.getSubRouter(vertx));

        // Cafeterias endpoints
        final CafeteriaResource cafeteriaResource = new CafeteriaResource();
        router.mountSubRouter("/api/v1/cafeterias", cafeteriaResource.getSubRouter(vertx));

        int portNumber = config().getInteger(CONFIG_HTTP_SERVER_PORT, 3000);
        server.requestHandler(router).listen(portNumber, ar -> {
            if (ar.succeeded()) {
                LOGGER.info("HTTP server running on port " + portNumber);
                promise.complete();
            } else {
                LOGGER.error("Could not start a HTTP server", ar.cause());
                promise.fail(ar.cause());
            }
        });
    }

    private void indexHandler(RoutingContext context) {
        context.response().putHeader("Content-Type", "text/html");
        context.response().end("Welcome to FoodIST REST Server!");
    }
}