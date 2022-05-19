<?php

declare(strict_types=1);

namespace App\DAL\Content;

use App\DAL\AbstractEloquentDal;
use App\Models\Banner;

/**
 * Defines the data access layer operations for banner.
 *
 * @package App\DAL\Content
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class BannerDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return Banner::class;
    }
}
