<?php

declare(strict_types=1);

namespace App\Service\Ad\Creator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\MobileHomeAd;
use App\Models\MotoAd;
use App\Models\Dealer;
use App\Models\DealerShowRoom;
use App\Models\User;
use App\Service\Ad\AdCreateService;
use App\Service\Ad\Validator\MobileHomeAdValidator;
use App\Service\Market\MarketStorageFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MobileHomeAdCreator implements IAdCreator
{
    /**
     * @var MobileHomeAdValidator
     */
    private $validator;

    /**
     * @var AdCreateService
     */
    private $adCreateService;

    public function __construct(AdCreateService $adCreateService, MobileHomeAdValidator $validator)
    {
        $this->validator       = $validator;
        $this->adCreateService = $adCreateService;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::MOBILE_HOME_SLUG;
    }

    public function presentForm(): View
    {
        return view('mobile-home-ad.mobile-home-ad-create');
    }

    public function create(array $input): Ad
    {
        $input = $this->enrichInputBasedOnUserInformation($input);
        $input = $this->validator->validate($input);
        $ad    = $this->adCreateService->create($input);
        $this->createMobileHomeAd($ad, $input['mobile_home_ad']);

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

            if ($currentUser->dealer instanceof Dealer) {
                $input['mobile_home_ad']['dealer_id'] = $currentUser->dealer->id;
                $showRoomId                           = $input['mobile_home_ad']['dealer_show_room_id'];
                $selectedShowRoom                     = $currentUser->dealer->showRooms->first(
                    function (DealerShowRoom $showRoom) use ($showRoomId) {
                        return $showRoom->id === $showRoomId;
                    }
                );
                $input['market_id']                   = $selectedShowRoom->market_id;
            }
        }

        $input['market_id'] = $input['market_id'] ?? MarketStorageFacade::getMarket()->id;

        return $input;
    }

    /**
     * @param Ad    $ad
     * @param array $input
     *
     * @return MobileHomeAd
     */
    private function createMobileHomeAd(Ad $ad, array $input): MobileHomeAd
    {
        $mobileHomeAd                               = new MobileHomeAd();
        $mobileHomeAd->custom_make                  = $input['custom_make'] ?? null;
        $mobileHomeAd->custom_model                 = $input['custom_model'] ?? null;
        $mobileHomeAd->mileage                      = $input['mileage'];
        $mobileHomeAd->construction_year            = intval($input['construction_year']);
        $mobileHomeAd->first_registration_year      = intval($input['first_registration_year']);
        $mobileHomeAd->first_registration_month     = intval($input['first_registration_month']);
        $mobileHomeAd->color                        = $input['color'];
        $mobileHomeAd->condition                    = $input['condition'];
        $mobileHomeAd->price                        = $input['price'];
        $mobileHomeAd->price_contains_vat           = $input['price_contains_vat'];
        $mobileHomeAd->first_name                   = $input['first_name'] ?? null;
        $mobileHomeAd->last_name                    = $input['last_name'] ?? null;
        $mobileHomeAd->email_address                = $input['email_address'];
        $mobileHomeAd->address                      = $input['address'];
        $mobileHomeAd->zip_code                     = $input['zip_code'];
        $mobileHomeAd->city                         = $input['city'];
        $mobileHomeAd->country                      = $input['country'];
        $mobileHomeAd->mobile_number                = $input['mobile_number'];
        $mobileHomeAd->landline_number              = $input['landline_number'] ?? null;
        $mobileHomeAd->whatsapp_number              = $input['whatsapp_number'] ?? null;
        $mobileHomeAd->youtube_link                 = $input['youtube_link'] ?? null;
        $mobileHomeAd->engine_displacement          = $input['engine_displacement'] ?? null;
        $mobileHomeAd->emission_class               = $input['emission_class'] ?? null;
        $mobileHomeAd->power_kw                     = isset($input['power_kw']) ? intval(
            trim($input['power_kw'])
        ) : null;
        $mobileHomeAd->seats                        = isset($input['seats']) ? intval(trim($input['seats'])) : null;
        $mobileHomeAd->sleeping_places              = isset($input['sleeping_places']) ? intval(
            trim($input['sleeping_places'])
        ) : null;
        $mobileHomeAd->beds                         = $input['beds'] ?? null;
        $mobileHomeAd->length_cm                    = isset($input['length_cm']) ? floatval(
            trim($input['length_cm'])
        ) : null;
        $mobileHomeAd->width_cm                     = isset($input['width_cm']) ? floatval(
            trim($input['width_cm'])
        ) : null;
        $mobileHomeAd->height_cm                    = isset($input['height_cm']) ? floatval(
            trim($input['height_cm'])
        ) : null;
        $mobileHomeAd->payload_kg                   = isset($input['payload_kg']) ? floatval(
            trim($input['payload_kg'])
        ) : null;
        $mobileHomeAd->max_weight_allowed_kg        = isset($input['max_weight_allowed_kg']) ? floatval(
            trim($input['max_weight_allowed_kg'])
        ) : null;
        $mobileHomeAd->axes                         = isset($input['axes']) ? intval(trim($input['axes'])) : null;
        $mobileHomeAd->fuel_consumption             = isset($input['fuel_consumption']) ? floatval(
            $input['fuel_consumption']
        ) : null;
        $mobileHomeAd->co2_emissions                = isset($input['co2_emissions']) ? floatval(
            $input['co2_emissions']
        ) : null;
        $mobileHomeAd->owners                       = isset($input['owners']) ? intval(trim($input['owners'])) : null;
        $mobileHomeAd->inspection_valid_until_month = isset($input['inspection_valid_until_month']) ? intval(
            $input['inspection_valid_until_month']
        ) : null;
        $mobileHomeAd->inspection_valid_until_year  = isset($input['inspection_valid_until_year']) ? intval(
            $input['inspection_valid_until_year']
        ) : null;
        $mobileHomeAd->make()->associate($input['make_id']);
        $mobileHomeAd->model()->associate($input['model_id']);
        $mobileHomeAd->vehicleCategory()->associate($input['vehicle_category_id']);
        $mobileHomeAd->fuelType()->associate($input['fuel_type_id']);
        $mobileHomeAd->transmissionType()->associate($input['transmission_type_id']);
        if (isset($input['dealer_id'])) {
            $mobileHomeAd->dealer()->associate($input['dealer_id']);
            $mobileHomeAd->dealerShowRoom()->associate($input['dealer_show_room_id']);
        }
        $mobileHomeAd->ad()->associate($ad);

        $mobileHomeAd->save();
        $mobileHomeAd->options()->sync($input['options']);

        return $mobileHomeAd;
    }

    public function getInputKey(): string
    {
        return 'mobile_home_ad';
    }
}
