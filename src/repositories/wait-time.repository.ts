import {DefaultCrudRepository, repository, BelongsToAccessor} from '@loopback/repository';
import {WaitTime, WaitTimeRelations, Cafeteria} from '../models';
import {DbDataSource} from '../datasources';
import {inject, Getter} from '@loopback/core';
import {CafeteriaRepository} from './cafeteria.repository';

export class WaitTimeRepository extends DefaultCrudRepository<
  WaitTime,
  typeof WaitTime.prototype.id,
  WaitTimeRelations
> {

  public readonly cafeteria: BelongsToAccessor<Cafeteria, typeof WaitTime.prototype.id>;

  constructor(
    @inject('datasources.db') dataSource: DbDataSource, @repository.getter('CafeteriaRepository') protected cafeteriaRepositoryGetter: Getter<CafeteriaRepository>,
  ) {
    super(WaitTime, dataSource);
    this.cafeteria = this.createBelongsToAccessorFor('cafeteria', cafeteriaRepositoryGetter,);
    this.registerInclusionResolver('cafeteria', this.cafeteria.inclusionResolver);
  }
}
