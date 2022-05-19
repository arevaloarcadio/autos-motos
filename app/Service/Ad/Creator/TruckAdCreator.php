<?php

declare(strict_types=1);

namespace App\Service\Ad\Creator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\MobileHomeAd;
use App\Models\MotoAd;
use App\Models\TruckAd;
use App\Models\Dealer;
use App\Models\DealerShowRoom;
use App\Models\User;
use App\Service\Ad\AdCreateService;
use App\Service\Ad\Validator\MobileHomeAdValidator;
use App\Service\Ad\Validator\TruckAdValidator;
use App\Service\Market\MarketStorageFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TruckAdCreator implements IAdCreator
{
    /**
     * @var TruckAdValidator
     */
    private $validator;

    /**
     * @var AdCreateService
     */
    private $adCreateService;

    public function __construct(AdCreateService $adCreateService, TruckAdValidator $validator)
    {
        $this->validator       = $validator;
        $this->adCreateService = $adCreateService;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::TRUCK_SLUG;
    }

    public function presentForm(): View
    {
        return view('truck-ad.truck-ad-create');
    }

    public function create(array $input): Ad
    {
        $input = $this->enrichInputBasedOnUserInformation($input);
        $input = $this->validator->validate($input);
        $ad    = $this->adCreateService->create($input);
        $this->createTruckAd($ad, $input['truck_ad']);

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
                $input['truck_ad']['dealer_id'] = $currentUser->dealer->id;
                $showRoomId                     = $input['truck_ad']['dealer_show_room_id'];
                $selectedShowRoom               = $currentUser->dealer->showRooms->first(
                    function (DealerShowRoom $showRoom) use ($showRoomId) {
                        return $showRoom->id === $showRoomId;
                    }
                );
                $input['market_id']             = $selectedShowRoom->market_id;
            }
        }

        $input['market_id'] = $input['market_id'] ?? MarketStorageFacade::getMarket()->id;

        return $input;
    }

    /**
     * @param Ad    $ad
     * @param array $input
     *
     * @return TruckAd
     */
    private function createTruckAd(Ad $ad, array $input): TruckAd
    {
        $truckAd             = new TruckAd();
        $truckAd->truck_type = $input['truck_type'];
        $truckAd->make()->associate($input['make_id']);
        $truckAd->custom_make              = $input['custom_make'] ?? null;
        $truckAd->model                    = $input['model'];
        $truckAd->first_registration_year  = isset($input['first_registration_year']) ? intval(
            $input['first_registration_year']
        ) : null;
        $truckAd->first_registration_month = isset($input['first_registration_month']) ? intval(
            $input['first_registration_month']
        ) : null;
        $truckAd->vehicleCategory()->associate($input['vehicle_category_id']);
        $truckAd->construction_year = isset($input['construction_year']) ? intval(
            $input['construction_year']
        ) : null;
        $truckAd->fuelType()->associate($input['fuel_type_id']);
        $truckAd->transmissionType()->associate($input['transmission_type_id']);
        $truckAd->fuel_consumption             = isset($input['fuel_consumption']) ? floatval(
            $input['fuel_consumption']
        ) : null;
        $truckAd->power_kw                     = isset($input['power_kw']) ? intval(
            trim($input['power_kw'])
        ) : null;
        $truckAd->emission_class               = $input['emission_class'] ?? null;
        $truckAd->co2_emissions                = isset($input['co2_emissions']) ? floatval(
            $input['co2_emissions']
        ) : null;
        $truckAd->mileage                      = $input['mileage'] ?? null;
        $truckAd->cab                          = $input['cab'] ?? null;
        $truckAd->seats                        = isset($input['seats']) ? intval(trim($input['seats'])) : null;
        $truckAd->loading_space_length_mm      = isset($input['loading_space_length_mm']) ? floatval(
            trim($input['loading_space_length_mm'])
        ) : null;
        $truckAd->loading_space_width_mm       = isset($input['loading_space_width_mm']) ? floatval(
            trim($input['loading_space_width_mm'])
        ) : null;
        $truckAd->loading_space_height_mm      = isset($input['loading_space_height_mm']) ? floatval(
            trim($input['loading_space_height_mm'])
        ) : null;
        $truckAd->loading_volume_m3            = isset($input['loading_volume_m3']) ? floatval(
            trim($input['loading_volume_m3'])
        ) : null;
        $truckAd->payload_kg                   = isset($input['payload_kg']) ? floatval(
            trim($input['payload_kg'])
        ) : null;
        $truckAd->empty_weight_kg              = isset($input['empty_weight_kg']) ? floatval(
            trim($input['empty_weight_kg'])
        ) : null;
        $truckAd->permanent_total_weight_kg    = isset($input['permanent_total_weight_kg']) ? floatval(
            trim($input['permanent_total_weight_kg'])
        ) : null;
        $truckAd->allowed_pulling_weight_kg    = isset($input['allowed_pulling_weight_kg']) ? floatval(
            trim($input['allowed_pulling_weight_kg'])
        ) : null;
        $truckAd->axes                         = isset($input['axes']) ? intval(trim($input['axes'])) : null;
        $truckAd->construction_height_mm       = isset($input['construction_height_mm']) ? floatval(
            trim($input['construction_height_mm'])
        ) : null;
        $truckAd->lifting_height_mm            = isset($input['lifting_height_mm']) ? floatval(
            trim($input['lifting_height_mm'])
        ) : null;
        $truckAd->load_capacity_kg             = isset($input['load_capacity_kg']) ? floatval(
            trim($input['load_capacity_kg'])
        ) : null;
        $truckAd->operating_hours              = isset($input['operating_hours']) ? intval(
            trim($input['operating_hours'])
        ) : null;
        $truckAd->operating_weight_kg          = isset($input['operating_weight_kg']) ? intval(
            trim($input['operating_weight_kg'])
        ) : null;
        $truckAd->lifting_capacity_kg_m        = isset($input['lifting_capacity_kg_m']) ? intval(
            trim($input['lifting_capacity_kg_m'])
        ) : null;
        $truckAd->max_weight_allowed_kg        = isset($input['max_weight_allowed_kg']) ? floatval(
            trim($input['max_weight_allowed_kg'])
        ) : null;
        $truckAd->wheel_formula                = $input['wheel_formula'] ?? null;
        $truckAd->hydraulic_system             = $input['hydraulic_system'] ?? null;
        $truckAd->interior_color               = $input['interior_color'] ?? null;
        $truckAd->exterior_color               = $input['exterior_color'] ?? null;
        $truckAd->owners                       = isset($input['owners']) ? intval(trim($input['owners'])) : null;
        $truckAd->condition                    = $input['condition'];
        $truckAd->inspection_valid_until_month = isset($input['inspection_valid_until_month']) ? intval(
            $input['inspection_valid_until_month']
        ) : null;
        $truckAd->inspection_valid_until_year  = isset($input['inspection_valid_until_year']) ? intval(
            $input['inspection_valid_until_year']
        ) : null;
        $truckAd->price                        = $input['price'];
        $truckAd->price_contains_vat           = $input['price_contains_vat'];
        $truckAd->first_name                   = $input['first_name'] ?? null;
        $truckAd->last_name                    = $input['last_name'] ?? null;
        $truckAd->email_address                = $input['email_address'];
        $truckAd->address                      = $input['address'];
        $truckAd->zip_code                     = $input['zip_code'];
        $truckAd->city                         = $input['city'];
        $truckAd->country                      = $input['country'];
        $truckAd->mobile_number                = $input['mobile_number'];
        $truckAd->landline_number              = $input['landline_number'] ?? null;
        $truckAd->whatsapp_number              = $input['whatsapp_number'] ?? null;
        $truckAd->youtube_link                 = $input['youtube_link'] ?? null;

        if (isset($input['dealer_id'])) {
            $truckAd->dealer()->associate($input['dealer_id']);
            $truckAd->dealerShowRoom()->associate($input['dealer_show_room_id']);
        }
        $truckAd->ad()->associate($ad);

        $truckAd->save();
        $truckAd->options()->sync($input['options']);

        return $truckAd;
    }

    public function getInputKey(): string
    {
        return 'truck_ad';
    }
}
