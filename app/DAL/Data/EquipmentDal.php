<?php

declare(strict_types=1);

namespace App\DAL\Data;

use App\DAL\AbstractEloquentDal;
use App\Models\Equipment;
use Illuminate\Support\Collection;

/**
 * Defines the data access layer operations for make.
 *
 * @package App\DAL\Data
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class EquipmentDal extends AbstractEloquentDal
{

    /**
     * @param string $trimId
     *
     * @return Collection
     */
    public function findAllByTrimId(string $trimId): Collection
    {
        return $this->model->newQuery()
                           ->where('trim_id', $trimId)
                           ->orderBy('name', 'ASC')
                           ->get();
    }

    public function getModel(): string
    {
        return Equipment::class;
    }
}

