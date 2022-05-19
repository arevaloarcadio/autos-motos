<?php

declare(strict_types=1);

namespace App\DAL\Data;

use App\DAL\AbstractEloquentDal;
use App\Models\Specification;

/**
 * Defines the data access layer operations for make.
 *
 * @package App\DAL\Data
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class SpecificationDal extends AbstractEloquentDal
{
    public function getModel(): string
    {
        return Specification::class;
    }
}

