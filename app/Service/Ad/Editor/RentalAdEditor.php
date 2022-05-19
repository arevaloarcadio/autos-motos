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
class RentalAdEditor implements IAdEditor
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
        return $adType === AdTypeEnum::RENTAL_SLUG;
    }

    public function update(Ad $ad, array $input): Ad
    {
        return $ad;
    }

    public function updateFull(Ad $ad, array $input): Ad
    {
        $ad->title                     = $input['title'];
        $ad->description               = $input['description'];
        $ad->market_id                 = $input['market_id'];
        $ad->rentalAd->address         = $input['rental_ad']['address'];
        $ad->rentalAd->zip_code        = $input['rental_ad']['zip_code'];
        $ad->rentalAd->city            = $input['rental_ad']['city'];
        $ad->rentalAd->country         = $input['rental_ad']['country'];
        $ad->rentalAd->latitude        = $input['rental_ad']['latitude'];
        $ad->rentalAd->longitude       = $input['rental_ad']['longitude'];
        $ad->rentalAd->mobile_number   = $input['rental_ad']['mobile_number'];
        $ad->rentalAd->whatsapp_number = $input['rental_ad']['whatsapp_number'] ?? null;
        $ad->rentalAd->website_url     = $input['rental_ad']['website_url'] ?? null;

        if (isset($input['images']) && 0 < count($input['images'])) {
            $this->adImageService->updateImages($ad, $input['images']);
        }

        $ad->save();
        $ad->rentalAd->save();

        return $ad;
    }

    public function getSpecificAdFromAd(Ad $ad): Model
    {
        return $ad->rentalAd;
    }

    public function presentForm(string $slug): View
    {
        return view('rental-ad.rental-ad-edit', compact('slug'));
    }
}
