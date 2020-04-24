package pt.ulisboa.tecnico.cmov.foodist.server;

import io.vertx.core.Vertx;

public class Server {
    public static void main(String[] args) {
        System.out.println("Welcome to FoodIST REST pt.ulisboa.tecnico.cmov.foodist.server.Server " + "1.0-SNAPSHOT");
        final Vertx vertx = Vertx.vertx();
        vertx.deployVerticle(new MyApiVerticle());
    }
}
