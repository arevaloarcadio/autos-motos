<?php

declare(strict_types=1);

namespace App\DAL\Data;

use App\DAL\AbstractEloquentDal;
use App\Models\Generation;
use Illuminate\Support\Collection;

/**
 * Defines the data access layer operations for make.
 *
 * @package App\DAL\Data
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class GenerationDal extends AbstractEloquentDal
{
    /**
     * @param string   $modelId
     * @param int|null $year
     *
     * @return Collection
     */
    public function findAllByModelId(string $modelId, ?int $year = null): Collection
    {
        $query = $this->model->newQuery()
                             ->where('model_id', $modelId);

        if ( ! (null === $year)) {
            $query->whereRaw('? BETWEEN IFNULL(year_begin, 1900) AND IFNULL(year_end, YEAR(CURDATE()))')
                  ->setBindings([$modelId, $year]);
        }

        return $query->orderBy('name', 'ASC')
                     ->get();
    }

    public function getModel(): string
    {
        return Generation::class;
    }
}

