<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/vendor/autoload.php';
require_once 'dishes.php';
require_once 'cafeterias.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->get('/api/', function () {
    return file_get_contents('index.html');
});

$app->get('/api/dishes', function () {
    return getDishes();
});

$app->get('/api/dishes/{id}', function ($id) use ($app) {
    return getDish($id);
})->assert('id', '\d+');

$app->delete('/api/dishes/{id}', function ($id) use ($app) {
    return deleteDish($id);
})->assert('id', '\d+');

$app->post('/api/dishes', function (Request $request) use ($app) {
    $data = $request->request->all();
    return postDishes($data);
})->assert('id', '\d+');

$app->get('/api/cafeterias', function () {
    return getCafeterias();
});

$app->get('/api/cafeterias/{id}', function ($id) use ($app) {
    return getCafeteria($id);
})->assert('id', '\d+');

$app->get('/api/cafeterias/{id}/dishes', function ($id) use ($app) {
    return getDishesByCafeteria($id);
})->assert('id', '\d+');

$app->run();