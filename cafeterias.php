<?php

use Symfony\Component\HttpFoundation\JsonResponse;


include_once 'config/database.php';
include_once 'entities/cafeteria.php';

function getCafeterias()
{
    $cafeteria = new Cafeteria();

    // query cafeterias
    $stmt = $cafeteria->fetchAll();
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num > 0) {
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
    $cafeteria = new Cafeteria();

    // query cafeterias
    $stmt = $cafeteria->fetch($id);
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new JsonResponse($row, 200);
    } else {
        return new JsonResponse(array("message" => "No cafeteria found."), 404);
    }
}
