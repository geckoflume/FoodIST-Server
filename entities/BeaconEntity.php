<?php

class BeaconEntity extends BaseEntity
{
    public $cafeteria_id;
    public $datetime_arrive;
    public $datetime_leave;
    public $duration;

    public function __construct()
    {
        parent::__construct();
        $this->table_name = "beacons";
    }

    function fetchAllByCafeteria($cafeteria_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cafeteria_id = :cafeteria_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $cafeteria_id);

        return $stmt;
    }

    function insertBeacon()
    {
        $query = "INSERT INTO " . $this->table_name . "(cafeteria_id, datetime_arrive) VALUES(:cafeteria_id, :datetime_arrive)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $this->cafeteria_id);
        $stmt->bindParam(":datetime_arrive", $this->datetime_arrive);

        return $stmt;
    }

    function updateBeacon()
    {
        $query = "UPDATE " . $this->table_name . " SET datetime_leave = :datetime_leave, duration = :duration WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":datetime_leave", $this->datetime_leave);
        $stmt->bindParam(":duration", $this->duration);

        return $stmt;
    }
}
