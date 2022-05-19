<?php
declare(strict_types=1);

namespace App\Manager\Dealer;

use App\DAL\DataAccessLayerInterface;
use App\DAL\Dealer\DealerShowRoomDal;
use App\Manager\AbstractManager;

/**
 * Defines the entity persistence of dealer show room data transfer objects.
 *
 * @package App\Manager\Dealer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class DealerShowRoomManager extends AbstractManager
{
    /**
     * @var DealerShowRoomDal
     */
    private $dealerShowRoomDal;
    
    /**
     * DealerManager constructor.
     *
     * @param DealerShowRoomDal $dealerShowRoomDal
     */
    public function __construct(DealerShowRoomDal $dealerShowRoomDal)
    {
        $this->dealerShowRoomDal = $dealerShowRoomDal;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->dealerShowRoomDal;
    }
}
