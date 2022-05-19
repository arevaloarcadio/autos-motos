<?php
declare(strict_types=1);

namespace App\Service\Ad\Finder;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Finder
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
interface IAdFinder
{
    public function supports(string $adType): bool;

    public function find(string $adType, array $input): LengthAwarePaginator;

    public function count(string $adType, array $input): int;

    public function presentAds(LengthAwarePaginator $ads, string $adType, ?string $adSubType): View;

    public function getBaseQuery(string $adType): Builder;
}
