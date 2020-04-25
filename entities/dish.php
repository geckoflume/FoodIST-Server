<?php

class Dish
{
    private $conn;
    private $table_name = "dishes";

    public $id;
    public $cafeteria_id;
    public $name;
    public $price;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    function fetchAll()
    {
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function fetchAllByCafeteria($cafeteria_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cafeteria_id = :cafeteria_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $cafeteria_id);

        $stmt->execute();

        return $stmt;
    }

    function insertDish()
    {
        $query = "INSERT INTO " . $this->table_name . "(cafeteria_id, name, price) VALUES(:cafeteria_id, :name, :price)";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->cafeteria_id = htmlspecialchars(strip_tags($this->cafeteria_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));

        $stmt->bindParam(":cafeteria_id", $this->cafeteria_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);

        if ($stmt->execute()) {
            return array(
                "id" => $this->conn->lastInsertId(),
                "cafeteria_id" => $this->cafeteria_id,
                "name" => $this->name,
                "price" => $this->price
            );
        } else {
            return false;
        }
    }

    function fetch($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt;
    }

    function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt;
    }
}
