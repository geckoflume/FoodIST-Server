package pt.ulisboa.tecnico.cmov.foodist.server.resource;

import io.vertx.core.Vertx;
import io.vertx.core.json.Json;
import io.vertx.core.json.JsonObject;
import io.vertx.ext.web.Router;
import io.vertx.ext.web.RoutingContext;
import pt.ulisboa.tecnico.cmov.foodist.server.model.Cafeteria;
import pt.ulisboa.tecnico.cmov.foodist.server.model.Dish;
import pt.ulisboa.tecnico.cmov.foodist.server.service.CafeteriaService;
import pt.ulisboa.tecnico.cmov.foodist.server.service.DishService;

import java.util.List;

public class CafeteriaResource {
    private final CafeteriaService cafeteriaService = new CafeteriaService();

    public Router getSubRouter(final Vertx vertx) {
        final Router subRouter = Router.router(vertx);
        //subRouter.route("/*").handler(BodyHandler.create());
        subRouter.get("/").handler(this::getAllCafeterias);
        subRouter.get("/:id").handler(this::getCafeteriaId);
        subRouter.get("/:id/dishes").handler(this::getCafeteriaDishes);
        return subRouter;
    }

    private void getAllCafeterias(RoutingContext routingContext) {
        final List<Cafeteria> cafeterias = cafeteriaService.findAll();

        final JsonObject jsonResponse = new JsonObject();
        jsonResponse.put("cafeterias", cafeterias);

        routingContext.response()
                .setStatusCode(200)
                .putHeader("content-type", "application/json")
                .end(Json.encode(jsonResponse)); // not prettied for performance, can use Json.encodePrettily() instead
    }

    private void getCafeteriaId(RoutingContext routingContext) {
        final int id = Integer.parseInt(routingContext.request().getParam("id"));
        final Cafeteria cafeteria = cafeteriaService.findById(id);

        if (cafeteria == null) {
            final JsonObject errorJsonResponse = new JsonObject();
            errorJsonResponse.put("error", "No cafeteria can be found for the specified id:" + id);
            errorJsonResponse.put("id", id);

            routingContext.response()
                    .setStatusCode(404)
                    .putHeader("content-type", "application/json")
                    .end(Json.encode(errorJsonResponse));
            return;
        }
        routingContext.response()
                .setStatusCode(200)
                .putHeader("content-type", "application/json")
                .end(Json.encode(cafeteria));
    }

    private void getCafeteriaDishes(RoutingContext routingContext) {
        final int id = Integer.parseInt(routingContext.request().getParam("id"));
        final Cafeteria cafeteria = cafeteriaService.findById(id);

        if (cafeteria == null) {
            final JsonObject errorJsonResponse = new JsonObject();
            errorJsonResponse.put("error", "No cafeteria can be found for the specified id:" + id);
            errorJsonResponse.put("id", id);

            routingContext.response()
                    .setStatusCode(404)
                    .putHeader("content-type", "application/json")
                    .end(Json.encode(errorJsonResponse));
            return;
        }


        final DishService dishService = new DishService();
        final List<Dish> dishes = dishService.findByCafeteriaId(id);

        final JsonObject jsonResponse = new JsonObject();
        jsonResponse.put("dishes", dishes);

        routingContext.response()
                .setStatusCode(200)
                .putHeader("content-type", "application/json")
                .end(Json.encode(jsonResponse));
    }
}
