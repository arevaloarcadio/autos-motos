<?php

declare(strict_types=1);

namespace App\Service\Ad\Creator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\ShopAd;
use App\Models\Dealer;
use App\Models\DealerShowRoom;
use App\Models\User;
use App\Service\Ad\AdCreateService;
use App\Service\Ad\Validator\ShopAdValidator;
use App\Service\Market\MarketStorageFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class ShopAdCreator implements IAdCreator
{
    /**
     * @var ShopAdValidator
     */
    private $validator;

    /**
     * @var AdCreateService
     */
    private $adCreateService;

    public function __construct(AdCreateService $adCreateService, ShopAdValidator $validator)
    {
        $this->validator       = $validator;
        $this->adCreateService = $adCreateService;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::SHOP_SLUG;
    }

    public function presentForm(): View
    {
        return view('shop-ad.shop-ad-create');
    }

    public function create(array $input): Ad
    {
        $startTime = microtime(true);
        $input     = $this->enrichInputBasedOnUserInformation($input);
        $input     = $this->validator->validate($input);
        $ad        = $this->adCreateService->create($input);
        $this->createShopAd($ad, $input['shop_ad']);

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
                $input['shop_ad']['dealer_id'] = $currentUser->dealer->id;
                $showRoomId                    = $input['shop_ad']['dealer_show_room_id'];
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
     * @return ShopAd
     */
    private function createShopAd(Ad $ad, array $input): ShopAd
    {
        $shopAd                     = new ShopAd();
        $shopAd->manufacturer       = $input['manufacturer'];
        $shopAd->category           = $input['category'];
        $shopAd->code               = $input['code'] ?? null;
        $shopAd->model              = $input['model'] ?? null;
        $shopAd->condition          = $input['condition'];
        $shopAd->price              = $input['price'];
        $shopAd->price_contains_vat = $input['price_contains_vat'];
        $shopAd->first_name         = $input['first_name'] ?? null;
        $shopAd->last_name          = $input['last_name'] ?? null;
        $shopAd->email_address      = $input['email_address'];
        $shopAd->address            = $input['address'];
        $shopAd->zip_code           = $input['zip_code'];
        $shopAd->city               = $input['city'];
        $shopAd->country            = $input['country'];
        $shopAd->mobile_number      = $input['mobile_number'];
        $shopAd->landline_number    = $input['landline_number'] ?? null;
        $shopAd->whatsapp_number    = $input['whatsapp_number'] ?? null;
        $shopAd->youtube_link       = $input['youtube_link'] ?? null;
        $shopAd->make()->associate($input['make_id']);
        if (isset($input['dealer_id'])) {
            $shopAd->dealer()->associate($input['dealer_id']);
            $shopAd->dealerShowRoom()->associate($input['dealer_show_room_id']);
        }
        $shopAd->ad()->associate($ad);

        $shopAd->save();

        return $shopAd;
    }

    public function getInputKey(): string
    {
        return 'shop_ad';
    }
}
