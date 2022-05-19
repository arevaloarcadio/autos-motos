<?php
declare(strict_types=1);

namespace App\DAL\Dealer;

use App\DAL\AbstractEloquentDal;
use App\Models\Dealer;

/**
 * Defines the data access layer operations for dealer.
 *
 * @package App\DAL\Dealer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class DealerDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return Dealer::class;
    }
}
