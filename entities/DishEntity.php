<?php

class DishEntity extends BaseEntity
{
    public $cafeteria_id;
    public $name;
    public $price;

    public function __construct()
    {
        parent::__construct();
        $this->table_name = "dishes";
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
        $this->name = htmlspecialchars(strip_tags($this->name));

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

    function updateDish() {
        $query = "UPDATE " . $this->table_name . " SET cafeteria_id = :cafeteria_id, name = :name, price = :price WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":cafeteria_id", $this->cafeteria_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);

        $stmt->execute();

        return $stmt;
    }
}
