import {Entity, model, property, belongsTo} from '@loopback/repository';
import {Cafeteria} from './cafeteria.model';

@model()
export class Dish extends Entity {
  @property({
    type: 'number',
    id: true,
    generated: true,
  })
  id?: number;

  @property({
    type: 'string',
    required: true,
  })
  name: string;

  @property({
    type: 'number',
    required: true,
  })
  price: number;

  @belongsTo(() => Cafeteria)
  cafeteriaId: number;

  constructor(data?: Partial<Dish>) {
    super(data);
  }
}

export interface DishRelations {
  // describe navigational properties here
}

export type DishWithRelations = Dish & DishRelations;
