<?php

class PictureEntity extends BaseEntity
{
    public $dish_id;
    public $filename;

    public function __construct()
    {
        parent::__construct();
        $this->table_name = "pictures";
    }

    public function fetchAllByDish($dish_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE dish_id = :dish_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dish_id", $dish_id);
        
        return $stmt;
    }

    public function insertPicture()
    {
        $query = "INSERT INTO " . $this->table_name . "(dish_id, filename) VALUES(:dish_id, :filename)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dish_id", $this->dish_id);
        $stmt->bindParam(":filename", $this->filename);

        return $stmt;
    }

    public function deleteAllByDishId($dish_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE dish_id = :dish_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":dish_id", $dish_id);

        return $stmt;
    }
}