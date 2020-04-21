import {DefaultCrudRepository, repository, HasManyRepositoryFactory} from '@loopback/repository';
import {Cafeteria, CafeteriaRelations, Dish} from '../models';
import {DbDataSource} from '../datasources';
import {inject, Getter} from '@loopback/core';
import {DishRepository} from './dish.repository';

export class CafeteriaRepository extends DefaultCrudRepository<
  Cafeteria,
  typeof Cafeteria.prototype.id,
  CafeteriaRelations
> {

  public readonly dishes: HasManyRepositoryFactory<Dish, typeof Cafeteria.prototype.id>;

  constructor(
    @inject('datasources.db') dataSource: DbDataSource, @repository.getter('DishRepository') protected dishRepositoryGetter: Getter<DishRepository>,
  ) {
    super(Cafeteria, dataSource);
    this.dishes = this.createHasManyRepositoryFactoryFor('dishes', dishRepositoryGetter,);
    this.registerInclusionResolver('dishes', this.dishes.inclusionResolver);
  }
}
