<?php

declare(strict_types=1);

namespace App\DAL\Ad;

use App\DAL\AbstractEloquentDal;
use App\Models\Ad;

/**
 * Defines the data access layer operations for ad.
 *
 * @package App\DAL\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdDal extends AbstractEloquentDal
{
    public function getModel(): string
    {
        return Ad::class;
    }
}
