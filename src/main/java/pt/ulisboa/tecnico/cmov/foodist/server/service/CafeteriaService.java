package pt.ulisboa.tecnico.cmov.foodist.server.service;

import pt.ulisboa.tecnico.cmov.foodist.server.model.Cafeteria;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class CafeteriaService {
    private final Map<Integer, Cafeteria> cafeterias = new HashMap<>();
    private Integer currentCafeteriaId = 0;

    public CafeteriaService() {
        super();
        initCafeterias();
    }

    private void initCafeterias() {
        for (int i = 0; i < 15; i++) {
            add(new Cafeteria(null));
        }
    }

    public List<Cafeteria> findAll() {
        return new ArrayList<>(cafeterias.values());
    }

    public Cafeteria findById(final Integer id) {
        return cafeterias.get(id);
    }

    public Cafeteria add(final Cafeteria cafeteria) {
        final Cafeteria newCafeteria = new Cafeteria(++currentCafeteriaId);
        cafeterias.put(newCafeteria.getId(), newCafeteria);
        return newCafeteria;
    }
}
