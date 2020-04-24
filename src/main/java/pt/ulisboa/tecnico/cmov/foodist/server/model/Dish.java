package pt.ulisboa.tecnico.cmov.foodist.server.model;

public class Dish {
    private final Integer id;
    private int cafeteriaId;
    private String name;
    private double price;

    public Dish(final Integer id, int cafeteriaId, String name, double price) {
        this.id = id;
        this.cafeteriaId = cafeteriaId;
        this.name = name;
        this.price = price;
    }

    public int getId() {
        return id;
    }

    public int getCafeteriaId() {
        return cafeteriaId;
    }

    public String getName() {
        return name;
    }

    public double getPrice() {
        return price;
    }

    @Override
    public String toString() {
        return "Dish [id=" + id + ", cafeteriaId=" + cafeteriaId + ", name=" + name + ", price=" + price + "]";
    }
}
