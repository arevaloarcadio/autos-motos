<?php

declare(strict_types=1);

namespace App\Service\Ad\Creator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\RentalAd;
use App\Models\User;
use App\Service\Ad\AdCreateService;
use App\Service\Ad\Validator\RentalAdValidator;
use App\Service\Market\MarketStorageFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class RentalAdCreator implements IAdCreator
{
    /**
     * @var RentalAdValidator
     */
    private $validator;

    /**
     * @var AdCreateService
     */
    private $adCreateService;

    public function __construct(AdCreateService $adCreateService, RentalAdValidator $validator)
    {
        $this->validator       = $validator;
        $this->adCreateService = $adCreateService;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::RENTAL_SLUG;
    }

    public function presentForm(): View
    {
        return view('rental-ad.rental-ad-create');
    }

    public function create(array $input): Ad
    {
        $input = $this->enrichInputBasedOnUserInformation($input);
        $input = $this->validator->validate($input);
        $ad    = $this->adCreateService->create($input);
        $this->createRentalAd($ad, $input['rental_ad']);

        return $ad;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    private function enrichInputBasedOnUserInformation(array $input): array
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (false === $currentUser->hasRole(RoleEnum::ADMIN)) {
            $input['user_id'] = $currentUser->id;
        }

        $input['market_id'] = $input['market_id'] ?? MarketStorageFacade::getMarket()->id;

        return $input;
    }

    /**
     * @param Ad    $ad
     * @param array $input
     *
     * @return RentalAd
     */
    private function createRentalAd(Ad $ad, array $input): RentalAd
    {
        $rentalAd                  = new RentalAd();
        $rentalAd->address         = $input['address'];
        $rentalAd->zip_code        = $input['zip_code'];
        $rentalAd->city            = $input['city'];
        $rentalAd->country         = $input['country'];
        $rentalAd->latitude        = $input['latitude'] ?? null;
        $rentalAd->longitude       = $input['longitude'] ?? null;
        $rentalAd->mobile_number   = $input['mobile_number'];
        $rentalAd->whatsapp_number = $input['whatsapp_number'] ?? null;
        $rentalAd->website_url     = $input['website_url'] ?? null;
        $rentalAd->email_address   = $input['email_address'] ?? null;
        $rentalAd->ad()->associate($ad);

        $rentalAd->save();

        return $rentalAd;
    }

    public function getInputKey(): string
    {
        return 'rental_ad';
    }
}
