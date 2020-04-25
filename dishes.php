<?php

use Symfony\Component\HttpFoundation\JsonResponse;

require 'entities/DishEntity.php';

function getDishes()
{
    $dish = new DishEntity();

    // query dishes
    $stmt = $dish->fetchAll();
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
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

function getDishesByCafeteria($id)
{
    $dish = new DishEntity();

    // query dishes
    $stmt = $dish->fetchAllByCafeteria($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $dishes_arr = array();

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
        return new JsonResponse(array("message" => "No dishes found for this cafeteria."), 404);
    }
}

function getDish($id)
{
    $dish = new DishEntity();

    // query dishes
    $stmt = $dish->fetch($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new JsonResponse($row, 200);
    } else {
        return new JsonResponse(array("message" => "No dish found."), 404);
    }
}

function postDish($data)
{
    $dish = new DishEntity();

    if (!empty($data["cafeteria_id"]) && !empty($data["name"]) && !empty($data["price"])) {
        $dish->cafeteria_id = $data["cafeteria_id"];
        $dish->name = $data["name"];
        $dish->price = $data["price"];

        $stmt = $dish->insertDish();
        $stmt->execute();

        if ($stmt->execute()) {
            $dish = array(
                "id" => $dish->conn->lastInsertId(),
                "cafeteria_id" => $dish->cafeteria_id,
                "name" => $dish->name,
                "price" => $dish->price
            );
            $r = new JsonResponse($dish, 201);
            $r->setEncodingOptions(JSON_NUMERIC_CHECK);
            return $r;
        } // if unable to create the dish
        else {
            return new JsonResponse(array("message" => "Unable to create dish. Please check that this cafeteria exists."), 503);
        }
    } else {
        return new JsonResponse(array("message" => "Unable to create dish. Data is incomplete."), 400);
    }
}

function updateDish($data, $id)
{
    $dish = new DishEntity();

    if (!empty($id) && (!empty($data["cafeteria_id"]) || !empty($data["name"]) || !empty($data["price"]))) {
        $dish->id = $id;
        $dish->cafeteria_id = $data["cafeteria_id"];
        $dish->name = $data["name"];
        $dish->price = $data["price"];

        $stmt = $dish->updateDish();
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $dish = array(
                "id" => $dish->id,
                "cafeteria_id" => $dish->cafeteria_id,
                "name" => $dish->name,
                "price" => $dish->price
            );
            $r = new JsonResponse($dish, 200);
            $r->setEncodingOptions(JSON_NUMERIC_CHECK);
            return $r;
        } else {
            return new JsonResponse(array("message" => "No dish found. This dish was not updated."), 404);
        }
    }
}

function deleteDish($id)
{
    $picture = new PictureEntity();
    $picture_stmt = $picture->deleteAllByDishId($id); // to avoid the foreign key constraint fail
    $picture_stmt->execute();

    $dish = new DishEntity();

    $stmt = $dish->delete($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() == 1) {
        return new JsonResponse(array("message" => "Dish deleted."), 200);
    } else {
        return new JsonResponse(array("message" => "No dish found. This dish was not deleted."), 404);
    }
}
