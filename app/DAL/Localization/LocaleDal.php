<?php
declare(strict_types=1);

namespace App\DAL\Localization;


use App\DAL\AbstractEloquentDal;
use App\Models\Locale;

/**
 * Defines the data access layer operations for locale.
 *
 * @package App\DAL\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class LocaleDal extends AbstractEloquentDal
{
    
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return Locale::class;
    }
}
