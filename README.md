# FoodIST-Server
FoodIST: Finding food on campus

## Specifications

FoodIST server module, providing a REST API HTTPS server.  
Meant to be used with the FoodIST Android application, it provides ability to crowdsource cafeteria menus and queue wait times.

## Prerequisites

- A web server, can be Apache, nginx...
- Mysql/MariaDB
- Composer

## How to configure

- Load the [config/database.php](config/database.php) script in your corresponding database.
- Then, setup the [database.sql](init.sql) using your database connexion data.
- Finally, configure your web server :

### How to configure Apache

Use the provided [.htaccess](.htaccess), and modify the `FallbackResource` rule according to the app path.

### How to configure other web servers

https://silex.symfony.com/doc/2.0/web_servers.html

## How to start

The server will be started on https://localhost/.

## Endpoints

### Dishes
| Method | Endpoint                    |
|:------ |:--------------------------- |
| GET    | /api/dishes                 |
| POST   | /api/dishes                 |
| GET    | /api/dishes/{id}            |
| DELETE | /api/dishes/{id}            |

