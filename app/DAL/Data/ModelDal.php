<?php

declare(strict_types=1);

namespace App\DAL\Data;

use App\DAL\AbstractEloquentDal;
use App\Models\Model;
use Illuminate\Support\Collection;

/**
 * Defines the data access layer operations for make.
 *
 * @package App\DAL\Data
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class ModelDal extends AbstractEloquentDal
{
    /**
     * @param string $makeId
     *
     * @return Collection
     */
    public function findAllByMakeId(string $makeId): Collection
    {
        return $this->model->newQuery()
                           ->where('make_id', $makeId)
                           ->orderBy('name', 'ASC')
                           ->get();
    }

    public function getModel(): string
    {
        return Model::class;
    }
}

