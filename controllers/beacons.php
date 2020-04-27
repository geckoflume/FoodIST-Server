<?php

require 'entities/BeaconEntity.php';

function getBeacons()
{
    $beacon = new BeaconEntity();

    // query beacons
    $stmt = $beacon->fetchAll();
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $beacons_arr = array();

        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // this will make $row['name'] to just $name only
            /**
             * @var int $id
             * @var int $cafeteria_id
             * @var string $datetime_arrive
             * @var string $datetime_leave
             * @var int $duration
             */
            extract($row);

            $beacon_item = array(
                "id" => $id,
                "cafeteria_id" => $cafeteria_id,
                "datetime_arrive" => $datetime_arrive,
                "datetime_leave" => $datetime_leave,
                "duration" => $duration
            );

            array_push($beacons_arr, $beacon_item);
        }
        return new MyJsonResponse($beacons_arr, 200);
    } else {
        return new MyJsonResponse(array("message" => "No beacons found."), 404);
    }
}

function getBeaconsByCafeteria($id)
{
    $beacon = new BeaconEntity();

    // query beacons
    $stmt = $beacon->fetchAllByCafeteria($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $beacons_arr = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // this will make $row['name'] to just $name only
            /**
             * @var int $id
             * @var int $cafeteria_id
             * @var string $datetime_arrive
             * @var string $datetime_leave
             * @var int $duration
             */
            extract($row);

            $beacon_item = array(
                "id" => $id,
                "cafeteria_id" => $cafeteria_id,
                "datetime_arrive" => $datetime_arrive,
                "datetime_leave" => $datetime_leave,
                "duration" => $duration
            );

            array_push($beacons_arr, $beacon_item);
        }
        return new MyJsonResponse($beacons_arr, 200);
    } else {
        return new MyJsonResponse(array("message" => "No beacons found for this cafeteria."), 404);
    }
}

function getBeacon($id)
{
    $beacon = new BeaconEntity();

    // query beacons
    $stmt = $beacon->fetch($id);
    $stmt->execute();

    // check if 1 record found
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new MyJsonResponse($row, 200);
    } else {
        return new MyJsonResponse(array("message" => "No beacon found."), 404);
    }
}

function postBeacon($data)
{
    $beacon = new BeaconEntity();

    if (!empty($data["cafeteria_id"]) && !empty($data["datetime_arrive"])) {
        $beacon->cafeteria_id = $data["cafeteria_id"];
        $beacon->datetime_arrive = $data["datetime_arrive"];

        $stmtInQueue = $beacon->fetchAllByCafeteriaInQueue($beacon->cafeteria_id);
        $stmtInQueue->execute();
        $beacon->count_in_queue = $stmtInQueue->rowCount();

        $stmt = $beacon->insertBeacon();

        if ($stmt->execute()) {
            $beacon = array(
                "id" => $beacon->conn->lastInsertId(),
                "cafeteria_id" => $beacon->cafeteria_id,
                "datetime_arrive" => $beacon->datetime_arrive,
                "datetime_leave" => $beacon->datetime_leave,
                "duration" => $beacon->duration,
                "count_in_queue" => $beacon->count_in_queue
            );
            return new MyJsonResponse($beacon, 201);
        } // if unable to create the beacon
        else {
            return new MyJsonResponse(array("message" => "Unable to create beacon. Please check that this cafeteria exists."), 503);
        }
    } else {
        return new MyJsonResponse(array("message" => "Unable to create beacon. Data is incomplete."), 400);
    }
}

function updateBeacon($data, $id)
{
    $beacon = new BeaconEntity();

    if (!empty($id) && !empty($data["datetime_leave"])) {
        // Need to fetch all fields to populate the BeaconEntity
        $stmt = $beacon->fetch($id);
        $stmt->execute();

        // check if 1 record found
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            /**
             * @var int $id
             * @var int $cafeteria_id
             * @var string $datetime_arrive
             * @var string $datetime_leave
             * @var int $duration
             * @var int $count_in_queue
             */
            extract($row);

            try {
                $timestamp_arrive = (new DateTime($datetime_arrive))->getTimestamp();
                $timestamp_leave = (new DateTime($data["datetime_leave"]))->getTimestamp();
                $beacon->id = $id;
                $beacon->datetime_leave = $data["datetime_leave"];
                $beacon->duration = $timestamp_leave - $timestamp_arrive;

                $stmt = $beacon->updateBeacon();
                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    $beacon = array(
                        "id" => $beacon->id,
                        "cafeteria_id" => $cafeteria_id,
                        "datetime_arrive" => $datetime_arrive,
                        "datetime_leave" => $beacon->datetime_leave,
                        "duration" => $beacon->duration,
                        "count_in_queue" => $count_in_queue
                    );
                    return new MyJsonResponse($beacon, 200);
                } else {
                    // just in case, but should never happen
                    return new MyJsonResponse(array("message" => "No beacon found. This beacon was not updated."), 404);
                }
            } catch (Exception $e) {
                return new MyJsonResponse(array("message" => "Server error."), 500);
            }

        } else {
            return new MyJsonResponse(array("message" => "No beacon found. This beacon was not updated."), 404);
        }
    } else {
        return new MyJsonResponse(array("message" => "Unable to update beacon. Data is incomplete."), 400);
    }
}
/*
function deleteBeacon($id)
{
    $beacon = new BeaconEntity();

    $stmt = $beacon->delete($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() == 1) {
        return new MyJsonResponse(array("message" => "Beacon deleted."), 200);
    } else {
        return new MyJsonResponse(array("message" => "No beacon found. This beacon was not deleted."), 404);
    }
}
*/