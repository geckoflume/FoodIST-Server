<?php

require 'entities/PictureEntity.php';

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
        return new MyJsonResponse($pictures_arr, 200);
    } else {
        return new MyJsonResponse(array("message" => "No pictures found."), 404);
    }
}

function getPicturesByDish($id)
{
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
        return new MyJsonResponse($pictures_arr, 200);
    } else {
        return new MyJsonResponse(array("message" => "No dish found or no pictures found for this dish."), 404);
    }
}

function getPicture($id)
{
    $picture = new PictureEntity();

    // query pictures
    $stmt = $picture->fetch($id);
    $stmt->execute();

    // check if 1 record found
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new MyJsonResponse($row, 200);
    } else {
        return new MyJsonResponse(array("message" => "No picture found."), 404);
    }
}

function postPicture($data, $filename)
{
    $picture = new PictureEntity();

    if (!empty($data["dish_id"])) {
        $picture->dish_id = $data["dish_id"];
        $picture->filename = $filename;

        // create the picture
        $stmt = $picture->insertPicture();

        if ($stmt->execute()) {
            $dish = array(
                "id" => $picture->conn->lastInsertId(),
                "dish_id" => $picture->dish_id,
                "filename" => $picture->filename
            );
            return new MyJsonResponse($dish, 201);
        } // if unable to create the picture
        else {
            return new MyJsonResponse(array("message" => "Unable to create picture. Please check that this dish exists."), 503);
        }
    } else {
        return new MyJsonResponse(array("message" => "Unable to create picture. Data is incomplete."), 400);
    }
}

function deletePicture($id)
{
    $picture = new PictureEntity();

    // query picture
    $stmt = $picture->fetch($id);
    $stmt->execute();

    // check if 1 record found
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $file = PictureUploader::$destination . '/' . $row['filename'];

        // query picture for deletion
        $stmt = $picture->delete($id);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            // delete picture from filesystem
            if (file_exists($file))
                unlink($file);
            return new MyJsonResponse(array("message" => "Picture deleted."), 200);
        } else {
            return new MyJsonResponse(array("message" => "No picture found. This picture was not deleted."), 404);
        }
    } else {
        return new MyJsonResponse(array("message" => "No picture found. This picture was not deleted."), 404);
    }
}
