<?php

use Symfony\Component\HttpFoundation\JsonResponse;

include_once 'entities/CafeteriaEntity.php';

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
                "id" => $id
            );

            array_push($cafeterias_arr, $cafeteria_item);
        }
        return new JsonResponse($cafeterias_arr, 200);
    } else {
        return new JsonResponse(array("message" => "No cafeterias found."), 404);
    }
}

function getCafeteria($id)
{
    $cafeteria = new CafeteriaEntity();

    // query cafeterias
    $stmt = $cafeteria->fetch($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new JsonResponse($row, 200);
    } else {
        return new JsonResponse(array("message" => "No cafeteria found."), 404);
    }
}
