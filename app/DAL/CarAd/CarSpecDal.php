<?php
declare(strict_types=1);

namespace App\DAL\CarAd;

use App\DAL\AbstractEloquentDal;
use App\Models\CarSpec;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Defines the data access layer operations for car spec.
 *
 * @package App\DAL\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarSpecDal extends AbstractEloquentDal
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
                           ->select('car_specs.*')
                           ->where('car_model_id', $input['car_model_id'])
                           ->where('car_fuel_type_id', $input['car_fuel_type_id'])
                           ->where('car_body_type_id', $input['car_body_type_id'])
                           ->where('car_transmission_type_id', $input['car_transmission_type_id'])
                           ->whereNotNull('production_start_year')
                           ->where(
                               function (Builder $query) use ($input) {
                                   $query->where('production_start_year', '=', $input['year'])
                                         ->orWhere(
                                             function (Builder $query) use ($input) {
                                                 $query->whereNotNull('production_end_year')
                                                       ->whereRaw(
                                                           '? BETWEEN production_start_year AND production_end_year'
                                                       )
                                                       ->setBindings([$input['year']]);
                                             }
                                         );
                               }
                           )
                           ->orderBy('engine', 'ASC')
                           ->get();
    }
    
    /**
     * @param array $input
     *
     * @return Collection
     */
    public function retrieveYearsByModelId(array $input): Collection
    {
        return $this->model->newQuery()
                           ->distinct()
                           ->select(['production_start_year', 'production_end_year'])
                           ->where('car_model_id', $input['car_model_id'])
                           ->get();
    }
    
    /**
     * @param string $generationId
     * @param int    $itemsPerPage
     *
     * @return LengthAwarePaginator
     */
    public function findAllByGenerationId(string $generationId, int $itemsPerPage): LengthAwarePaginator
    {
        return $this->model->with(['generation'])
                           ->newQuery()
                           ->where('car_generation_id', $generationId)
                           ->orderBy('engine', 'ASC')
                           ->paginate($itemsPerPage);
    }
    
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return CarSpec::class;
    }
}
