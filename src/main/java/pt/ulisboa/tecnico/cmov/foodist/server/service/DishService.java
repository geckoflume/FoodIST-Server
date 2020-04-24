package pt.ulisboa.tecnico.cmov.foodist.server.service;

import pt.ulisboa.tecnico.cmov.foodist.server.model.Dish;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class DishService {
    private final Map<Integer, Dish> dishes = new HashMap<>();
    private Integer currentDishId = 0;

    public DishService() {
        super();
        initDishes();
    }

    private void initDishes() {
        add(new Dish(null, 1, "Soup", 0.6));
        add(new Dish(null, 1, "Cooked Porkchop", 1.5));
    }

    public List<Dish> findAll() {
        return new ArrayList<>(dishes.values());
    }

    public Dish findById(final Integer id) {
        return dishes.get(id);
    }

    public List<Dish> findByCafeteriaId(final Integer cafeteriaId) {
        List<Dish> cafeteriaDishes = new ArrayList<>();
        for (Map.Entry<Integer, Dish> dish : dishes.entrySet()) {
            if (dish.getValue().getCafeteriaId() == cafeteriaId)
                cafeteriaDishes.add(dish.getValue());
        }
        return cafeteriaDishes;
    }

    public Dish update(final Dish dish) {
        dishes.put(dish.getId(), dish);
        return dish;
    }

    public void remove(final Integer id) {
        dishes.remove(id);
    }

    public Dish add(final Dish dish) {
        final Dish newDish = new Dish(++currentDishId, dish.getCafeteriaId(), dish.getName(), dish.getPrice());
        dishes.put(newDish.getId(), newDish);
        return newDish;
    }
}
