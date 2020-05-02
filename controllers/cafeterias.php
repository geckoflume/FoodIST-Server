<?php

use Phpml\Regression\LeastSquares;

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

function wrapValueInArray(&$item)
{
    $item = array($item);
}

function computeWaitTime($cafeteria_id)
{
    // Fetch all the completed (which are no longer active) beacons rows
    $beacon = new BeaconEntity();

    $stmt = $beacon->fetchAllByCafeteriaCompleted($cafeteria_id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $dataset = $stmt->fetchAll();
        $samples = array_column($dataset, 'count_in_queue');
        $targets = array_column($dataset, 'duration');

        // In the particular case where we only have rows whose count_in_queue values are the same,
        // we cannot compute a regression line. Then, we return the mean value for the durations.
        if (count(array_unique($samples)) > 1) {
            array_walk($samples, 'wrapValueInArray');

            // Compute the linear regression using the least squares method
            $regression = new LeastSquares();
            $regression->train($samples, $targets);

            // Fetch the number of users in queue (now)
            $stmtInQueue = $beacon->fetchAllByCafeteriaInQueue($cafeteria_id);
            $stmtInQueue->execute();
            $count_in_queue = $stmtInQueue->rowCount();

            // Return the actual wait time, predicted from the linear regression
            $ret = $regression->predict(array($count_in_queue));
        } else {
            $ret = array_sum($targets) / count($targets);
        }
        return intval(ceil($ret));
    } else {
        return 0;
    }
}
