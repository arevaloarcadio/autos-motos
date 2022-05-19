<?php

declare(strict_types=1);

namespace App\DAL\Data;

use App\DAL\AbstractEloquentDal;
use App\Models\Option;

/**
 * Defines the data access layer operations for make.
 *
 * @package App\DAL\Data
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class OptionDal extends AbstractEloquentDal
{
    public function getModel(): string
    {
        return Option::class;
    }
}

