import {DefaultCrudRepository, repository, HasManyRepositoryFactory, HasOneRepositoryFactory} from '@loopback/repository';
import {Cafeteria, CafeteriaRelations, Dish, WaitTime} from '../models';
import {DbDataSource} from '../datasources';
import {inject, Getter} from '@loopback/core';
import {DishRepository} from './dish.repository';
import {WaitTimeRepository} from './wait-time.repository';

export class CafeteriaRepository extends DefaultCrudRepository<
  Cafeteria,
  typeof Cafeteria.prototype.id,
  CafeteriaRelations
> {

  public readonly dishes: HasManyRepositoryFactory<Dish, typeof Cafeteria.prototype.id>;

  public readonly waitTime: HasOneRepositoryFactory<WaitTime, typeof Cafeteria.prototype.id>;

  constructor(
    @inject('datasources.db') dataSource: DbDataSource, @repository.getter('DishRepository') protected dishRepositoryGetter: Getter<DishRepository>, @repository.getter('WaitTimeRepository') protected waitTimeRepositoryGetter: Getter<WaitTimeRepository>,
  ) {
    super(Cafeteria, dataSource);
    this.waitTime = this.createHasOneRepositoryFactoryFor('waitTime', waitTimeRepositoryGetter);
    this.registerInclusionResolver('waitTime', this.waitTime.inclusionResolver);
    this.dishes = this.createHasManyRepositoryFactoryFor('dishes', dishRepositoryGetter,);
    this.registerInclusionResolver('dishes', this.dishes.inclusionResolver);
  }
}
