<?php
declare(strict_types=1);

namespace App\DAL\CarAd;

use App\DAL\AbstractEloquentDal;
use App\Models\CarModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Defines the data access layer operations for car model.
 *
 * @package App\DAL\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarModelDal extends AbstractEloquentDal
{
    /**
     * @param string $makeId
     * @param int    $itemsPerPage
     *
     * @return LengthAwarePaginator
     */
    public function findAllByMakeId(
        string $makeId,
        int $itemsPerPage
    ): LengthAwarePaginator {
        return $this->model->with(['make'])
                           ->newQuery()
                           ->where('car_make_id', $makeId)
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
        return CarModel::class;
    }
}
