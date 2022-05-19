<?php
declare(strict_types=1);

namespace App\DAL\Dealer;

use App\DAL\AbstractEloquentDal;
use App\Models\DealerShowRoom;

/**
 * Defines the data access layer operations for dealer show room.
 *
 * @package App\DAL\Dealer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class DealerShowRoomDal extends AbstractEloquentDal
{
    /**
     * Get the current model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return DealerShowRoom::class;
    }
}
