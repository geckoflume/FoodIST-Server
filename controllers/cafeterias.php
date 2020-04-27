<?php

require 'entities/CafeteriaEntity.php';

function getCafeterias()
{
    $cafeteria = new CafeteriaEntity();

    // query cafeterias
    $stmt = $cafeteria->fetchAll();
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $cafeterias_arr = array();

        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // this will make $row['name'] to just $name only
            /**
             * @var int $id
             */
            extract($row);

            $cafeteria_item = array(
                "id" => $id,
                "wait_time" => computeWaitTime($id)
            );

            array_push($cafeterias_arr, $cafeteria_item);
        }
        return new MyJsonResponse($cafeterias_arr, 200);
    } else {
        return new MyJsonResponse(array("message" => "No cafeterias found."), 404);
    }
}

function getCafeteria($id)
{
    $cafeteria = new CafeteriaEntity();

    // query cafeterias
    $stmt = $cafeteria->fetch($id);
    $stmt->execute();

    // check if 1 record found
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // this will make $row['name'] to just $name only
        /**
         * @var int $id
         */
        extract($row);
        $cafeteria_item = array(
            "id" => $id,
            "wait_time" => computeWaitTime($id)
        );
        return new MyJsonResponse($cafeteria_item, 200);
    } else {
        return new MyJsonResponse(array("message" => "No cafeteria found."), 404);
    }
}

function computeWaitTime($cafeteria_id)
{
    // Fetch 10 last completed (which are no longer active) beacons rows
    $beacon = new BeaconEntity();

    $stmt = $beacon->averageData($cafeteria_id);
    $stmt->execute();

    // check if 1 record found (should contain avg(duration) and avg(count_in_queue))
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // this will make $row['name'] to just $name only
        /**
         * @var float $avg_duration
         * @var float $avg_count_in_queue
         */
        extract($row);

        // Fetch the number of users in queue (now)
        $stmtInQueue = $beacon->fetchAllByCafeteriaInQueue($cafeteria_id);
        $stmtInQueue->execute();
        $count_in_queue = $stmtInQueue->rowCount();

        // Return the actual wait time ($t = $avg_duration * $count_in_queue / $avg_count_in_queue)
        if ($avg_count_in_queue == 0) {
            return 0;
        } else {
            return $avg_duration * $count_in_queue / $avg_count_in_queue;
        }
    }
    return null; // should never happen
}
