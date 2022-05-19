<?php
declare(strict_types=1);

namespace App\Manager;

use App\DAL\DataAccessLayerInterface;
use App\DAL\Market\MarketDal;
use App\Manager\AbstractManager;
use App\Models\Market;
use Illuminate\Database\Eloquent\Model;

/**
 * Defines the entity persistence of market data transfer objects.
 *
 * @package App\Manager\Market
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 *
 * @method Market|null findOneBy(array $criteria, array $loadRelationships = [])
 */
class MarketManager extends AbstractManager
{
    /**
     * @var MarketDal
     */
    private $marketDal;

    /**
     * MarketManager constructor.
     *
     * @param MarketDal $marketDal
     */
    public function __construct(MarketDal $marketDal)
    {
        $this->marketDal = $marketDal;
    }

    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    public function getRepository()
    {
        return $this->marketDal;
    }
}
