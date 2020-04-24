package pt.ulisboa.tecnico.cmov.foodist.server;

import io.vertx.core.AbstractVerticle;
import io.vertx.core.http.HttpServerOptions;
import io.vertx.core.logging.Logger;
import io.vertx.core.logging.LoggerFactory;
import io.vertx.core.net.PemKeyCertOptions;
import io.vertx.ext.web.Router;
import io.vertx.ext.web.handler.BodyHandler;
import pt.ulisboa.tecnico.cmov.foodist.server.resource.DishResource;

public class MyApiVerticle extends AbstractVerticle {
    private static final Logger LOGGER = LoggerFactory.getLogger(MyApiVerticle.class);
    private static final int KB = 1024;
    private static final int MB = 1024 * KB;
    private static final String BASE_URL = "/api/v1";

    @Override
    public void start() throws Exception {
        LOGGER.info("----- In start -----");

        Router router = Router.router(vertx);

        // Handlers
        router.route().handler(BodyHandler.create().setBodyLimit(50 * MB)); // to prevent ddos with abusive uploads
        router.errorHandler(500, rc -> {
            System.err.println("Handling failure");
            Throwable failure = rc.failure();
            if (failure != null)
                failure.printStackTrace();
        });

        // Endpoints
        router.get("/").handler(event -> event.response().end("Welcome to FoodIST REST pt.ulisboa.tecnico.cmov.foodist.server.Server!"));

        // Dishes endpoints
        final DishResource dishResource = new DishResource();
        final Router dishSubRouter = dishResource.getSubRouter(vertx);
        router.mountSubRouter(BASE_URL + "/dishes", dishSubRouter);

        PemKeyCertOptions cert = new PemKeyCertOptions().setKeyPath("server-key.pem").setCertPath("server-cert.pem");
        vertx.createHttpServer(new HttpServerOptions().setSsl(true).setPemKeyCertOptions(cert))
                .requestHandler(router)
                .listen(3000);
    }

    @Override
    public void stop() throws Exception {
        LOGGER.info("----- In stop -----");
    }
}