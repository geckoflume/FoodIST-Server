<?php

use Symfony\Component\HttpFoundation\JsonResponse;

include_once 'entities/PictureEntity.php';

function getPictures()
{
    $picture = new PictureEntity();

    // query pictures
    $stmt = $picture->fetchAll();
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $pictures_arr = array();

        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // this will make $row['name'] to just $name only
            /**
             * @var int $id
             * @var int $dish_id
             * @var string $filename
             */
            extract($row);

            $picture_item = array(
                "id" => $id,
                "dish_id" => $dish_id,
                "filename" => $filename
            );

            array_push($pictures_arr, $picture_item);
        }
        return new JsonResponse($pictures_arr, 200);
    } else {
        return new JsonResponse(array("message" => "No pictures found."), 404);
    }
}

function getPicturesByDish($id) {
    $picture = new PictureEntity();

    // query pictures
    $stmt = $picture->fetchAllByDish($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $pictures_arr = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // this will make $row['name'] to just $name only
            /**
             * @var int $id
             * @var int $dish_id
             * @var string $filename
             */
            extract($row);

            $picture_item = array(
                "id" => $id,
                "dish_id" => $dish_id,
                "filename" => $filename
            );

            array_push($pictures_arr, $picture_item);
        }
        return new JsonResponse($pictures_arr, 200);
    } else {
        return new JsonResponse(array("message" => "No pictures found for this dish."), 404);
    }
}

function getPicture($id)
{
    $picture = new PictureEntity();

    // query pictures
    $stmt = $picture->fetch($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new JsonResponse($row, 200);
    } else {
        return new JsonResponse(array("message" => "No picture found."), 404);
    }
}

function postPicture($data)
{
    $picture = new PictureEntity();

    if (!empty($data["dish_id"]) && !empty($data["filename"])) {
        $picture->dish_id = $data["dish_id"];
        $picture->filename = $data["filename"];

        // create the picture
        $stmt = $picture->insertPicture();
        $stmt->execute();

        if ($stmt->execute()) {
            $dish = array(
                "id" => $picture->conn->lastInsertId(),
                "cafeteria_id" => $picture->dish_id,
                "name" => $picture->filename
            );
            $r = new JsonResponse($dish, 201);
            $r->setEncodingOptions(JSON_NUMERIC_CHECK);
            return $r;
        } // if unable to create the picture
        else {
            return new JsonResponse(array("message" => "Unable to create picture. Please check that this cafeteria exists."), 503);
        }
    } else {
        return new JsonResponse(array("message" => "Unable to create picture. Data is incomplete."), 400);
    }
}

function deletePicture($id)
{
    $picture = new PictureEntity();

    // query pictures
    $stmt = $picture->delete($id);
    $stmt->execute();

    // check if more than 0 record found
    if ($stmt->rowCount() == 1) {
        return new JsonResponse(array("message" => "Picture deleted."), 200);
    } else {
        return new JsonResponse(array("message" => "No picture found. This picture was not deleted."), 404);
    }
}
