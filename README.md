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
| Method                     | Endpoint                    |
|:-------------------------- |:--------------------------- |
| GET                        | /api/cafeterias             |
| GET                        | /api/cafeterias/{id}        |
| GET                        | /api/cafeterias/{id}/dishes |

### Dishes
| Method                     | Endpoint                    |
|:-------------------------- |:--------------------------- |
| GET                        | /api/dishes                 |
| POST                       | /api/dishes                 |
| GET                        | /api/dishes/{id}            |
| PUT                        | /api/dishes/{id}            |
| DELETE                     | /api/dishes/{id}            |
| GET                        | /api/dishes/{id}/pictures   |

### Pictures
| Method                     | Endpoint                    |
|:-------------------------- |:--------------------------- |
| GET                        | /api/pictures               |
| POST (multipart/form-data) | /api/pictures               |
| GET                        | /api/pictures/{id}          |
| DELETE                     | /api/pictures/{id}          |

## Valuable resources:
 - How to upload a file using a REST web service:
	 - https://stackoverflow.com/a/4083908
	 - https://symfonycasts.com/screencast/symfony-uploads/storing-uploaded-file

