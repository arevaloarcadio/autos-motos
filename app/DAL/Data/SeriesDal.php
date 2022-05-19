<?php

declare(strict_types=1);

namespace App\DAL\Data;

use App\DAL\AbstractEloquentDal;
use App\Models\Series;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Defines the data access layer operations for make.
 *
 * @package App\DAL\Data
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class SeriesDal extends AbstractEloquentDal
{

    /**
     * @param string $generationId
     *
     * @return Collection
     */
    public function findAllByGenerationId(string $generationId): Collection
    {
        return $this->model->newQuery()
                           ->where('generation_id', $generationId)
                           ->orderBy('name', 'ASC')
                           ->get();
    }

    public function getModel(): string
    {
        return Series::class;
    }
}

