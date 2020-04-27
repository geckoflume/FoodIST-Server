<?php

class BeaconEntity extends BaseEntity
{
    public $cafeteria_id;
    public $datetime_arrive;
    public $datetime_leave;
    public $duration;
    public $count_in_queue;
    private $lastBeaconsNumberForAvg = 10;

    public function __construct()
    {
        parent::__construct();
        $this->table_name = "beacons";
    }

    public function fetchAllByCafeteria($cafeteria_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cafeteria_id = :cafeteria_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $cafeteria_id);

        return $stmt;
    }

    public function fetchAllByCafeteriaInQueue($cafeteria_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cafeteria_id = :cafeteria_id AND datetime_leave IS NULL";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $cafeteria_id);

        return $stmt;
    }

    public function insertBeacon()
    {
        $query = "INSERT INTO " . $this->table_name . "(cafeteria_id, datetime_arrive, count_in_queue) VALUES(:cafeteria_id, :datetime_arrive, :count_in_queue)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $this->cafeteria_id);
        $stmt->bindParam(":datetime_arrive", $this->datetime_arrive);
        $stmt->bindParam(":count_in_queue", $this->count_in_queue);

        return $stmt;
    }

    public function updateBeacon()
    {
        $query = "UPDATE " . $this->table_name . " SET datetime_leave = :datetime_leave, duration = :duration WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":datetime_leave", $this->datetime_leave);
        $stmt->bindParam(":duration", $this->duration);

        return $stmt;
    }

    public function averageData($cafeteria_id)
    {
        $query = "SELECT AVG(duration) as avg_duration, AVG(count_in_queue) as avg_count_in_queue
                    FROM (
                        SELECT duration, count_in_queue 
                        FROM " . $this->table_name . " 
                        WHERE cafeteria_id = :cafeteria_id AND datetime_leave IS NOT NULL 
                        ORDER BY datetime_leave DESC, id DESC LIMIT :lastBeaconsNumberForAvg
                        ) lastBeacons";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cafeteria_id", $cafeteria_id);
        $stmt->bindParam(":lastBeaconsNumberForAvg", $this->lastBeaconsNumberForAvg, PDO::PARAM_INT);

        return $stmt;
    }
}
