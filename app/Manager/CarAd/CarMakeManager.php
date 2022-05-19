<?php
declare(strict_types=1);

namespace App\Manager\CarAd;

use App\DAL\CarAd\CarMakeDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of car make data transfer objects.
 *
 * @package App\Manager\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class CarMakeManager extends AbstractManager
{
    /**
     * @var CarMakeDal
     */
    private $carMakeDal;
    
    /**
     * CarMakeManager constructor.
     *
     * @param CarMakeDal $carMakeDal
     */
    public function __construct(CarMakeDal $carMakeDal)
    {
        $this->carMakeDal = $carMakeDal;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->carMakeDal;
    }
}
