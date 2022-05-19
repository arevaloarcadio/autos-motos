<?php

declare(strict_types=1);

namespace App\Manager\Data;

use App\DAL\Data\EquipmentDal;
use App\Manager\AbstractManager;
use Illuminate\Support\Collection;

/**
 * Defines the entity persistence of Equipment data transfer objects.
 *
 * @package App\Manager\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class EquipmentManager extends AbstractManager
{
    /**
     * @var EquipmentDal
     */
    private $dataAccessLayer;

    /**
     * @param EquipmentDal $dataAccessLayer
     */
    public function __construct(EquipmentDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    /**
     * @param string $trimId
     *
     * @return Collection
     */
    public function findAllByTrimId(string $trimId): Collection
    {
        return $this->dataAccessLayer->findAllByTrimId($trimId);
    }

    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
