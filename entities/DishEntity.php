<?php

class DishEntity extends BaseEntity
{
    public $cafeteria_id;
    public $name;
    public $price;
    public $have_info;
    public $meat;
    public $fish;
    public $vegetarian;
    public $vegan;
    public $dietary_data;

    public function __construct()
    {
        parent::__construct();
        $this->table_name = "dishes";
    }

    public function fetchAllByCafeteria($cafeteria_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cafeteria_id = :cafeteria_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $cafeteria_id);

        return $stmt;
    }

    public function insertDish()
    {
        $query = "INSERT INTO " . $this->table_name . "(cafeteria_id, name, price, have_info, meat, fish, vegetarian, vegan, dietary_data) VALUES(:cafeteria_id, :name, :price, :have_info, :meat, :fish, :vegetarian, :vegan, :dietary_data)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $this->cafeteria_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":have_info", $this->have_info, PDO::PARAM_BOOL);
        $stmt->bindParam(":meat", $this->meat, PDO::PARAM_BOOL);
        $stmt->bindParam(":fish", $this->fish, PDO::PARAM_BOOL);
        $stmt->bindParam(":vegetarian", $this->vegetarian, PDO::PARAM_BOOL);
        $stmt->bindParam(":vegan", $this->vegan, PDO::PARAM_BOOL);
        $stmt->bindParam(":dietary_data", $this->dietary_data);
        return $stmt;
    }

    public function updateDish()
    {
        $query = "UPDATE " . $this->table_name . " SET cafeteria_id = :cafeteria_id, name = :name, price = :price, have_info = :have_info, meat = :, fish = :meat, vegetarian = :, vegan = :vegetarian, dietary_data = :dietary_data WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":cafeteria_id", $this->cafeteria_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":have_info", $this->have_info);
        $stmt->bindParam(":meat", $this->meat);
        $stmt->bindParam(":fish", $this->fish);
        $stmt->bindParam(":vegetarian", $this->vegetarian);
        $stmt->bindParam(":vegan", $this->vegan);
        $stmt->bindParam(":dietary_data", $this->dietary_data);

        return $stmt;
    }
}
