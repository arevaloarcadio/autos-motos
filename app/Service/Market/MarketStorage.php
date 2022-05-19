<?php
declare(strict_types=1);

namespace App\Service\Market;

use App\Models\Market;
use Exception;

/**
 * @package App\Service\Market
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class MarketStorage
{
    /**
     * @var Market | null
     */
    private $market = null;
    
    /**
     * Get the value of the market property.
     *
     * @return Market
     * @throws Exception
     */
    public function getMarket(): Market
    {
        if ($this->market instanceof Market) {
            return $this->market;
        }
        
        throw new Exception('Market not found.');
    }
    
    /**
     * Set the value of the market property.
     *
     * @param Market|null $market
     *
     * @return MarketStorage
     */
    public function setMarket(?Market $market): MarketStorage
    {
        $this->market = $market;
        
        return $this;
    }
}
