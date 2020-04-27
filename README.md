# FoodIST-Server: Finding food on campus [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/419f476bdcd0fcbde597)
 
FoodIST server module, providing a REST API HTTPS server for Mobile and Ubiquitous Computing class @ Instituto Superior Técnico, Lisbon, Portugal.

Meant to be used with the FoodIST Android application, it provides ability to crowdsource cafeteria menus, dishes pictures and queue wait times.

More information: https://fenix.tecnico.ulisboa.pt/disciplinas/CMov4/2019-2020/2-semestre

*Group 23*

## Features

 - Fetch cafeterias
 - Fetch menus
 - Fetch/add/delete/update the queue wait duration for each user
 - Fetch/add/delete/update dishes
 - Fetch/add/delete picture dishes
 
 For the usage, please read [How to use / Endpoints](#how-to-use-endpoints).

## TODO

 - Add authentication and support for TLS/SSL with HTTPS

## Specifications

This web application is written in PHP 7, built around Silex micro-framework for routing (lighter than the whole Symfony package).  
Data are stored in a MySQL database and can be updated using HTTP methods.

## Prerequisites

- A web server, can be Apache, nginx...
- PHP 7
- A MySQL/MariaDB database
- Composer

## How to setup

- Load the [init.sql](init.sql) script in your corresponding database.
- Then, setup the [utils/Database.php](utils/Database.php) using your database connexion data.
- Finally, configure your web server :

### How to configure Apache

Use the provided [.htaccess](.htaccess), and modify the `FallbackResource` rule according to the app path.

### How to configure other web servers

https://silex.symfony.com/doc/2.0/web_servers.html

## How to use / Endpoints 

The server will be started on https://localhost/.  
Base api url: https://localhost/api

### Cafeterias

| Method                     | Endpoint                     | Example                     |
|:-------------------------- |:---------------------------- |:--------------------------- |
| GET                        | /api/cafeterias              | `/api/cafeterias`           |
| GET                        | /api/cafeterias/{id}         | `/api/cafeterias/1`         |
| GET                        | /api/cafeterias/{id}/beacons | `/api/cafeterias/1/beacons` |
| GET                        | /api/cafeterias/{id}/dishes  | `/api/cafeterias/1/dishes`  |

### Beacons

| Method                     | Endpoint                     | Example                                                               |
|:-------------------------- |:---------------------------- |:--------------------------------------------------------------------- |
| GET                        | /api/beacons                 | `/api/beacons`                                                        |
| POST                       | /api/beacons                 | `{"cafeteria_id": 12, "datetime_arrive": "2020-04-26T09:12:43.511Z"}` |
| GET                        | /api/beacons/{id}            | `/api/beacons/1`                                                      |
| PUT                        | /api/beacons/{id}            | `{"datetime_leave": "2020-04-26T09:12:43.511Z"}`                      |


### Dishes

| Method                     | Endpoint                     | Example                                                         |
|:-------------------------- |:---------------------------- |:--------------------------------------------------------------- |
| GET                        | /api/dishes                  | `/api/dishes`                                                   |
| POST                       | /api/dishes                  | `{"cafeteria_id": 12, "name": "Bacalhau à brás", "price": 1.4}` |
| GET                        | /api/dishes/{id}             | `/api/dishes/1`                                                 |
| PUT                        | /api/dishes/{id}             | `{"cafeteria_id": 2, "name": "Soup", "price": 0.8}`             |
| DELETE                     | /api/dishes/{id}             | `/api/dishes/1`                                                 |
| GET                        | /api/dishes/{id}/pictures    | `/api/dishes/1/pictures`                                        |

### Pictures

| Method                     | Endpoint                     | Example                                   |
|:-------------------------- |:---------------------------- |:----------------------------------------- |
| GET                        | /api/pictures                | `/api/pictures`                           |
| POST (multipart/form-data) | /api/pictures                | `{"dish_id": 12, "picture": <JPEG file>}` |
| GET                        | /api/pictures/{id}           | `/api/pictures/1`                         |
| DELETE                     | /api/pictures/{id}           | `/api/pictures/1`                         |

## Valuable resources

 - How to upload a file using a REST web service:
	 - https://stackoverflow.com/a/4083908
	 - https://symfonycasts.com/screencast/symfony-uploads/storing-uploaded-file
	 
 - MySQL and JSON date formats:
    - https://stackoverflow.com/a/409305
    - https://xkcd.com/1179/
    
 - Queuing theory
    - https://en.wikipedia.org/wiki/Queueing_theory

