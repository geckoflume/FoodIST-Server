package pt.ulisboa.tecnico.cmov.foodist.server;

import io.vertx.core.AbstractVerticle;
import io.vertx.core.DeploymentOptions;
import io.vertx.core.Promise;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class MainVerticle extends AbstractVerticle {
    private static final Logger LOGGER = LoggerFactory.getLogger(MainVerticle.class);

    @Override
    public void start(Promise<Void> promise) {
        Promise<String> dbVerticleDeployment = Promise.promise();
        vertx.deployVerticle(new DatabaseVerticle(), dbVerticleDeployment);

        dbVerticleDeployment.future().compose(id -> {
            Promise<String> httpVerticleDeployment = Promise.promise();
            vertx.deployVerticle(
                    "pt.ulisboa.tecnico.cmov.foodist.server.HttpServerVerticle",
                    new DeploymentOptions().setInstances(1), // can deploy 2 instances for load balancing
                    httpVerticleDeployment);
            return httpVerticleDeployment.future();
        }).onComplete(ar -> {
            if (ar.succeeded()) {
                promise.complete();
            } else {
                promise.fail(ar.cause());
            }
        });
    }
}