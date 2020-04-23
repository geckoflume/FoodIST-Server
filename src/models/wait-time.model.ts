import {Entity, model, property, belongsTo} from '@loopback/repository';
import {Cafeteria} from './cafeteria.model';

@model()
export class WaitTime extends Entity {
  @property({
    type: 'number',
    id: true,
    generated: true,
  })
  id?: number;

  @property({
    type: 'number',
    required: false,
  })
  time: number;

  @property({
    type: 'Date',
    required: true,
  })
  arrival: Date;

  @property({
    type: 'Date',
    required: false,
  })
  departure: Date;

  @belongsTo(() => Cafeteria)
  cafeteriaId: number;

  constructor(data?: Partial<WaitTime>) {
    super(data);
  }
}

export interface WaitTimeRelations {
  // describe navigational properties here
}

export type WaitTimeWithRelations = WaitTime & WaitTimeRelations;
