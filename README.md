# FoodIST-Server

FoodIST: Finding food on campus

## Specifications

FoodIST server module, providing a REST API HTTPS server.  
Meant to be used with the FoodIST Android application, it provides ability to crowdsource cafeteria menus and queue wait times.

## Prerequisites

- A web server, can be Apache, nginx...
- PHP 7
- A MySQL/MariaDB database
- Composer

## How to configure

- Load the [config/database.php](utils/Database.php) script in your corresponding database.
- Then, setup the [database.sql](init.sql) using your database connexion data.
- Finally, configure your web server :

### How to configure Apache

Use the provided [.htaccess](.htaccess), and modify the `FallbackResource` rule according to the app path.

### How to configure other web servers

https://silex.symfony.com/doc/2.0/web_servers.html

## How to start

The server will be started on https://localhost/.

## Endpoints

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
| GET                        | /api/beacons                 |  `/api/beacons`                                                       |
| POST                       | /api/beacons                 | `{"cafeteria_id": 12, "datetime_arrive": "2020-04-26T09:12:43.511Z"}` |
| GET                        | /api/beacons/{id}            |  `/api/beacons/1`                                                     |
| PUT                        | /api/beacons/{id}            | `{"datetime_leave": "2020-04-26T09:12:43.511Z"}`                      |
| DELETE                     | /api/beacons/{id}            |  `/api/beacons/1`                                                     |


### Dishes
| Method                     | Endpoint                     | Example                                                         |
|:-------------------------- |:---------------------------- |:--------------------------------------------------------------- |
| GET                        | /api/dishes                  |  `/api/dishes`                                                  |
| POST                       | /api/dishes                  | `{"cafeteria_id": 12, "name": "Bacalhau à brás", "price": 1.4}` |
| GET                        | /api/dishes/{id}             |  `/api/dishes/1`                                                |
| PUT                        | /api/dishes/{id}             | `{"cafeteria_id": 2, "name": "Soup", "price": 0.8}`             |
| DELETE                     | /api/dishes/{id}             |  `/api/dishes/1`                                                |
| GET                        | /api/dishes/{id}/pictures    |  `/api/dishes/1/pictures`                                       |

### Pictures
| Method                     | Endpoint                     | Example                                   |
|:-------------------------- |:---------------------------- |:----------------------------------------- |
| GET                        | /api/pictures                | `/api/pictures`                           |
| POST (multipart/form-data) | /api/pictures                | `{"dish_id": 12, "picture": <JPEG file>}` |
| GET                        | /api/pictures/{id}           |  `/api/pictures/1`                        |
| DELETE                     | /api/pictures/{id}           |  `/api/pictures/1`                        |

## Valuable resources:
 - How to upload a file using a REST web service:
	 - https://stackoverflow.com/a/4083908
	 - https://symfonycasts.com/screencast/symfony-uploads/storing-uploaded-file
	 
 - MySQL and JSON date formats
    - https://stackoverflow.com/a/409305
    - https://xkcd.com/1179/

