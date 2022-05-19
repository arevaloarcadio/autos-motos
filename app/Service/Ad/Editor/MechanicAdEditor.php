<?php

declare(strict_types=1);

namespace App\Service\Ad\Editor;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Ad;
use App\Service\Ad\AdImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Editor
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MechanicAdEditor implements IAdEditor
{
    use InputFilterable;

    /**
     * @var AdImageService
     */
    private $adImageService;

    public function __construct(AdImageService $adImageService)
    {
        $this->adImageService = $adImageService;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::MECHANIC_SLUG;
    }

    public function update(Ad $ad, array $input): Ad
    {
        return $ad;
    }

    public function updateFull(Ad $ad, array $input): Ad
    {
        $ad->title                       = $input['title'];
        $ad->description                 = $input['description'];
        $ad->market_id                   = $input['market_id'];
        $ad->mechanicAd->address         = $input['mechanic_ad']['address'];
        $ad->mechanicAd->zip_code        = $input['mechanic_ad']['zip_code'];
        $ad->mechanicAd->city            = $input['mechanic_ad']['city'];
        $ad->mechanicAd->country         = $input['mechanic_ad']['country'];
        $ad->mechanicAd->latitude        = $input['mechanic_ad']['latitude'];
        $ad->mechanicAd->longitude       = $input['mechanic_ad']['longitude'];
        $ad->mechanicAd->mobile_number   = $input['mechanic_ad']['mobile_number'];
        $ad->mechanicAd->whatsapp_number = $input['mechanic_ad']['whatsapp_number'] ?? null;
        $ad->mechanicAd->website_url     = $input['mechanic_ad']['website_url'] ?? null;

        if (isset($input['images']) && 0 < count($input['images'])) {
            $this->adImageService->updateImages($ad, $input['images']);
        }

        $ad->save();
        $ad->mechanicAd->save();

        return $ad;
    }

    public function getSpecificAdFromAd(Ad $ad): Model
    {
        return $ad->mechanicAd;
    }

    public function presentForm(string $slug): View
    {
        return view('mechanic-ad.mechanic-ad-edit', compact('slug'));
    }
}
