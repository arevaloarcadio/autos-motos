<?php
declare(strict_types=1);

namespace App\DAL\CarAd;

use App\DAL\AbstractEloquentDal;
use App\Models\CarGeneration;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Defines the data access layer operations for car generation.
 *
 * @package App\DAL\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarGenerationDal extends AbstractEloquentDal
{
    /**
     * @param string   $modelId
     * @param int      $itemsPerPage
     * @param string[] $loadRelationships
     *
     * @return LengthAwarePaginator
     */
    public function findAllByModelId(
        string $modelId,
        int $itemsPerPage,
        array $loadRelationships = []
    ): LengthAwarePaginator {
        return $this->model->with($loadRelationships)
                           ->newQuery()
                           ->where('car_model_id', $modelId)
                           ->orderBy('name', 'ASC')
                           ->paginate($itemsPerPage);
    }
    
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return CarGeneration::class;
    }
}
