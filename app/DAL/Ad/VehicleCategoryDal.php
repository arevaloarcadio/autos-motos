<?php
declare(strict_types=1);

namespace App\DAL\Ad;

use App\DAL\AbstractEloquentDal;
use App\Models\VehicleCategory;

/**
 * Defines the data access layer operations for vehicle category.
 *
 * @package App\DAL\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class VehicleCategoryDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return VehicleCategory::class;
    }
}
