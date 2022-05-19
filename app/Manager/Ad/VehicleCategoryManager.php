<?php
declare(strict_types=1);

namespace App\Manager\Ad;

use App\DAL\Ad\VehicleCategoryDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;
use App\Manager\FindAllByAdTypeManagerTrait;

/**
 * Defines the entity persistence of vehicle category data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class VehicleCategoryManager extends AbstractManager
{
    use FindAllByAdTypeManagerTrait;

    /**
     * @var VehicleCategoryDal
     */
    private $dataAccessLayer;

    /**
     * @param VehicleCategoryDal $dataAccessLayer
     */
    public function __construct(VehicleCategoryDal $dataAccessLayer)
    {
        $this->dataAccessLayer = $dataAccessLayer;
    }

    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->dataAccessLayer;
    }
}
