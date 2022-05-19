<?php

declare(strict_types=1);

namespace App\Service\Ad\Creator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\MotoAd;
use App\Models\Dealer;
use App\Models\DealerShowRoom;
use App\Models\User;
use App\Service\Ad\AdCreateService;
use App\Service\Ad\Validator\MotoAdValidator;
use App\Service\Market\MarketStorageFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MotoAdCreator implements IAdCreator
{
    /**
     * @var MotoAdValidator
     */
    private $validator;

    /**
     * @var AdCreateService
     */
    private $adCreateService;

    public function __construct(AdCreateService $adCreateService, MotoAdValidator $validator)
    {
        $this->validator       = $validator;
        $this->adCreateService = $adCreateService;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::MOTO_SLUG;
    }

    public function presentForm(): View
    {
        return view('moto-ad.moto-ad-create');
    }

    public function create(array $input): Ad
    {
        $input = $this->enrichInputBasedOnUserInformation($input);
        $input = $this->validator->validate($input);
        $ad    = $this->adCreateService->create($input);
        $this->createMotoAd($ad, $input['moto_ad']);

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
                $input['moto_ad']['dealer_id'] = $currentUser->dealer->id;
                $showRoomId                    = $input['moto_ad']['dealer_show_room_id'];
                $selectedShowRoom              = $currentUser->dealer->showRooms->first(
                    function (DealerShowRoom $showRoom) use ($showRoomId) {
                        return $showRoom->id === $showRoomId;
                    }
                );
                $input['market_id']            = $selectedShowRoom->market_id;
            }
        }

        $input['market_id'] = $input['market_id'] ?? MarketStorageFacade::getMarket()->id;

        return $input;
    }

    /**
     * @param Ad    $ad
     * @param array $input
     *
     * @return MotoAd
     */
    private function createMotoAd(Ad $ad, array $input): MotoAd
    {
        $motoAd                               = new MotoAd();
        $motoAd->custom_make                  = $input['custom_make'] ?? null;
        $motoAd->custom_model                 = $input['custom_model'] ?? null;
        $motoAd->mileage                      = $input['mileage'];
        $motoAd->first_registration_year      = intval($input['first_registration_year']);
        $motoAd->first_registration_month     = intval($input['first_registration_month']);
        $motoAd->color                        = $input['color'];
        $motoAd->condition                    = $input['condition'];
        $motoAd->price                        = $input['price'];
        $motoAd->price_contains_vat           = $input['price_contains_vat'];
        $motoAd->first_name                   = $input['first_name'] ?? null;
        $motoAd->last_name                    = $input['last_name'] ?? null;
        $motoAd->email_address                = $input['email_address'];
        $motoAd->address                      = $input['address'];
        $motoAd->zip_code                     = $input['zip_code'];
        $motoAd->city                         = $input['city'];
        $motoAd->country                      = $input['country'];
        $motoAd->mobile_number                = $input['mobile_number'];
        $motoAd->landline_number              = $input['landline_number'] ?? null;
        $motoAd->whatsapp_number              = $input['whatsapp_number'] ?? null;
        $motoAd->youtube_link                 = $input['youtube_link'] ?? null;
        $motoAd->engine_displacement          = $input['engine_displacement'] ?? null;
        $motoAd->emission_class               = $input['emission_class'] ?? null;
        $motoAd->power_kw                     = isset($input['power_kw']) ? intval(trim($input['power_kw'])) : null;
        $motoAd->gears                        = isset($input['gears']) ? intval(trim($input['gears'])) : null;
        $motoAd->cylinders                    = isset($input['cylinders']) ? intval(trim($input['cylinders'])) : null;
        $motoAd->weight_kg                    = isset($input['weight_kg']) ? floatval(trim($input['weight_kg'])) : null;
        $motoAd->fuel_consumption             = isset($input['fuel_consumption']) ? floatval(
            $input['fuel_consumption']
        ) : null;
        $motoAd->co2_emissions                = isset($input['co2_emissions']) ? floatval(
            $input['co2_emissions']
        ) : null;
        $motoAd->owners                       = isset($input['owners']) ? intval(trim($input['owners'])) : null;
        $motoAd->inspection_valid_until_month = isset($input['inspection_valid_until_month']) ? intval(
            $input['inspection_valid_until_month']
        ) : null;
        $motoAd->inspection_valid_until_year  = isset($input['inspection_valid_until_year']) ? intval(
            $input['inspection_valid_until_year']
        ) : null;
        $motoAd->last_customer_service_month  = isset($input['last_customer_service_month']) ? intval(
            $input['last_customer_service_month']
        ) : null;
        $motoAd->last_customer_service_year   = isset($input['last_customer_service_year']) ? intval(
            $input['last_customer_service_year']
        ) : null;
        $motoAd->make()->associate($input['make_id']);
        $motoAd->model()->associate($input['model_id']);
        $motoAd->bodyType()->associate($input['body_type_id']);
        $motoAd->fuelType()->associate($input['fuel_type_id']);
        $motoAd->transmissionType()->associate($input['transmission_type_id']);
        $motoAd->driveType()->associate($input['drive_type_id']);
        if (isset($input['dealer_id'])) {
            $motoAd->dealer()->associate($input['dealer_id']);
            $motoAd->dealerShowRoom()->associate($input['dealer_show_room_id']);
        }
        $motoAd->ad()->associate($ad);

        $motoAd->save();
        $motoAd->options()->sync($input['options']);

        return $motoAd;
    }

    public function getInputKey(): string
    {
        return 'moto_ad';
    }
}
