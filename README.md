# FoodIST-Server
FoodIST: Finding food on campus

## Specifications

FoodIST server module, providing a REST API, based on LoopBack4.  
Meant to be used with the FoodIST android application, to provide ability to crowdsource cafeteria menus and queue wait times.

## Prerequisites

- Node.js >v10

## How to build

```shell script
npm i -g @loopback/cli
npm run clean && npm run build
```

## How to start

Run
```shell script
npm start
```
The server will be started on http://localhost:3000/.

## How to use / Endpoints

Get the available API on http://localhost:3000/explorer and the OpenAPI on http://localhost:3000/openapi.json.

Exhaustive enpoint list:
- GET /cafeterias​/count
- GET /cafeterias
- POST /cafeterias​/{id}​/dishes
- GET /cafeterias​/{id}​/dishes
- DELETE /cafeterias​/{id}​/dishes
- GET /dishes​/count
- GET /dishes​/{id}
- DELETE /dishes​/{id}
- POST /dishes
- GET /dishes  

<p align="center">
  <a href="http://loopback.io/"><img src="https://loopback.io/images/branding/powered-by-loopback/blue/powered-by-loopback-sm.png" alt="LoopBack"/></a>
</p>
