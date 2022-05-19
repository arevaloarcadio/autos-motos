<?php
declare(strict_types=1);

namespace App\Manager\CarAd;

use App\DAL\CarAd\CarWheelDriveTypeDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;
use App\Manager\FindAllByAdTypeManagerTrait;

/**
 * Defines the entity persistence of car wheel driver type data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarWheelDriveTypeManager extends AbstractManager
{
    use FindAllByAdTypeManagerTrait;

    /**
     * @var CarWheelDriveTypeDal
     */
    private $carWheelDriveTypeDal;
    
    /**
     * CarWheelDriverTypeManager constructor.
     *
     * @param CarWheelDriveTypeDal $carWheelDriveTypeDal
     */
    public function __construct(CarWheelDriveTypeDal $carWheelDriveTypeDal)
    {
        $this->carWheelDriveTypeDal = $carWheelDriveTypeDal;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->carWheelDriveTypeDal;
    }
}
