<?php

declare(strict_types=1);

namespace App\Output;

use App\Models\Dealer;
use App\Models\DealerShowRoom;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class ImportSellerOutput
{
    private Dealer $dealer;
    private DealerShowRoom $showRoom;

    public function __construct(Dealer $dealer, DealerShowRoom $showRoom)
    {
        $this->dealer   = $dealer;
        $this->showRoom = $showRoom;
    }

    /**
     * Get the value of the dealer property.
     *
     * @return Dealer
     */
    public function getDealer(): Dealer
    {
        return $this->dealer;
    }

    /**
     * Get the value of the showRoom property.
     *
     * @return DealerShowRoom
     */
    public function getShowRoom(): DealerShowRoom
    {
        return $this->showRoom;
    }
}
