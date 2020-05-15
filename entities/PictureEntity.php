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

    public function fetchAllFirst($pictures_count)
    {
        // See https://stackoverflow.com/a/33767662
        $query = "SELECT id, dish_id, filename FROM (SELECT *, @rn := IF(@cat = dish_id, @rn+1, IF(@cat := dish_id, 1, 1)) AS rn FROM " . $this->table_name . " CROSS JOIN (SELECT @rn := 0, @cat := '') AS vars ORDER BY id) AS t WHERE rn <= :pictures_count";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":pictures_count", $pictures_count);

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