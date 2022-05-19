<?php
declare(strict_types=1);

namespace App\Manager\Ad;

use App\DAL\Ad\AutoOptionDal;
use App\DAL\DataAccessLayerInterface;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of car option data transfer objects.
 *
 * @package App\Manager\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AutoOptionManager extends AbstractManager
{
    /**
     * @var AutoOptionDal
     */
    private $autoOptionDal;
    
    /**
     * CarSpecManager constructor.
     *
     * @param AutoOptionDal $autoOptionDal
     */
    public function __construct(AutoOptionDal $autoOptionDal)
    {
        $this->autoOptionDal = $autoOptionDal;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->autoOptionDal;
    }
}
