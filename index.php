<?php

use Phpml\Regression\LeastSquares;

$cafeteriaId = $_GET['cafeteriaId'] ?? '1';
$scatter = "[]";
$line = "[]";

$stmt = (new BeaconEntity())->fetchAllByCafeteriaCompleted($cafeteriaId);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $datasetJson = array_map(function ($tag) {
        return array(
            'x' => $tag['count_in_queue'],
            'y' => $tag['duration']
        );
    }, $dataset);
    $scatter = json_encode($datasetJson);

    $samples = array_column($dataset, 'count_in_queue');
    $targets = array_column($dataset, 'duration');
    $max = max($samples);

    if (count(array_unique($samples)) > 1) {
        array_walk($samples, 'wrapValueInArray');
        $regression = new LeastSquares();
        $regression->train($samples, $targets);
        $lineVal = array();
        for ($i = 0; $i <= $max; $i++) {
            $lineVal[$i]["x"] = $i;
            $lineVal[$i]["y"] = $regression->predict(array($i));
        }
    } else {
        $lineVal[] = array(
            "x" => $samples[0],
            "y" => array_sum($targets) / count($targets)
        );
    }
    $line = json_encode($lineVal);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        th, td {
            vertical-align: middle !important;
            text-align: center;
        }

        pre {
            display: inline-block;
            text-align: left;
            margin-bottom: 0;
        }

        .form-inline .form-group {
            margin: 10px auto 20px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <title>FoodIST REST Server</title>
</head>
<body>
<main class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4"
             src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgdmVyc2lvbj0iMS4xIgogICBpZD0ic3ZnMiIKICAgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIKICAgd2lkdGg9IjE3MS4xMTMzMyIKICAgaGVpZ2h0PSIyMDYuOTU0NjciCiAgIHZpZXdCb3g9IjAgMCAxNzEuMTEzMzIgMjA2Ljk1NDY3Ij48bWV0YWRhdGEKICAgICBpZD0ibWV0YWRhdGE4Ij48cmRmOlJERj48Y2M6V29yawogICAgICAgICByZGY6YWJvdXQ9IiI+PGRjOmZvcm1hdD5pbWFnZS9zdmcreG1sPC9kYzpmb3JtYXQ+PGRjOnR5cGUKICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPjxkYzp0aXRsZT48L2RjOnRpdGxlPjwvY2M6V29yaz48L3JkZjpSREY+PC9tZXRhZGF0YT48ZGVmcwogICAgIGlkPSJkZWZzNiIgLz48ZwogICAgIGlkPSJnMTAiCiAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMS4zMzMzMzMzLDAsMCwtMS4zMzMzMzMzLC0xMjQuMTgxNzMsNDk4Ljg2Njc5KSI+PGcKICAgICAgIGlkPSJnMjAiCiAgICAgICB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxOTEuNDY1MywzMjUuMTQyMSkiPjxwYXRoCiAgICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KDAuNzUwMDAwMDIsMCwwLC0wLjc1MDAwMDAyLC05OC4zMjksNDkuMDA4KSIKICAgICAgICAgaWQ9InBhdGgyMiIKICAgICAgICAgZD0ibSAwLDAgdiA4Ni44NTU0NjkgYyAwLDc4LjIwNjY2MSA4NS4zOTY0ODQsMTIwLjA5OTYxMSA4NS4zOTY0ODQsMTIwLjA5OTYxMSAwLDAgODUuNzE2Nzk2LC00MS44OTI5NSA4NS43MTY3OTYsLTEyMC4wOTk2MTEgViAwIFogbSAxMzMuMTM0NzcsMjYuNTQ0OTIyIDIuNTY0NDUsMi41NjQ0NTMgLTMwLjUyNzM0LDI4LjYzODY3MiAzLjMxNDQ1LDMuMzA4NTk0IDI5LjU3NDIyLC0yOS41NzQyMTkgMi43MjA3LDIuNzE0ODQ0IC0yOS41NzQyMiwyOS41NzgxMjUgMi43OTQ5MiwyLjc5ODgyOCAyOS41NzIyNywtMjkuNTc4MTI1IDIuNDcwNywyLjQ1NzAzMSAtMjkuMzk4NDQsMjkuMzk2NDg0IDMuMzcxMSwzLjM4MDg2IDI4LjU2MjUsLTMwLjI0MDIzNSAyLjgwNDY5LDIuODE0NDU0IC0yMy4zNDc2NiwyOS40MjM4MjggYyAwLDAgLTAuNTU4MDYsMC41Njg0NzkgLTAuNzk0OTIsMC43NzczNDMgLTMuMzk1MTMsMi45NjcwMTMgLTcuNjYxODksNC4zNzAzMTMgLTExLjg4ODY3LDQuMjU1ODYgLTAuMDI0MiwwIC0wLjA3MzksMC4wMDQ1IC0wLjA4OTgsMC4wMTE3MiAtOS4zNDUzOSwwLjA5NDQgLTE1LjY3MjE3Myw0Ljg4MzMxOSAtMjAuMDIzNDM2LDkuODY5MTQgMC4xNzY2OTksOC40MjkzNDUgMTAuMzczNjQ2LDEyLjkxMzUyMSAxMi4xMTcxODYsMTMuNDI3NzMxIDIuODEyNDUsMi44MTI0NCAzMS40NzQ2MSwzMS45MDIzNSAzMS40NzQ2MSwzMS45MDIzNSBsIC0wLjAzOTEsMC4wMTU2IGMgMC4wMzkyLDAuMDIzMiAwLjA3NTksMC4wMzg0IDAuMTA3NDIsMC4wNzgxIDIuNjYzNzUsMi42NDMzMyAyLjY2Mzc1LDYuOTQyNDQgMCw5LjU2NjQgLTIuNjIzNjMsMi42NDc0OSAtNi45Mzg2NCwyLjY0NzQ5IC05LjU2NjQxLDAgLTAuMDM2MywtMC4wMTYzIC0wLjA3MjMsLTAuMDUzNiAtMC4wNzIzLC0wLjA5MzcgaCAtMC4wNDEgYyAwLDAgLTMyLjUxOTgzMiwtMzIuNTE4ODYgLTM1Ljc0OTk5NywtMzUuNzY1NjIgLTAuNTIyNTIsLTAuNTIyMTcgLTMuMTgyNTYyLC0zLjE4NTg4IC03LjE2Nzk2OSwtNy4xNzU3OCAtNy42MDE0NzksNy41OTgwMyAtNDQuNTA3ODEyLDQ0LjQ5MjE4IC00NC41MDc4MTIsNDQuNDkyMTggbCAtMC4wMDU5LC0wLjAwNiBjIC0wLjAyNDI1LDAuMDI0MiAtMC4wMzc5NCwwLjA2NzMgLTAuMDgwMDgsMC4xMDc0MyAtMi42MjMyNzgsMi42MjQzNyAtNi45MDE3NCwyLjYyMzc2IC05LjUzMTI1LC0wLjAwOCAtMi42MjcyMjQsLTIuNjMxNTggLTIuNjM3NTU2LC02Ljg5NzQ3IC0wLjAwMzksLTkuNTMzMiAwLjAzODAxLC0wLjA0IDAuMDc3MjMsLTAuMDUyMiAwLjEwOTM3NSwtMC4wODQgbCAtMC4wMDIsLTAuMDA4IGMgMCwwIDM2Ljg1ODE1MywtMzYuODU5NjE1IDQ0LjUwMzkwNiwtNDQuNTEzNjY3IEMgNTQuOTk0NTc5LDY5LjcxMjE1MyAxOS43Mjg1MTYsMzQuMzQ5NjA5IDE5LjcyODUxNiwzNC4zNDk2MDkgbCAxLjU2NjQwNiwtMS41ODc4OSBjIDAsMCAxOS43Nzc2OTIsLTUuNDg5MjUzIDcyLjIyNDYwOSw0NS44MjAzMTIgMi42ODM3OCwtMy42MTE5MzIgNC43NTI4NjYsLTguMzYzMjg3IDUuMTQyNTc4LC0xNC43NjE3MTkgLTAuNTU0NjY4LC01LjA0NDI2OCAxLjEwNTUzNSwtMTAuMjgxNDMxIDQuOTY2ODAxLC0xNC4xNDA2MjQgMC4zNTM3NSwtMC4zNTc5MSAxLjM3MzA0LC0xLjIxNjc5NyAxLjM3MzA0LC0xLjIxNjc5NyB6IgogICAgICAgICBzdHlsZT0iZmlsbDojMDA5ZGUwO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpldmVub2RkO3N0cm9rZTpub25lO3N0cm9rZS13aWR0aDoxLjMzMzMzMzI1IiAvPjwvZz48L2c+PC9zdmc+"
             alt="FoodIST Logo" width="96" height="96">
        <h1>FoodIST Server</h1>
        <p class="lead">Welcome to FoodIST REST Server!</p>
        <p>Repository: <a href="https://github.com/geckoflume/FoodIST-Server">https://github.com/geckoflume/FoodIST-Server</a>
        </p>
        <a href="https://app.getpostman.com/run-collection/419f476bdcd0fcbde597">
            <img src="https://run.pstmn.io/button.svg"></a>
    </div>
    <canvas id="cafeteriaChart"></canvas>
    <form class="form-inline">
        <div class="form-group">
            <label for="inputCafeteria">Cafeteria ID</label>
            <input type="number" class="form-control mx-md-3" id="inputCafeteria" name="cafeteriaId"
                   value="<?php echo $cafeteriaId ?>" placeholder="Cafeteria ID" min="1" max="15" required>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover">
            <caption>Endpoints for cafeterias</caption>
            <thead>
            <tr>
                <th style="width: 25%">Method</th>
                <th style="width: 35%">Endpoint</th>
                <th style="width: 40%">Example</th>
            </tr>
            </thead>
            <tbody>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/cafeterias</td>
                <td><code><a href="cafeterias">/api/cafeterias</a></code></td>
            </tr>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/cafeterias/{id}</td>
                <td><code><a href="cafeterias/1">/api/cafeterias/1</a></code></td>
            </tr>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/cafeterias/{id}/beacons</td>
                <td><code><a href="cafeterias/1/beacons">/api/cafeterias/1/beacons</a></code></td>
            </tr>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/cafeterias/{id}/dishes</td>
                <td><code><a href="cafeterias/1/dishes">/api/cafeterias/1/dishes</a></code></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover">
            <caption>Endpoints for beacons</caption>
            <thead>
            <tr>
                <th style="width: 25%">Method</th>
                <th style="width: 35%">Endpoint</th>
                <th style="width: 40%">Example</th>
            </tr>
            </thead>
            <tbody>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/beacons</td>
                <td><code><a href="beacons">/api/beacons</a></code></td>
            </tr>
            <tr class="table-warning">
                <td>POST</td>
                <td>/api/beacons</td>
                <td>
<pre><code>{
    "cafeteria_id": 12,
    "datetime_arrive": "2020-04-26T09:12:43.511Z"
}</code></pre>
                </td>
            </tr>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/beacons/{id}</td>
                <td><code><a href="beacons/1">/api/beacons/1</a></code></td>
            </tr>
            <tr class="table-primary">
                <td>PUT</td>
                <td>/api/beacons/{id}</td>
                <td>
<pre><code>{
    "datetime_leave": "2020-04-26T09:28:43.511Z"
}</code></pre>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover">
            <caption>Endpoints for dishes</caption>
            <thead>
            <tr>
                <th style="width: 25%">Method</th>
                <th style="width: 35%">Endpoint</th>
                <th style="width: 40%">Example</th>
            </tr>
            </thead>
            <tbody>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/dishes</td>
                <td><code><a href="dishes">/api/dishes</a></code></td>
            </tr>
            <tr class="table-warning">
                <td>POST</td>
                <td>/api/dishes</td>
                <td>
<pre><code>{
    "cafeteria_id": 12,
    "name": "Bacalhau à brás",
    "price": 1.4
}</code></pre>
                </td>
            </tr>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/dishes/{id}</td>
                <td><code><a href="dishes/1">/api/dishes/1</a></code></td>
            </tr>
            <tr class="table-primary">
                <td>PUT</td>
                <td>/api/dishes/{id}</td>
                <td>
<pre><code>{
    "cafeteria_id": 2,
    "name": "Soup",
    "price": 0.8,
    "have_info": true,
    "meat": false,
    "fish": true,
    "vegetarian": false,
    "vegan": false,
    "dietary_data": "This contains fish"
}</code></pre>
                </td>
            </tr>
            <tr class="table-danger">
                <td>DELETE</td>
                <td>/api/dishes/{id}</td>
                <td><code>/api/dishes/1</code></td>
            </tr>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/dishes/{id}/pictures</td>
                <td><code><a href="dishes/1/pictures">/api/dishes/1/pictures</a></code></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover">
            <caption>Endpoints for pictures</caption>
            <thead>
            <tr>
                <th style="width: 25%">Method</th>
                <th style="width: 35%">Endpoint</th>
                <th style="width: 40%">Example</th>
            </tr>
            </thead>
            <tbody>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/pictures</td>
                <td><code><a href="pictures">/api/pictures</a></code></td>
            </tr>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/pictures/first</td>
                <td><code><a href="pictures/first">/api/pictures/first</a></code></td>
            </tr>
            <tr class="table-warning">
                <td>POST (multipart/form-data)</td>
                <td>/api/pictures</td>
                <td>
<pre><code>{
    "dish_id": 12,
    "picture": &lt;JPEG file&gt;
}</code></pre>
                </td>
            </tr>
            <tr class="table-success">
                <td>GET</td>
                <td>/api/pictures/{id}</td>
                <td><code><a href="pictures/1">/api/pictures/1</a></code></td>
            </tr>
            <tr class="table-danger">
                <td>DELETE</td>
                <td>/api/pictures/{id}</td>
                <td><code>/api/pictures/1</code></td>
            </tr>
            </tbody>
        </table>
    </div>
</main>
<script>
    let ctx = document.getElementById('cafeteriaChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                label: 'Time spent in the queue (from db)',
                data: <?php echo $scatter ?>,
                showLine: false,
                pointBackgroundColor: 'rgba(54, 162, 235, 0.2)',
                pointBorderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)'
            },
                {
                    label: 'Expected time spent in the queue (linear regression)',
                    data: <?php echo $line ?>,
                    fill: false,
                    pointBackgroundColor: 'rgba(255, 99, 132, 0.2)',
                    pointBorderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)'
                }]
        },
        options: {
            scales: {
                xAxes: [{
                    type: 'linear',
                    position: 'bottom',
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        suggestedMin: 0
                    },
                    scaleLabel: {
                        display: true,
                        labelString: '# of users in queue at the time the user posted a request to the server'
                    }
                }],
                yAxes: [{
                    ticks: {
                        precision: 0
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Time the user stayed in queue (in seconds)'
                    }
                }]
            },
            title: {
                display: true,
                text: 'Wait time per waiting users count for cafeteria #<?php echo $cafeteriaId ?>'
            },
            tooltips: {
                enabled: true,
                mode: 'single',
                callbacks: {
                    title: function (tooltipItems, data) {
                        return tooltipItems[0].xLabel + " user" + ((tooltipItems[0].xLabel > 1) ? "s" : "") + " in queue";
                    },
                    label: function (tooltipItems, data) {
                        if (tooltipItems.yLabel === Math.ceil(tooltipItems.yLabel))
                            return tooltipItems.yLabel + 's wait time';
                        else
                            // use bitwise xor to cast to int (https://stackoverflow.com/a/8388483)
                            return tooltipItems.yLabel + 's wait time (rounded as ' + (Math.ceil(tooltipItems.yLabel) | 0) + 's)';
                    }
                }
            },
            responsive: true
        }
    });
</script>
</body>
</html>