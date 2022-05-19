<?php

declare(strict_types=1);

namespace App\Service\Ad\Viewer;

use App\Models\Ad;
use Illuminate\Support\Collection;

/**
 * @package App\Service\Ad\Viewer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
trait ExcludeCurrentAdTrait
{
    protected function excludeCurrentAd(Collection $items, Ad $currentAd): Collection
    {
        $items = $items->filter(
            function (Ad $item) use ($currentAd) {
                return $item->id !== $currentAd->id;
            }
        );
        if ($items->count() === 10) {
            return $items;
        }

        return $items->take(10);
    }
}
