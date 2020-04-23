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
import {Cafeteria} from '../models';
import {CafeteriaRepository} from '../repositories';

export class CafeteriaController {
  constructor(
    @repository(CafeteriaRepository)
    public cafeteriaRepository : CafeteriaRepository,
  ) {}

  @get('/cafeterias/count', {
    responses: {
      '200': {
        description: 'Cafeteria model count',
        content: {'application/json': {schema: CountSchema}},
      },
    },
  })
  async count(
    @param.where(Cafeteria) where?: Where<Cafeteria>,
  ): Promise<Count> {
    return this.cafeteriaRepository.count(where);
  }

  @get('/cafeterias', {
    responses: {
      '200': {
        description: 'Array of Cafeteria model instances',
        content: {
          'application/json': {
            schema: {
              type: 'array',
              items: getModelSchemaRef(Cafeteria, {includeRelations: true}),
            },
          },
        },
      },
    },
  })
  async find(): Promise<Cafeteria[]> {
    return this.cafeteriaRepository.find();
  }
}
