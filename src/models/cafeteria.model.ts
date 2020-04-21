import {Entity, model, property, hasMany, hasOne} from '@loopback/repository';
import {Dish} from './dish.model';
import {WaitTime} from './wait-time.model';

@model()
export class Cafeteria extends Entity {
  @property({
    type: 'number',
    id: true,
    generated: false,
    required: true,
  })
  id: number;

  @hasMany(() => Dish)
  dishes: Dish[];

  @hasOne(() => WaitTime)
  waitTime: WaitTime;

  constructor(data?: Partial<Cafeteria>) {
    super(data);
  }
}

export interface CafeteriaRelations {
  // describe navigational properties here
}

export type CafeteriaWithRelations = Cafeteria & CafeteriaRelations;
