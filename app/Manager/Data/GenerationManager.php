<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\GenerationDal;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Manager\AbstractManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Defines the entity persistence of Generation data transfer objects.
 *
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class GenerationManager extends AbstractManager
{
    /**
     * @var GenerationDal
     */
    private $dataAccessLayer;

    /**
     * @param GenerationDal $dataAccessLayer
     */
    public function __construct(GenerationDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    /**
     * @param string   $modelId
     * @param int|null $year
     *
     * @return Collection
     */
    public function findAllByModelId(string $modelId, ?int $year = null): Collection
    {
        return $this->dataAccessLayer->findAllByModelId($modelId, $year);
    }


    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
