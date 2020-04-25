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

    function fetchAllByDish($dish_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE dish_id = :dish_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dish_id", $dish_id);

        $stmt->execute();

        return $stmt;
    }

    function insertPicture()
    {
        $query = "INSERT INTO " . $this->table_name . "(dish_id, filename) VALUES(:dish_id, :filename)";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->dish_id = htmlspecialchars(strip_tags($this->dish_id));
        $this->filename = htmlspecialchars(strip_tags($this->filename));

        $stmt->bindParam(":dish_id", $this->dish_id);
        $stmt->bindParam(":filename", $this->filename);

        if ($stmt->execute()) {
            return array(
                "id" => $this->conn->lastInsertId(),
                "dish_id" => $this->dish_id,
                "filename" => $this->filename
            );
        } else {
            return false;
        }
    }
}