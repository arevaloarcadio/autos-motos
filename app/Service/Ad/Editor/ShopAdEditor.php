<?php

declare(strict_types=1);

namespace App\Service\Ad\Editor;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Ad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @package App\Service\Ad\Editor
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class ShopAdEditor implements IAdEditor
{
    use InputFilterable;

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::SHOP_SLUG;
    }

    public function update(Ad $ad, array $input): Ad
    {
        $relevantInput = $this->filterRelevantInput($ad->shopAd, $input);
        if (0 === count($relevantInput)) {
            return $ad;
        }

        $ad->shopAd->update($relevantInput);

        return $ad;
    }

    public function updateFull(Ad $ad, array $input): Ad
    {
        return $ad;
    }

    public function getSpecificAdFromAd(Ad $ad): Model
    {
        return $ad->shopAd;
    }

    public function presentForm(string $slug): View
    {
        throw new NotFoundHttpException();
    }
}
