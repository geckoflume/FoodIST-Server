<?php

class Cafeteria
{
    private $conn;
    private $table_name = "cafeterias";

    public $id;

    // constructor with $db as database connection
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

    function fetch($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt;
    }
}
