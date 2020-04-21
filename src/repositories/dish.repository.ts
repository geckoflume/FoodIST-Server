import {DefaultCrudRepository, repository, BelongsToAccessor} from '@loopback/repository';
import {Dish, DishRelations, Cafeteria} from '../models';
import {DbDataSource} from '../datasources';
import {inject, Getter} from '@loopback/core';
import {CafeteriaRepository} from './cafeteria.repository';

export class DishRepository extends DefaultCrudRepository<
  Dish,
  typeof Dish.prototype.id,
  DishRelations
> {

  public readonly cafeteria: BelongsToAccessor<Cafeteria, typeof Dish.prototype.id>;

  constructor(
    @inject('datasources.db') dataSource: DbDataSource, @repository.getter('CafeteriaRepository') protected cafeteriaRepositoryGetter: Getter<CafeteriaRepository>,
  ) {
    super(Dish, dataSource);
    this.cafeteria = this.createBelongsToAccessorFor('cafeteria', cafeteriaRepositoryGetter,);
    this.registerInclusionResolver('cafeteria', this.cafeteria.inclusionResolver);
  }
}
