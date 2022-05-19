<?php
declare(strict_types=1);

namespace App\DAL\CarAd;

use App\DAL\AbstractEloquentDal;
use App\Models\CarMake;

/**
 * Defines the data access layer operations for car make.
 *
 * @package App\DAL\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarMakeDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return CarMake::class;
    }
}
