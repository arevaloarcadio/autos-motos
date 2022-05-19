<?php

declare(strict_types=1);

namespace App\Service\Ad\Creator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\MechanicAd;
use App\Models\Dealer;
use App\Models\DealerShowRoom;
use App\Models\User;
use App\Service\Ad\AdCreateService;
use App\Service\Ad\Validator\MechanicAdValidator;
use App\Service\Market\MarketStorageFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MechanicAdCreator implements IAdCreator
{
    /**
     * @var MechanicAdValidator
     */
    private $validator;

    /**
     * @var AdCreateService
     */
    private $adCreateService;

    public function __construct(AdCreateService $adCreateService, MechanicAdValidator $validator)
    {
        $this->validator       = $validator;
        $this->adCreateService = $adCreateService;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::MECHANIC_SLUG;
    }

    public function presentForm(): View
    {
        return view('mechanic-ad.mechanic-ad-create');
    }

    public function create(array $input): Ad
    {
        $input = $this->enrichInputBasedOnUserInformation($input);
        $input = $this->validator->validate($input);
        $ad    = $this->adCreateService->create($input);
        $this->createMechanicAd($ad, $input['mechanic_ad']);

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
     * @return MechanicAd
     */
    private function createMechanicAd(Ad $ad, array $input): MechanicAd
    {
        $mechanicAd                  = new MechanicAd();
        $mechanicAd->address         = $input['address'];
        $mechanicAd->zip_code        = $input['zip_code'];
        $mechanicAd->city            = $input['city'];
        $mechanicAd->country         = $input['country'];
        $mechanicAd->latitude        = $input['latitude'] ?? null;
        $mechanicAd->longitude       = $input['longitude'] ?? null;
        $mechanicAd->mobile_number   = $input['mobile_number'];
        $mechanicAd->whatsapp_number = $input['whatsapp_number'] ?? null;
        $mechanicAd->website_url     = $input['website_url'] ?? null;
        $mechanicAd->email_address   = $input['email_address'] ?? null;
        $mechanicAd->ad()->associate($ad);

        $mechanicAd->save();

        return $mechanicAd;
    }

    public function getInputKey(): string
    {
        return 'mechanic_ad';
    }
}
