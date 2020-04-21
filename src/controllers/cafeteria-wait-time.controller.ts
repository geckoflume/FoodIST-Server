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
  WaitTime,
} from '../models';
import {CafeteriaRepository} from '../repositories';

export class CafeteriaWaitTimeController {
  constructor(
    @repository(CafeteriaRepository) protected cafeteriaRepository: CafeteriaRepository,
  ) { }

  @get('/cafeterias/{id}/wait-time', {
    responses: {
      '200': {
        description: 'Cafeteria has one WaitTime',
        content: {
          'application/json': {
            schema: getModelSchemaRef(WaitTime),
          },
        },
      },
    },
  })
  async get(
    @param.path.number('id') id: number,
    @param.query.object('filter') filter?: Filter<WaitTime>,
  ): Promise<WaitTime> {
    return this.cafeteriaRepository.waitTime(id).get(filter);
  }

  @post('/cafeterias/{id}/wait-time', {
    responses: {
      '200': {
        description: 'Cafeteria model instance',
        content: {'application/json': {schema: getModelSchemaRef(WaitTime)}},
      },
    },
  })
  async create(
    @param.path.number('id') id: typeof Cafeteria.prototype.id,
    @requestBody({
      content: {
        'application/json': {
          schema: getModelSchemaRef(WaitTime, {
            title: 'NewWaitTimeInCafeteria',
            exclude: ['id'],
            optional: ['cafeteriaId']
          }),
        },
      },
    }) waitTime: Omit<WaitTime, 'id'>,
  ): Promise<WaitTime> {
    return this.cafeteriaRepository.waitTime(id).create(waitTime);
  }

  @del('/cafeterias/{id}/wait-time', {
    responses: {
      '200': {
        description: 'Cafeteria.WaitTime DELETE success count',
        content: {'application/json': {schema: CountSchema}},
      },
    },
  })
  async delete(
    @param.path.number('id') id: number,
    @param.query.object('where', getWhereSchemaFor(WaitTime)) where?: Where<WaitTime>,
  ): Promise<Count> {
    return this.cafeteriaRepository.waitTime(id).delete(where);
  }
}
