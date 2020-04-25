<?php

class Cafeteria
{
    private $conn;
    private $table_name = "cafeterias";

    public $id;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }
}
