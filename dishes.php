<?php

use Symfony\Component\HttpFoundation\JsonResponse;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config/database.php';
include_once 'entities/dish.php';

function getDishes()
{
    $dish = new Dish();

    // query dishes
    $stmt = $dish->fetchAll();
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num > 0) {
        $dishes_arr = array();

        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // this will make $row['name'] to just $name only
            /**
             * @var int $id
             * @var int $cafeteria_id
             * @var string $name
             * @var double $price
             */
            extract($row);

            $dish_item = array(
                "id" => $id,
                "cafeteria_id" => $cafeteria_id,
                "name" => $name,
                "price" => $price
            );

            array_push($dishes_arr, $dish_item);
        }

        return new JsonResponse($dishes_arr, 200);
    } else {
        return new JsonResponse(array("message" => "No dishes found."), 404);
    }
}

function getDish($id)
{
    $dish = new Dish();

    // query dishes
    $stmt = $dish->fetch($id);
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new JsonResponse($row, 200);
    } else {
        return new JsonResponse(array("message" => "No dish found."), 404);
    }
}

function postDishes($data)
{
    $dish = new Dish();

    if (!empty($data["cafeteria_id"]) && !empty($data["name"]) && !empty($data["price"])) {
        $dish->cafeteria_id = $data["cafeteria_id"];
        $dish->name = $data["name"];
        $dish->price = $data["price"];

        // create the dish
        $dish = $dish->insertDish();
        if ($dish) {
            return new JsonResponse($dish, 201);;
        } // if unable to create the dish
        else {
            return new JsonResponse(array("message" => "Unable to create dish. Please check that this cafeteria exists."), 503);
        }
    } else {
        return new JsonResponse(array("message" => "Unable to create dish. Data is incomplete."), 400);
    }
}

function deleteDish($id)
{
    $dish = new Dish();

    // query dishes
    $stmt = $dish->delete($id);
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num == 1) {
        return new JsonResponse(array("message" => "Dish deleted."), 200);
    } else {
        return new JsonResponse(array("message" => "No dish found. This dish was not deleted."), 404);
    }
}
