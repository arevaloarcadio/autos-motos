<?php

declare(strict_types=1);

namespace App\Service\Ad\Creator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\AutoAd;
use App\Models\Dealer;
use App\Models\DealerShowRoom;
use App\Models\User;
use App\Service\Ad\AdCreateService;
use App\Service\Ad\Validator\AutoAdValidator;
use App\Service\Market\MarketStorageFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AutoAdCreator implements IAdCreator
{
    /**
     * @var AutoAdValidator
     */
    private $validator;

    /**
     * @var AdCreateService
     */
    private $adCreateService;

    public function __construct(AdCreateService $adCreateService, AutoAdValidator $validator)
    {
        $this->validator       = $validator;
        $this->adCreateService = $adCreateService;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::AUTO_SLUG;
    }

    public function presentForm(): View
    {
        return view('auto-ad.auto-ad-create');
    }

    public function create(array $input): Ad
    {
        $startTime = microtime(true);
        $input     = $this->enrichInputBasedOnUserInformation($input);
        $input     = $this->validator->validate($input);
        $ad        = $this->adCreateService->create($input);
        $this->createAutoAd($ad, $input['auto_ad']);

        $endTime       = microtime(true);
        $executionTime = ($endTime - $startTime);
        Log::info(sprintf('Ad %s created in %d seconds', $ad->id, $executionTime));

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
                $input['auto_ad']['dealer_id'] = $currentUser->dealer->id;
                $showRoomId                    = $input['auto_ad']['dealer_show_room_id'];
                /** @var DealerShowRoom $selectedShowRoom */
                $selectedShowRoom              = $currentUser->dealer->showRooms->first(
                    function (DealerShowRoom $showRoom) use ($showRoomId) {
                        return $showRoom->id === $showRoomId;
                    }
                );
                $input['market_id']            = $selectedShowRoom->market_id;
                $input['auto_ad']['latitude']  = $selectedShowRoom->latitude;
                $input['auto_ad']['longitude'] = $selectedShowRoom->longitude;
            }
        }

        $input['market_id'] = $input['market_id'] ?? MarketStorageFacade::getMarket()->id;

        return $input;
    }

    /**
     * @param Ad    $ad
     * @param array $input
     *
     * @return AutoAd
     */
    private function createAutoAd(Ad $ad, array $input): AutoAd
    {
        $autoAd                               = new AutoAd();
        $autoAd->mileage                      = $input['mileage'];
        $autoAd->first_registration_year      = intval($input['first_registration_year']);
        $autoAd->first_registration_month     = intval($input['first_registration_month']);
        $autoAd->additional_vehicle_info      = $input['additional_vehicle_info'] ?? null;
        $autoAd->interior_color               = $input['interior_color'];
        $autoAd->exterior_color               = $input['exterior_color'];
        $autoAd->condition                    = $input['condition'];
        $autoAd->doors                        = isset($input['doors']) ? intval(trim($input['doors'])) : null;
        $autoAd->seats                        = isset($input['seats']) ? intval(trim($input['seats'])) : null;
        $autoAd->price                        = $input['price'];
        $autoAd->price_contains_vat           = $input['price_contains_vat'];
        $autoAd->first_name                   = $input['first_name'] ?? null;
        $autoAd->last_name                    = $input['last_name'] ?? null;
        $autoAd->email_address                = $input['email_address'];
        $autoAd->address                      = $input['address'];
        $autoAd->zip_code                     = $input['zip_code'];
        $autoAd->city                         = $input['city'];
        $autoAd->country                      = $input['country'];
        $autoAd->mobile_number                = $input['mobile_number'];
        $autoAd->landline_number              = $input['landline_number'] ?? null;
        $autoAd->whatsapp_number              = $input['whatsapp_number'] ?? null;
        $autoAd->youtube_link                 = $input['youtube_link'] ?? null;
        $autoAd->engine_displacement          = $input['engine_displacement'] ?? null;
        $autoAd->power_hp                     = isset($input['power_hp']) ? intval(trim($input['power_hp'])) : null;
        $autoAd->fuel_consumption             = isset($input['fuel_consumption']) ? floatval(
            $input['fuel_consumption']
        ) : null;
        $autoAd->co2_emissions                = isset($input['co2_emissions']) ? floatval(
            $input['co2_emissions']
        ) : null;
        $autoAd->owners                       = isset($input['owners']) ? intval(trim($input['owners'])) : null;
        $autoAd->vin                          = $input['vin'] ?? null;
        $autoAd->inspection_valid_until_month = isset($input['inspection_valid_until_month']) ? intval(
            $input['inspection_valid_until_month']
        ) : null;
        $autoAd->inspection_valid_until_year  = isset($input['inspection_valid_until_year']) ? intval(
            $input['inspection_valid_until_year']
        ) : null;
        $autoAd->make()->associate($input['make_id']);
        $autoAd->model()->associate($input['model_id']);
        $autoAd->generation()->associate($input['generation_id']);
        $autoAd->series()->associate($input['series_id']);
        $autoAd->trim()->associate($input['trim_id']);
        $autoAd->equipment()->associate($input['equipment_id']);
        $autoAd->bodyType()->associate($input['ad_body_type_id']);
        $autoAd->fuelType()->associate($input['ad_fuel_type_id']);
        $autoAd->transmissionType()->associate($input['ad_transmission_type_id']);
        $autoAd->driveType()->associate($input['ad_drive_type_id']);
        if (isset($input['dealer_id'])) {
            $autoAd->dealer()->associate($input['dealer_id']);
            $autoAd->dealerShowRoom()->associate($input['dealer_show_room_id']);
        }
        $autoAd->ad()->associate($ad);

        $autoAd->save();
        $autoAd->autoOptions()->sync($input['options']);

        return $autoAd;
    }

    public function getInputKey(): string
    {
        return 'auto_ad';
    }
}
