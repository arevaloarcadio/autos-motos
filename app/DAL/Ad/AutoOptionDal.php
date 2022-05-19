<?php
declare(strict_types=1);

namespace App\DAL\Ad;

use App\DAL\AbstractEloquentDal;
use App\Models\AutoOption;

/**
 * Defines the data access layer operations for car option.
 *
 * @package App\DAL\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AutoOptionDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return AutoOption::class;
    }
}
