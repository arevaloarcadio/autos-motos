<?php
declare(strict_types=1);

namespace App\DAL\Market;

use App\DAL\AbstractEloquentDal;
use App\Models\Market;

/**
 * Defines the data access layer operations for market.
 *
 * @package App\DAL\Market
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MarketDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return Market::class;
    }
}
