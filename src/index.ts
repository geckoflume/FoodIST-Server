import {FoodistServerApplication} from './application';
import {ApplicationConfig} from '@loopback/core';
import fs from 'fs';

export {FoodistServerApplication};

export async function main() {
  const options = {
    rest: {
      protocol: 'https',
      key: fs.readFileSync('./key.pem'),
      cert: fs.readFileSync('./cert.pem'),
    },
  };

  const app = new FoodistServerApplication(options);
  await app.boot();
  await app.start();

  console.log(`Welcome to FoodIST REST Server`);
  const url = app.restServer.url;
  console.log(`Server is running at ${url}`);

  return app;
}
