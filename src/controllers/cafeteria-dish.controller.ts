import {
  Count,
  CountSchema,
  Filter,
  repository,
  Where,
} from '@loopback/repository';
import {
  del,
  get,
  getModelSchemaRef,
  getWhereSchemaFor,
  param,
  post,
  requestBody,
} from '@loopback/rest';
import {
  Cafeteria,
  Dish,
} from '../models';
import {CafeteriaRepository} from '../repositories';

export class CafeteriaDishController {
  constructor(
    @repository(CafeteriaRepository) protected cafeteriaRepository: CafeteriaRepository,
  ) { }

  @get('/cafeterias/{id}/dishes', {
    responses: {
      '200': {
        description: 'Array of Cafeteria has many Dish',
        content: {
          'application/json': {
            schema: {type: 'array', items: getModelSchemaRef(Dish)},
          },
        },
      },
    },
  })
  async find(
    @param.path.number('id') id: number,
  ): Promise<Dish[]> {
    return this.cafeteriaRepository.dishes(id).find();
  }

  @post('/cafeterias/{id}/dishes', {
    responses: {
      '200': {
        description: 'Cafeteria model instance',
        content: {'application/json': {schema: getModelSchemaRef(Dish)}},
      },
    },
  })
  async create(
    @param.path.number('id') id: typeof Cafeteria.prototype.id,
    @requestBody({
      content: {
        'application/json': {
          schema: getModelSchemaRef(Dish, {
            title: 'NewDishInCafeteria',
            exclude: ['id'],
            optional: ['cafeteriaId']
          }),
        },
      },
    }) dish: Omit<Dish, 'id'>,
  ): Promise<Dish> {
    return this.cafeteriaRepository.dishes(id).create(dish);
  }

  @del('/cafeterias/{id}/dishes', {
    responses: {
      '200': {
        description: 'Cafeteria.Dish DELETE success count',
        content: {'application/json': {schema: CountSchema}},
      },
    },
  })
  async delete(
    @param.path.number('id') id: number,
    @param.query.object('where', getWhereSchemaFor(Dish)) where?: Where<Dish>,
  ): Promise<Count> {
    return this.cafeteriaRepository.dishes(id).delete(where);
  }
}
