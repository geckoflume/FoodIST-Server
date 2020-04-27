<?php

abstract class BaseEntity
{
    public $conn;
    protected $table_name = "";

    public $id;

    // constructor with $db as database connection
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function fetchAll()
    {
        $query = "SELECT * FROM " . $this->table_name;

        return $this->conn->prepare($query);
    }


    public function fetch($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        return $stmt;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        return $stmt;
    }
}