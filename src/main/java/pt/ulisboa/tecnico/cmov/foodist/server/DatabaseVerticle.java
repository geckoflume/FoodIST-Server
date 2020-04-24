package pt.ulisboa.tecnico.cmov.foodist.server;

import io.vertx.core.AbstractVerticle;
import io.vertx.core.Promise;
import io.vertx.core.eventbus.Message;
import io.vertx.core.json.JsonArray;
import io.vertx.core.json.JsonObject;
import io.vertx.ext.jdbc.JDBCClient;
import io.vertx.ext.sql.ResultSet;
import io.vertx.ext.sql.SQLConnection;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.HashMap;
import java.util.List;
import java.util.Properties;
import java.util.stream.Collectors;

public class DatabaseVerticle extends AbstractVerticle {
    public static final String CONFIG_DB_JDBC_URL = "db.jdbc.url";
    public static final String CONFIG_DB_JDBC_DRIVER_CLASS = "db.jdbc.driver_class";
    public static final String CONFIG_DB_JDBC_MAX_POOL_SIZE = "db.jdbc.max_pool_size";
    public static final String CONFIG_DB_SQL_QUERIES_RESOURCE_FILE = "db.sqlqueries.resource.file";

    public static final String CONFIG_DB_QUEUE = "db.queue";

    private static final Logger LOGGER = LoggerFactory.getLogger(DatabaseVerticle.class);

    private enum SqlQuery {
        CREATE_DISHES_TABLE,
        ALL_DISHES,
        GET_DISH,
        CREATE_DISH,
        UPDATE_DISH,
        DELETE_DISH
    }

    private final HashMap<SqlQuery, String> sqlQueries = new HashMap<>();

    private void loadSqlQueries() throws IOException {
        String queriesFile = config().getString(CONFIG_DB_SQL_QUERIES_RESOURCE_FILE);
        InputStream queriesInputStream;
        if (queriesFile != null) {
            queriesInputStream = new FileInputStream(queriesFile);
        } else {
            queriesInputStream = getClass().getResourceAsStream("/db-queries.properties");
        }

        Properties queriesProps = new Properties();
        queriesProps.load(queriesInputStream);
        queriesInputStream.close();

        sqlQueries.put(SqlQuery.CREATE_DISHES_TABLE, queriesProps.getProperty("create-dishes-table"));
        sqlQueries.put(SqlQuery.ALL_DISHES, queriesProps.getProperty("all-dishes"));
        sqlQueries.put(SqlQuery.GET_DISH, queriesProps.getProperty("get-dish"));
        sqlQueries.put(SqlQuery.CREATE_DISH, queriesProps.getProperty("create-dish"));
        sqlQueries.put(SqlQuery.UPDATE_DISH, queriesProps.getProperty("update-dish"));
        sqlQueries.put(SqlQuery.DELETE_DISH, queriesProps.getProperty("delete-dish"));
    }

    private JDBCClient dbClient;

    @Override
    public void start(Promise<Void> promise) throws Exception {
        // this uses blocking APIs, but data is small...
        loadSqlQueries();

        dbClient = JDBCClient.createShared(vertx, new JsonObject()
                .put("url", config().getString(CONFIG_DB_JDBC_URL, "jdbc:hsqldb:file:db/foodist"))
                .put("driver_class", config().getString(CONFIG_DB_JDBC_DRIVER_CLASS, "org.hsqldb.jdbcDriver"))
                .put("max_pool_size", config().getInteger(CONFIG_DB_JDBC_MAX_POOL_SIZE, 30)));

        dbClient.getConnection(ar -> {
            if (ar.failed()) {
                LOGGER.error("Could not open a database connection", ar.cause());
                promise.fail(ar.cause());
            } else {
                SQLConnection connection = ar.result();
                connection.execute(sqlQueries.get(SqlQuery.CREATE_DISHES_TABLE), create -> {
                    connection.close();
                    if (create.failed()) {
                        LOGGER.error("Database preparation error", create.cause());
                        promise.fail(create.cause());
                    } else {
                        vertx.eventBus().consumer(config().getString(CONFIG_DB_QUEUE, "DB.queue"), this::onMessage);
                        promise.complete();
                    }
                });
            }
        });
    }

