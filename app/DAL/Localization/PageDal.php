<?php
declare(strict_types=1);

namespace App\DAL\Localization;

use App\DAL\AbstractEloquentDal;
use App\Models\Page;

/**
 * Defines the data access layer operations for page.
 *
 * @package App\DAL\Localization
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class PageDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return Page::class;
    }
}
