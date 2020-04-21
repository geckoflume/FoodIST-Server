import {Entity, model, property, hasMany} from '@loopback/repository';
import {Dish} from './dish.model';

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

  constructor(data?: Partial<Cafeteria>) {
    super(data);
  }
}

export interface CafeteriaRelations {
  // describe navigational properties here
}

export type CafeteriaWithRelations = Cafeteria & CafeteriaRelations;