    public enum ErrorCodes {
        NO_ACTION_SPECIFIED,
        BAD_ACTION,
        DB_ERROR
    }

    public void onMessage(Message<JsonObject> message) {
        if (!message.headers().contains("action")) {
            LOGGER.error("No action header specified for message with headers {} and body {}",
                    message.headers(), message.body().encodePrettily());
            message.fail(ErrorCodes.NO_ACTION_SPECIFIED.ordinal(), "No action header specified");
            return;
        }
        String action = message.headers().get("action");

        switch (action) {
            case "all-dishes":
                fetchAllDishes(message);
                break;
            case "get-dish":
                fetchDish(message);
                break;
            case "create-page":
                createDish(message);
                break;
            case "update-dish":
                updateDish(message);
                break;
            case "delete-page":
                deleteDish(message);
                break;
            default:
                message.fail(ErrorCodes.BAD_ACTION.ordinal(), "Bad action: " + action);
        }
    }

    private void fetchAllDishes(Message<JsonObject> message) {
        dbClient.query(sqlQueries.get(SqlQuery.ALL_DISHES), res -> {
            if (res.succeeded()) {
                List<String> pages = res.result()
                        .getResults()
                        .stream()
                        .map(json -> json.getString(0))
                        .sorted()
                        .collect(Collectors.toList());
                message.reply(new JsonObject().put("dishes", new JsonArray(pages)));
            } else {
                reportQueryError(message, res.cause());
            }
        });
    }

    private void fetchDish(Message<JsonObject> message) {
        String requestedPage = message.body().getString("page");
        JsonArray params = new JsonArray().add(requestedPage);

        dbClient.queryWithParams(sqlQueries.get(SqlQuery.GET_DISH), params, fetch -> {
            if (fetch.succeeded()) {
                JsonObject response = new JsonObject();
                ResultSet resultSet = fetch.result();
                if (resultSet.getNumRows() == 0) {
                    response.put("found", false);
                } else {
                    response.put("found", true);
                    JsonArray row = resultSet.getResults().get(0);
                    response.put("id", row.getInteger(0));
                    response.put("rawContent", row.getString(1));
                }
                message.reply(response);
            } else {
                reportQueryError(message, fetch.cause());
            }
        });
    }

    private void createDish(Message<JsonObject> message) {
        JsonObject request = message.body();
        JsonArray data = new JsonArray()
                .add(request.getString("title"))
                .add(request.getString("markdown"));

        dbClient.updateWithParams(sqlQueries.get(SqlQuery.CREATE_DISH), data, res -> {
            if (res.succeeded()) {
                message.reply("ok");
            } else {
                reportQueryError(message, res.cause());
            }
        });
    }

    private void updateDish(Message<JsonObject> message) {
        JsonObject request = message.body();
        JsonArray data = new JsonArray()
                .add(request.getString("markdown"))
                .add(request.getString("id"));

        dbClient.updateWithParams(sqlQueries.get(SqlQuery.UPDATE_DISH), data, res -> {
            if (res.succeeded()) {
                message.reply("ok");
            } else {
                reportQueryError(message, res.cause());
            }
        });
    }

    private void deleteDish(Message<JsonObject> message) {
        JsonArray data = new JsonArray().add(message.body().getString("id"));

        dbClient.updateWithParams(sqlQueries.get(SqlQuery.DELETE_DISH), data, res -> {
            if (res.succeeded()) {
                message.reply("ok");
            } else {
                reportQueryError(message, res.cause());
            }
        });
    }

    private void reportQueryError(Message<JsonObject> message, Throwable cause) {
        LOGGER.error("Database query error", cause);
        message.fail(ErrorCodes.DB_ERROR.ordinal(), cause.getMessage());
    }

}
