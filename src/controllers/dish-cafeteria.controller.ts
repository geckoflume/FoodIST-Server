import {
  repository,
} from '@loopback/repository';
import {
  param,
  get,
  getModelSchemaRef,
} from '@loopback/rest';
import {
  Dish,
  Cafeteria,
} from '../models';
import {DishRepository} from '../repositories';

export class DishCafeteriaController {
  constructor(
    @repository(DishRepository)
    public dishRepository: DishRepository,
  ) { }

  @get('/dishes/{id}/cafeteria', {
    responses: {
      '200': {
        description: 'Cafeteria belonging to Dish',
        content: {
          'application/json': {
            schema: {type: 'array', items: getModelSchemaRef(Cafeteria)},
          },
        },
      },
    },
  })
  async getCafeteria(
    @param.path.number('id') id: typeof Dish.prototype.id,
  ): Promise<Cafeteria> {
    return this.dishRepository.cafeteria(id);
  }
}
