<?php

declare(strict_types=1);

namespace App\Service\Ad;

use App\Models\Ad;

/**
 * @package App\Service\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdStatusService
{
    public function changeStatus(Ad $ad, int $newStatus): Ad
    {
        $ad->status = $newStatus;
        $ad->save();
        
        return $ad;
    }
}
