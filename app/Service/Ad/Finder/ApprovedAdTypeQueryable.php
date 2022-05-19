<?php

declare(strict_types=1);

namespace App\Service\Ad\Finder;

use App\Enum\Core\ApprovalStatusEnum;
use App\Models\Ad;
use Illuminate\Database\Eloquent\Builder;

/**
 * @package App\Service\Ad\Finder
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
trait ApprovedAdTypeQueryable
{
    public function getBaseQuery(string $adType): Builder
    {
        return Ad::where('type', $adType)
                 ->where('status', ApprovalStatusEnum::APPROVED);
    }
    
}
