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
    return home();
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

function home()
{
    return '<!DOCTYPE html>
            <html>
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body, table {
                        text-align: center !important;
                        margin: auto !important;
                    }
                </style>
            </head>
            <body>
                <h1>FoodIST REST Server</h1>
                <p>Welcome!</p>
                <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgdmVyc2lvbj0iMS4xIgogICBpZD0ic3ZnMiIKICAgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIKICAgd2lkdGg9IjE3MS4xMTMzMyIKICAgaGVpZ2h0PSIyMDYuOTU0NjciCiAgIHZpZXdCb3g9IjAgMCAxNzEuMTEzMzIgMjA2Ljk1NDY3Ij48bWV0YWRhdGEKICAgICBpZD0ibWV0YWRhdGE4Ij48cmRmOlJERj48Y2M6V29yawogICAgICAgICByZGY6YWJvdXQ9IiI+PGRjOmZvcm1hdD5pbWFnZS9zdmcreG1sPC9kYzpmb3JtYXQ+PGRjOnR5cGUKICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPjxkYzp0aXRsZT48L2RjOnRpdGxlPjwvY2M6V29yaz48L3JkZjpSREY+PC9tZXRhZGF0YT48ZGVmcwogICAgIGlkPSJkZWZzNiIgLz48ZwogICAgIGlkPSJnMTAiCiAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMS4zMzMzMzMzLDAsMCwtMS4zMzMzMzMzLC0xMjQuMTgxNzMsNDk4Ljg2Njc5KSI+PGcKICAgICAgIGlkPSJnMjAiCiAgICAgICB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxOTEuNDY1MywzMjUuMTQyMSkiPjxwYXRoCiAgICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KDAuNzUwMDAwMDIsMCwwLC0wLjc1MDAwMDAyLC05OC4zMjksNDkuMDA4KSIKICAgICAgICAgaWQ9InBhdGgyMiIKICAgICAgICAgZD0ibSAwLDAgdiA4Ni44NTU0NjkgYyAwLDc4LjIwNjY2MSA4NS4zOTY0ODQsMTIwLjA5OTYxMSA4NS4zOTY0ODQsMTIwLjA5OTYxMSAwLDAgODUuNzE2Nzk2LC00MS44OTI5NSA4NS43MTY3OTYsLTEyMC4wOTk2MTEgViAwIFogbSAxMzMuMTM0NzcsMjYuNTQ0OTIyIDIuNTY0NDUsMi41NjQ0NTMgLTMwLjUyNzM0LDI4LjYzODY3MiAzLjMxNDQ1LDMuMzA4NTk0IDI5LjU3NDIyLC0yOS41NzQyMTkgMi43MjA3LDIuNzE0ODQ0IC0yOS41NzQyMiwyOS41NzgxMjUgMi43OTQ5MiwyLjc5ODgyOCAyOS41NzIyNywtMjkuNTc4MTI1IDIuNDcwNywyLjQ1NzAzMSAtMjkuMzk4NDQsMjkuMzk2NDg0IDMuMzcxMSwzLjM4MDg2IDI4LjU2MjUsLTMwLjI0MDIzNSAyLjgwNDY5LDIuODE0NDU0IC0yMy4zNDc2NiwyOS40MjM4MjggYyAwLDAgLTAuNTU4MDYsMC41Njg0NzkgLTAuNzk0OTIsMC43NzczNDMgLTMuMzk1MTMsMi45NjcwMTMgLTcuNjYxODksNC4zNzAzMTMgLTExLjg4ODY3LDQuMjU1ODYgLTAuMDI0MiwwIC0wLjA3MzksMC4wMDQ1IC0wLjA4OTgsMC4wMTE3MiAtOS4zNDUzOSwwLjA5NDQgLTE1LjY3MjE3Myw0Ljg4MzMxOSAtMjAuMDIzNDM2LDkuODY5MTQgMC4xNzY2OTksOC40MjkzNDUgMTAuMzczNjQ2LDEyLjkxMzUyMSAxMi4xMTcxODYsMTMuNDI3NzMxIDIuODEyNDUsMi44MTI0NCAzMS40NzQ2MSwzMS45MDIzNSAzMS40NzQ2MSwzMS45MDIzNSBsIC0wLjAzOTEsMC4wMTU2IGMgMC4wMzkyLDAuMDIzMiAwLjA3NTksMC4wMzg0IDAuMTA3NDIsMC4wNzgxIDIuNjYzNzUsMi42NDMzMyAyLjY2Mzc1LDYuOTQyNDQgMCw5LjU2NjQgLTIuNjIzNjMsMi42NDc0OSAtNi45Mzg2NCwyLjY0NzQ5IC05LjU2NjQxLDAgLTAuMDM2MywtMC4wMTYzIC0wLjA3MjMsLTAuMDUzNiAtMC4wNzIzLC0wLjA5MzcgaCAtMC4wNDEgYyAwLDAgLTMyLjUxOTgzMiwtMzIuNTE4ODYgLTM1Ljc0OTk5NywtMzUuNzY1NjIgLTAuNTIyNTIsLTAuNTIyMTcgLTMuMTgyNTYyLC0zLjE4NTg4IC03LjE2Nzk2OSwtNy4xNzU3OCAtNy42MDE0NzksNy41OTgwMyAtNDQuNTA3ODEyLDQ0LjQ5MjE4IC00NC41MDc4MTIsNDQuNDkyMTggbCAtMC4wMDU5LC0wLjAwNiBjIC0wLjAyNDI1LDAuMDI0MiAtMC4wMzc5NCwwLjA2NzMgLTAuMDgwMDgsMC4xMDc0MyAtMi42MjMyNzgsMi42MjQzNyAtNi45MDE3NCwyLjYyMzc2IC05LjUzMTI1LC0wLjAwOCAtMi42MjcyMjQsLTIuNjMxNTggLTIuNjM3NTU2LC02Ljg5NzQ3IC0wLjAwMzksLTkuNTMzMiAwLjAzODAxLC0wLjA0IDAuMDc3MjMsLTAuMDUyMiAwLjEwOTM3NSwtMC4wODQgbCAtMC4wMDIsLTAuMDA4IGMgMCwwIDM2Ljg1ODE1MywtMzYuODU5NjE1IDQ0LjUwMzkwNiwtNDQuNTEzNjY3IEMgNTQuOTk0NTc5LDY5LjcxMjE1MyAxOS43Mjg1MTYsMzQuMzQ5NjA5IDE5LjcyODUxNiwzNC4zNDk2MDkgbCAxLjU2NjQwNiwtMS41ODc4OSBjIDAsMCAxOS43Nzc2OTIsLTUuNDg5MjUzIDcyLjIyNDYwOSw0NS44MjAzMTIgMi42ODM3OCwtMy42MTE5MzIgNC43NTI4NjYsLTguMzYzMjg3IDUuMTQyNTc4LC0xNC43NjE3MTkgLTAuNTU0NjY4LC01LjA0NDI2OCAxLjEwNTUzNSwtMTAuMjgxNDMxIDQuOTY2ODAxLC0xNC4xNDA2MjQgMC4zNTM3NSwtMC4zNTc5MSAxLjM3MzA0LC0xLjIxNjc5NyAxLjM3MzA0LC0xLjIxNjc5NyB6IgogICAgICAgICBzdHlsZT0iZmlsbDojMDA5ZGUwO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpldmVub2RkO3N0cm9rZTpub25lO3N0cm9rZS13aWR0aDoxLjMzMzMzMzI1IiAvPjwvZz48L2c+PC9zdmc+" alt="FoodIST Logo" style="width:10%;">
                <table>
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Endpoint</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>GET</td>
                            <td>/api/dishes</td>
                        </tr>
                        <tr>
                            <td>POST</td>
                            <td>/api/dishes</td>
                        </tr>
                        <tr>
                            <td>GET</td>
                            <td>/api/dishes/{id}</td>
                        </tr>
                        <tr>
                            <td>DELETE</td>
                            <td>/api/dishes/{id}</td>
                        </tr>
                        <tr>
                            <td>GET</td>
                            <td>/api/cafeterias</td>
                        </tr>
                        <tr>
                            <td>GET</td>
                            <td>/api/cafeterias/{id}</td>
                        </tr>
                        <tr>
                            <td>GET</td>
                            <td>/api/cafeterias/{id}/dishes</td>
                        </tr>
                        <tr>
                    </tbody>
                </table>
            </body>
            </html>';
}