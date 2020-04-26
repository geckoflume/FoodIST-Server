<?php

use Symfony\Component\HttpFoundation\Request;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/utils/Database.php');
require_once(__DIR__ . '/utils/PictureUploader.php');
require_once(__DIR__ . '/utils/MyJsonResponse.php');
require_once(__DIR__ . '/entities/BaseEntity.php');
require_once(__DIR__ . '/dishes.php');
require_once(__DIR__ . '/cafeterias.php');
require_once(__DIR__ . '/pictures.php');

error_reporting(E_ALL);

$app = new Silex\Application();
$app['debug'] = true;

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

/**
 * Index page
 */
$app->get('/api/', function () {
    return file_get_contents('index.html');
});

/**
 * Dishes
 */
$app->get('/api/dishes', function () {
    return getDishes();
});

$app->get('/api/dishes/{id}', function ($id) use ($app) {
    return getDish($id);
})->assert('id', '\d+');

$app->put('/api/dishes/{id}', function (Request $request, $id) use ($app) {
    $data = $request->request->all();
    return updateDish($data, $id);
})->assert('id', '\d+');

$app->delete('/api/dishes/{id}', function ($id) use ($app) {
    return deleteDish($id);
})->assert('id', '\d+');

$app->post('/api/dishes', function (Request $request) use ($app) {
    $data = $request->request->all();
    return postDish($data);
});

/**
 * Cafeterias
 */
$app->get('/api/cafeterias', function () {
    return getCafeterias();
});

$app->get('/api/cafeterias/{id}', function ($id) use ($app) {
    return getCafeteria($id);
})->assert('id', '\d+');

$app->get('/api/cafeterias/{id}/dishes', function ($id) use ($app) {
    return getDishesByCafeteria($id);
})->assert('id', '\d+');


/**
 * Pictures
 */
$app->get('/api/pictures', function () {
    return getPictures();
});

$app->get('/api/pictures/{id}', function ($id) use ($app) {
    return getPicture($id);
})->assert('id', '\d+');

$app->delete('/api/pictures/{id}', function ($id) use ($app) {
    return deletePicture($id);
})->assert('id', '\d+');

$app->post('/api/pictures', function (Request $request) use ($app) {
    $data = $request->request->all();
    $uploadedPicture = $request->files->get('picture');

    if ($uploadedPicture == null || $uploadedPicture->getMimeType() != "image/jpeg") {
        return new MyJsonResponse(array("message" => "Unable to create picture. Data is incomplete, please provide a JPEG picture."), 400);
    } else {
        $pu = new PictureUploader($uploadedPicture);
        return postPicture($data, $pu->getNewFilename());
    }
});

$app->get('/api/dishes/{id}/pictures', function ($id) use ($app) {
    return getPicturesByDish($id);
})->assert('id', '\d+');

$app->run();