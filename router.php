<?php

use Symfony\Component\HttpFoundation\Request;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/utils/Database.php');
require_once(__DIR__ . '/utils/PictureUploader.php');
require_once(__DIR__ . '/utils/MyJsonResponse.php');
require_once(__DIR__ . '/entities/BaseEntity.php');
require_once(__DIR__ . '/controllers/cafeterias.php');
require_once(__DIR__ . '/controllers/beacons.php');
require_once(__DIR__ . '/controllers/dishes.php');
require_once(__DIR__ . '/controllers/pictures.php');

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
$app->get('/', function () {
    include 'index.php';
    return "";
});

/**
 * Cafeterias
 */
$app->get('/cafeterias', function () {
    return getCafeterias();
});

$app->get('/cafeterias/{id}', function ($id) use ($app) {
    return getCafeteria($id);
})->assert('id', '\d+');

$app->get('/cafeterias/{id}/beacons', function ($id) use ($app) {
    return getBeaconsByCafeteria($id);
})->assert('id', '\d+');

$app->get('/cafeterias/{id}/dishes', function ($id) use ($app) {
    return getDishesByCafeteria($id);
})->assert('id', '\d+');


/**
 * Beacons
 */
$app->get('/beacons', function () {
    return getBeacons();
});

$app->get('/beacons/{id}', function ($id) use ($app) {
    return getBeacon($id);
})->assert('id', '\d+');

$app->put('/beacons/{id}', function (Request $request, $id) use ($app) {
    $data = $request->request->all();
    return updateBeacon($data, $id);
})->assert('id', '\d+');
/*
$app->delete('/beacons/{id}', function ($id) use ($app) {
    return deleteBeacon($id);
})->assert('id', '\d+');
*/
$app->post('/beacons', function (Request $request) use ($app) {
    $data = $request->request->all();
    return postBeacon($data);
});


/**
 * Dishes
 */
$app->get('/dishes', function () {
    return getDishes();
});

$app->get('/dishes/{id}', function ($id) use ($app) {
    return getDish($id);
})->assert('id', '\d+');

$app->put('/dishes/{id}', function (Request $request, $id) use ($app) {
    $data = $request->request->all();
    return updateDish($data, $id);
})->assert('id', '\d+');

$app->delete('/dishes/{id}', function ($id) use ($app) {
    return deleteDish($id);
})->assert('id', '\d+');

$app->post('/dishes', function (Request $request) use ($app) {
    $data = $request->request->all();
    return postDish($data);
});


/**
 * Pictures
 */
$app->get('/pictures', function () {
    return getPictures();
});

$app->get('/pictures/first', function () {
    return getFirstPictures();
});

$app->get('/pictures/{id}', function ($id) use ($app) {
    return getPicture($id);
})->assert('id', '\d+');

$app->delete('/pictures/{id}', function ($id) use ($app) {
    return deletePicture($id);
})->assert('id', '\d+');

$app->post('/pictures', function (Request $request) use ($app) {
    $data = $request->request->all();
    $uploadedPicture = $request->files->get('picture');

    if ($uploadedPicture == null || $uploadedPicture->getMimeType() != "image/jpeg") {
        return new MyJsonResponse(array("message" => "Unable to create picture. Data is incomplete, please provide a JPEG picture."), 400);
    } else {
        $pu = new PictureUploader($uploadedPicture);
        return postPicture($data, $pu->getNewFilename());
    }
});

$app->get('/dishes/{id}/pictures', function ($id) use ($app) {
    return getPicturesByDish($id);
})->assert('id', '\d+');

$app->run();