<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\ModelDal;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Manager\AbstractManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Defines the entity persistence of Model data transfer objects.
 *
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class ModelManager extends AbstractManager
{
    /**
     * @var ModelDal
     */
    private $dataAccessLayer;

    /**
     * @param ModelDal $dataAccessLayer
     */
    public function __construct(ModelDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    /**
     * @param string $makeId
     *
     * @return Collection
     */
    public function findAllByMakeId(string $makeId): Collection
    {
        return $this->getRepository()->findAllByMakeId($makeId);
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
