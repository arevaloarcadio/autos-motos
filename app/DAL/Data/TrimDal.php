<?php

declare(strict_types=1);

namespace App\DAL\Data;

use App\DAL\AbstractEloquentDal;
use App\Models\Trim;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Defines the data access layer operations for make.
 *
 * @package App\DAL\Data
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class TrimDal extends AbstractEloquentDal
{
    /**
     * @param string $seriesId
     *
     * @return Collection
     */
    public function findAllBySeriesId(string $seriesId): Collection
    {
        return $this->model->newQuery()
                           ->where('series_id', $seriesId)
                           ->orderBy('name', 'ASC')
                           ->get();
    }

    public function getModel(): string
    {
        return Trim::class;
    }
}

