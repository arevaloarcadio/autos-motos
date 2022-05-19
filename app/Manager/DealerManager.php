<?php
declare(strict_types=1);

namespace App\Manager;

use App\DAL\DataAccessLayerInterface;
use App\DAL\Dealer\DealerDal;
use App\Manager\AbstractManager;
use App\Models\Dealer;

/**
 * Defines the entity persistence of dealer data transfer objects.
 *
 * @package App\Manager\Dealer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class DealerManager extends AbstractManager
{
    /**
     * @var DealerDal
     */
    private $dealerDal;
    
    /**
     * DealerManager constructor.
     *
     * @param DealerDal $dealerDal
     */
    public function __construct(DealerDal $dealerDal)
    {
        $this->dealerDal = $dealerDal;
    }
    
    /**
     * @param Dealer $instance
     * @param int    $newStatus
     *
     * @return Dealer
     */
    public function changeStatus(Dealer $instance, int $newStatus): Dealer
    {
        $instance->status = $newStatus;
        $this->dealerDal->save($instance);
        
        return $instance;
    }
    
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->dealerDal;
    }
}
