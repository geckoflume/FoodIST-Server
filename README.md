# FoodIST-Server
FoodIST: Finding food on campus

## Specifications

FoodIST server module, providing a REST API HTTPS server, based on Eclipse Vert.x v3.9.  
Meant to be used with the FoodIST android application, it provides ability to crowdsource cafeteria menus and queue wait times.

## Prerequisites

- Java JDK 8+
- Gradle

## How to build

```shell script
openssl req \
       -newkey rsa:2048 -nodes -keyout server-key.pem \
       -x509 -days 365 -out server-cert.pem
/.gradlew build
```

## How to start

```shell script
./gradlew run
```
The server will be started on https://localhost:3000/.

## Endpoints

### Dishes
| Method | Endpoint                       |
|:------ |:------------------------------ |
| GET    | /api/v1/dishes                 |
| POST   | /api/v1/dishes                 |
| GET    | /api/v1/dishes/{id}            |
| DELETE | /api/v1/dishes/{id}            |

### Cafeterias
| Method | Endpoint                       |
|:------ |:------------------------------ |
| GET    | /api/v1/cafeterias             |
| GET    | /api/v1/cafeterias/{id}             |
| GET    | /api/v1/cafeterias/{id}/dishes |


<p align="center">
  <a href="https://vertx.io/"><img src="https://github.com/vert-x3/vertx-web-site/raw/master/src/site/assets/logo-sm.png" alt="Vert.x"/></a>
</p>
