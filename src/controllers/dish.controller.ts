import {
  Count,
  CountSchema,
  Filter,
  FilterExcludingWhere,
  repository,
  Where,
} from '@loopback/repository';
import {
  post,
  param,
  get,
  getModelSchemaRef,
  del,
  requestBody,
} from '@loopback/rest';
import {Dish} from '../models';
import {DishRepository} from '../repositories';

export class DishController {
  constructor(
    @repository(DishRepository)
    public dishRepository : DishRepository,
  ) {}

  @post('/dishes', {
    responses: {
      '200': {
        description: 'Dish model instance',
        content: {'application/json': {schema: getModelSchemaRef(Dish)}},
      },
    },
  })
  async create(
    @requestBody({
      content: {
        'application/json': {
          schema: getModelSchemaRef(Dish, {
            title: 'NewDish',
            exclude: ['id'],
          }),
        },
      },
    })
    dish: Omit<Dish, 'id'>,
  ): Promise<Dish> {
    return this.dishRepository.create(dish);
  }

  @get('/dishes/count', {
    responses: {
      '200': {
        description: 'Dish model count',
        content: {'application/json': {schema: CountSchema}},
      },
    },
  })
  async count(
    @param.where(Dish) where?: Where<Dish>,
  ): Promise<Count> {
    return this.dishRepository.count(where);
  }

  @get('/dishes', {
    responses: {
      '200': {
        description: 'Array of Dish model instances',
        content: {
          'application/json': {
            schema: {
              type: 'array',
              items: getModelSchemaRef(Dish, {includeRelations: true}),
            },
          },
        },
      },
    },
  })
  async find(): Promise<Dish[]> {
    return this.dishRepository.find();
  }

  @get('/dishes/{id}', {
    responses: {
      '200': {
        description: 'Dish model instance',
        content: {
          'application/json': {
            schema: getModelSchemaRef(Dish, {includeRelations: true}),
          },
        },
      },
    },
  })
  async findById(
    @param.path.number('id') id: number
  ): Promise<Dish> {
    return this.dishRepository.findById(id);
  }

  @del('/dishes/{id}', {
    responses: {
      '204': {
        description: 'Dish DELETE success',
      },
    },
  })
  async deleteById(@param.path.number('id') id: number): Promise<void> {
    await this.dishRepository.deleteById(id);
  }
}
