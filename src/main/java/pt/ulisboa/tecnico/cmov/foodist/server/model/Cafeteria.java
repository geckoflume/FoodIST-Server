package pt.ulisboa.tecnico.cmov.foodist.server.model;

public class Cafeteria {
    private final Integer id;

    public Cafeteria(final Integer id) {
        this.id = id;
    }

    public int getId() {
        return id;
    }

    @Override
    public String toString() {
        return "Cafeteria [id=" + id + "]";
    }
}
