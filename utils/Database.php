<?php

class Database
{
    private $host = "localhost";
    private $db_name = "foodist";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";
    private $conn;

    // get the database connection
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset, $this->username, $this->password, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
            $this->conn->exec("set names " . $this->charset);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
