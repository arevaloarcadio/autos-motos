<?php

declare(strict_types=1);

namespace App\DAL\Operation;

use App\DAL\AbstractEloquentDal;
use App\Models\Operation;

/**
 * Defines the data access layer operations for operation.
 *
 * @package App\DAL\Operation
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class OperationDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return Operation::class;
    }
}
