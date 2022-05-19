<?php

declare(strict_types=1);

namespace App\Service\Ad\Editor;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Ad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Editor
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AutoAdEditor implements IAdEditor
{
    use InputFilterable;

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::AUTO_SLUG;
    }

    public function update(Ad $ad, array $input): Ad
    {
        $relevantInput = $this->filterRelevantInput($ad->autoAd, $input);
        if (0 === count($relevantInput)) {
            return $ad;
        }

        if (array_key_exists('options', $relevantInput)) {
            $ad->autoAd->autoOptions()->sync($relevantInput['options']);
            unset($relevantInput['options']);
        }

        if (0 === count($relevantInput)) {
            return $ad;
        }

        $ad->autoAd->update($relevantInput);

        return $ad;
    }

    public function updateFull(Ad $ad, array $input): Ad
    {
        return $ad;
    }

    public function getSpecificAdFromAd(Ad $ad): Model
    {
        return $ad->autoAd;
    }

    public function presentForm(string $slug): View
    {
        return view('auto-ad.auto-ad-edit', compact('slug'));
    }
}
