<?php
declare(strict_types=1);

namespace App\DAL\CarAd;

use App\DAL\AbstractEloquentDal;
use App\Models\CarFuelType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the data access layer operations for car fuel type.
 *
 * @package App\DAL\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarFuelTypeDal extends AbstractEloquentDal
{
    /**
     * @param array $input
     *
     * @return Collection
     */
    public function search(array $input): Collection
    {
        return $this->model->newQuery()
                           ->distinct()
                           ->select('car_fuel_types.*')
                           ->join('car_specs as cs', 'car_fuel_types.id', '=', 'cs.car_fuel_type_id')
                           ->where('cs.car_model_id', $input['car_model_id'])
                           ->whereNotNull('cs.production_start_year')
                           ->where(
                               function (Builder $query) use ($input) {
                                   $query->where('cs.production_start_year', '=', $input['year'])
                                         ->orWhere(
                                             function (Builder $query) use ($input) {
                                                 $query->whereNotNull('cs.production_end_year')
                                                       ->whereRaw(
                                                           '? BETWEEN cs.production_start_year AND cs.production_end_year'
                                                       )
                                                       ->setBindings([$input['year']]);
                                             }
                                         );
                               }
                           )
                           ->orderBy('car_fuel_types.internal_name', 'ASC')
                           ->get();
    }
    
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return CarFuelType::class;
    }
}
