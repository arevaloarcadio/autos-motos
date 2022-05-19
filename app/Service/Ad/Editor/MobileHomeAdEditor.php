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
class MobileHomeAdEditor implements IAdEditor
{
    use InputFilterable;

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::MOBILE_HOME_SLUG;
    }

    public function update(Ad $ad, array $input): Ad
    {
        $relevantInput = $this->filterRelevantInput($ad->mobileHomeAd, $input);
        if (0 === count($relevantInput)) {
            return $ad;
        }

        if (array_key_exists('options', $relevantInput)) {
            $ad->mobileHomeAd->options()->sync($relevantInput['options']);
            unset($relevantInput['options']);
        }

        if (0 === count($relevantInput)) {
            return $ad;
        }

        $ad->mobileHomeAd->update($relevantInput);

        return $ad;
    }

    public function updateFull(Ad $ad, array $input): Ad
    {
        return $ad;
    }

    public function getSpecificAdFromAd(Ad $ad): Model
    {
        return $ad->mobileHomeAd;
    }

    public function presentForm(string $slug): View
    {
        return view('mobile-home-ad.mobile-home-ad-edit', compact('slug'));
    }
}
