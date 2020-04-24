package pt.ulisboa.tecnico.cmov.foodist.server.resource;

import io.vertx.core.Vertx;
import io.vertx.core.json.Json;
import io.vertx.core.json.JsonObject;
import io.vertx.ext.web.Router;
import io.vertx.ext.web.RoutingContext;
import io.vertx.ext.web.handler.BodyHandler;
import pt.ulisboa.tecnico.cmov.foodist.server.model.Dish;
import pt.ulisboa.tecnico.cmov.foodist.server.service.DishService;

import java.util.List;

public class DishResource {
    private final DishService dishService = new DishService();

    public Router getSubRouter(final Vertx vertx) {
        final Router subRouter = Router.router(vertx);
        subRouter.route("/*").handler(BodyHandler.create());
        subRouter.get("/").handler(this::getAllDishes);
        subRouter.get("/:id").handler(this::getDishId);
        subRouter.post("/").handler(this::createDish);
        subRouter.put("/:id").handler(this::updateDish);
        subRouter.delete("/:id").handler(this::deleteDish);
        return subRouter;
    }

    private void getAllDishes(RoutingContext routingContext) {
        final List<Dish> dishes = dishService.findAll();

        final JsonObject jsonResponse = new JsonObject();
        jsonResponse.put("dishes", dishes);

        routingContext.response()
                .setStatusCode(200)
                .putHeader("content-type", "application/json")
                .end(Json.encode(jsonResponse)); // not prettied for performance, can use Json.encodePrettily() instead
    }

    private void getDishId(RoutingContext routingContext) {
        final int id = Integer.parseInt(routingContext.request().getParam("id"));
        final Dish dish = dishService.findById(id);

        if (dish == null) {
            final JsonObject errorJsonResponse = new JsonObject();
            errorJsonResponse.put("error", "No dish can be found for the specified id:" + id);
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
                .end(Json.encode(dish));
    }

    private void createDish(RoutingContext routingContext) {
        final JsonObject body = routingContext.getBodyAsJson();
        final Integer cafeteriaId = body.getInteger("cafeteria-id");
        final String name = body.getString("name");
        final Double price = body.getDouble("price");
        if (cafeteriaId == null || name == null || price == null) {
            final JsonObject errorJsonResponse = new JsonObject();
            errorJsonResponse.put("error", "Invalid request");

            routingContext.response()
                    .setStatusCode(400)
                    .putHeader("content-type", "application/json")
                    .end(Json.encode(errorJsonResponse));
            return;
        }
        final Dish dish = new Dish(null, cafeteriaId, name, price);
        final Dish createdDish = dishService.add(dish);

        routingContext.response()
                .setStatusCode(201)
                .putHeader("content-type", "application/json")
                .end(Json.encode(createdDish));
    }

    private void updateDish(RoutingContext routingContext) {
        final int id = Integer.parseInt(routingContext.request().getParam("id"));
        final JsonObject body = routingContext.getBodyAsJson();
        final Integer cafeteriaId = body.getInteger("cafeteria-id");
        final String name = body.getString("name");
        final Double price = body.getDouble("price");
        if (cafeteriaId == null || name == null || price == null) {
            final JsonObject errorJsonResponse = new JsonObject();
            errorJsonResponse.put("error", "Invalid request");

            routingContext.response()
                    .setStatusCode(400)
                    .putHeader("content-type", "application/json")
                    .end(Json.encode(errorJsonResponse));
            return;
        }
        final Dish dog = new Dish(id, cafeteriaId, name, price);
        final Dish updatedDish = dishService.update(dog);

        routingContext.response()
                .setStatusCode(200)
                .putHeader("content-type", "application/json")
                .end(Json.encode(updatedDish));
    }

    private void deleteDish(RoutingContext routingContext) {
        final int id = Integer.parseInt(routingContext.request().getParam("id"));

        dishService.remove(id);

        routingContext.response()
                .setStatusCode(200)
                .putHeader("content-type", "application/json")
                .end();
    }
}
