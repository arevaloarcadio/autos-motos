<?php

declare(strict_types=1);

namespace App\Service\Market;

use App\Models\Market;
use Illuminate\Support\Facades\Facade;

/**
 * @package App\Service\Market
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 *
 * @method static void setMarket(?Market $market)
 * @method static Market getMarket()
 */
class MarketStorageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'market';
    }
}
