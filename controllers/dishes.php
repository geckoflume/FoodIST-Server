<?php

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
             * @var bool $have_info
             * @var bool $meat
             * @var bool $fish
             * @var bool $vegetarian
             * @var bool $vegan
             * @var string $dietary_data
             */
            extract($row);

            $dish_item = array(
                "id" => $id,
                "cafeteria_id" => $cafeteria_id,
                "name" => $name,
                "price" => $price,
                "have_info" => (bool)$have_info,
                "meat" => (bool)$meat,
                "fish" => (bool)$fish,
                "vegetarian" => (bool)$vegetarian,
                "vegan" => (bool)$vegan,
                "dietary_data" => $dietary_data
            );

            array_push($dishes_arr, $dish_item);
        }
        return new MyJsonResponse($dishes_arr, 200);
    } else {
        return new MyJsonResponse(array("message" => "No dishes found."), 404);
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
             * @var bool $have_info
             * @var bool $meat
             * @var bool $fish
             * @var bool $vegetarian
             * @var bool $vegan
             * @var string $dietary_data
             */
            extract($row);

            $dish_item = array(
                "id" => $id,
                "cafeteria_id" => $cafeteria_id,
                "name" => $name,
                "price" => $price,
                "have_info" => (bool)$have_info,
                "meat" => (bool)$meat,
                "fish" => (bool)$fish,
                "vegetarian" => (bool)$vegetarian,
                "vegan" => (bool)$vegan,
                "dietary_data" => $dietary_data
            );

            array_push($dishes_arr, $dish_item);
        }
        return new MyJsonResponse($dishes_arr, 200);
    } else {
        return new MyJsonResponse(array("message" => "No dishes found for this cafeteria."), 404);
    }
}

function getDish($id)
{
    $dish = new DishEntity();

    // query dishes
    $stmt = $dish->fetch($id);
    $stmt->execute();

    // check if 1 record found
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // this will make $row['name'] to just $name only
        /**
         * @var int $id
         * @var int $cafeteria_id
         * @var string $name
         * @var double $price
         * @var bool $have_info
         * @var bool $meat
         * @var bool $fish
         * @var bool $vegetarian
         * @var bool $vegan
         * @var string $dietary_data
         */
        extract($row);
        $dish_item = array(
            "id" => $id,
            "cafeteria_id" => $cafeteria_id,
            "name" => $name,
            "price" => $price,
            "have_info" => (bool)$have_info,
            "meat" => (bool)$meat,
            "fish" => (bool)$fish,
            "vegetarian" => (bool)$vegetarian,
            "vegan" => (bool)$vegan,
            "dietary_data" => $dietary_data
        );
        return new MyJsonResponse($dish_item, 200);
    } else {
        return new MyJsonResponse(array("message" => "No dish found."), 404);
    }
}

function postDish($data)
{
    $dish = new DishEntity();
    if (isset($data["cafeteria_id"]) && isset($data["name"]) && isset($data["price"]) && isset($data["have_info"])
        && isset($data["meat"]) && isset($data["fish"]) && isset($data["vegetarian"]) && isset($data["vegan"]) && isset($data["dietary_data"])) {
        $dish->cafeteria_id = $data["cafeteria_id"];
        $dish->name = $data["name"];
        $dish->price = $data["price"];
        $dish->have_info = $data["have_info"];
        $dish->meat = $data["meat"];
        $dish->fish = $data["fish"];
        $dish->vegetarian = $data["vegetarian"];
        $dish->vegan = $data["vegan"];
        $dish->dietary_data = $data["dietary_data"];


        $stmt = $dish->insertDish();

        if ($stmt->execute()) {
            $dish = array(
                "id" => $dish->conn->lastInsertId(),
                "cafeteria_id" => $dish->cafeteria_id,
                "name" => $dish->name,
                "price" => $dish->price,
                "have_info" => (bool)$dish->have_info,
                "meat" => (bool)$dish->meat,
                "fish" => (bool)$dish->fish,
                "vegetarian" => (bool)$dish->vegetarian,
                "vegan" => (bool)$dish->vegan,
                "dietary_data" => $dish->dietary_data
            );
            return new MyJsonResponse($dish, 201);
        } // if unable to create the dish
        else {
            return new MyJsonResponse(array("message" => "Unable to create dish. Please check that this cafeteria exists."), 503);
        }
    } else {
        return new MyJsonResponse(array("message" => "Unable to create dish. Data is incomplete."), 400);
    }
}

function updateDish($data, $id)
{
    $dish = new DishEntity();

    if (isset($id) && ((isset($data["cafeteria_id"]) || isset($data["name"]) || isset($data["price"]) || isset($data["have_info"])
            || isset($data["meat"]) || isset($data["fish"]) || isset($data["vegetarian"]) || isset($data["vegan"]) || isset($data["dietary_data"])))) {
        $dish->id = $id;
        $dish->cafeteria_id = $data["cafeteria_id"];
        $dish->name = $data["name"];
        $dish->price = $data["price"];
        $dish->have_info = $data["have_info"];
        $dish->meat = $data["meat"];
        $dish->fish = $data["fish"];
        $dish->vegetarian = $data["vegetarian"];
        $dish->vegan = $data["vegan"];
        $dish->dietary_data = $data["dietary_data"];

        $stmt = $dish->updateDish();
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $dish = array(
                "id" => $dish->id,
                "cafeteria_id" => $dish->cafeteria_id,
                "name" => $dish->name,
                "price" => $dish->price,
                "have_info" => (bool)$dish->have_info,
                "meat" => (bool)$dish->meat,
                "fish" => (bool)$dish->fish,
                "vegetarian" => (bool)$dish->vegetarian,
                "vegan" => (bool)$dish->vegan,
                "dietary_data" => $dish->dietary_data
            );
            return new MyJsonResponse($dish, 200);
        } else {
            return new MyJsonResponse(array("message" => "No dish found. This dish was not updated."), 404);
        }
    } else {
        return new MyJsonResponse(array("message" => "Unable to update dish. Data is incomplete."), 400);
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
        return new MyJsonResponse(array("message" => "Dish deleted."), 200);
    } else {
        return new MyJsonResponse(array("message" => "No dish found. This dish was not deleted."), 404);
    }
}
