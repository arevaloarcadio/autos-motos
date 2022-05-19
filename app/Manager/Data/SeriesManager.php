<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\SeriesDal;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Manager\AbstractManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Defines the entity persistence of Series data transfer objects.
 *
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class SeriesManager extends AbstractManager
{
    /**
     * @var SeriesDal
     */
    private $dataAccessLayer;

    /**
     * @param SeriesDal $dataAccessLayer
     */
    public function __construct(SeriesDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    /**
     * @param string $generationId
     *
     * @return Collection
     */
    public function findAllByGenerationId(string $generationId): Collection
    {
        return $this->dataAccessLayer->findAllByGenerationId($generationId);
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
