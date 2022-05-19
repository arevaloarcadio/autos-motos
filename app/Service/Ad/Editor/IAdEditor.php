<?php

declare(strict_types=1);

namespace App\Service\Ad\Editor;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Editor
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
interface IAdEditor
{
    public function supports(string $adType): bool;

    public function update(Ad $ad, array $input): Ad;

    public function updateFull(Ad $ad, array $input): Ad;

    public function getSpecificAdFromAd(Ad $ad): Model;

    public function presentForm(string $slug): View;
}
