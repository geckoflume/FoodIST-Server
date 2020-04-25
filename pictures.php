<?php

use Symfony\Component\HttpFoundation\JsonResponse;

include_once 'entities/PictureEntity.php';

function getPictures()
{
    $picture = new PictureEntity();

    // query pictures
    $stmt = $picture->fetchAll();
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num > 0) {
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
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num > 0) {
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
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num > 0) {
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
        $picture = $picture->insertPicture();
        if ($picture) {
            return new JsonResponse($picture, 201);;
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
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if ($num == 1) {
        return new JsonResponse(array("message" => "Picture deleted."), 200);
    } else {
        return new JsonResponse(array("message" => "No picture found. This picture was not deleted."), 404);
    }
}
