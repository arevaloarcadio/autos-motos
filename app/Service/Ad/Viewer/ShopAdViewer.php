<?php

declare(strict_types=1);

namespace App\Service\Ad\Viewer;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\ConditionEnum;
use App\Models\Ad;
use App\Models\Dealer;
use App\Service\Ad\Finder\ShopAdFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Viewer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class ShopAdViewer implements IAdViewer
{
    use ExcludeCurrentAdTrait;

    private const RELATED_ADS_PRICE_RANGE_DEVIATION = 200;
    private const RELATED_ADS_ITEMS_PER_PAGE        = 11;

    /**
     * @var ShopAdFinder
     */
    private $finder;

    public function __construct(ShopAdFinder $finder)
    {
        $this->finder = $finder;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::SHOP_SLUG;
    }

    public function presentAd(string $adType, string $slug): View
    {
        $ad = Ad::with(['shopAd', 'shopAd.dealer', 'shopAd.dealerShowRoom'])
                ->whereSlug($slug)
                ->firstOrFail();

        $relatedAdsByCar   = $this->findRelatedAdsByMake($ad);
        $relatedAdsByPrice = $this->findRelatedAdsByPrice($ad);
        $sellerAds         = $this->findFromSameSeller($ad);

        $conditionOptions = [];

        if (Auth::user() && Auth::user()->id === $ad->user_id) {
            $conditionOptions = ConditionEnum::getAllTranslatedForShop();
        }

        return view(
            'shop-ad.shop-ad-view',
            compact(
                'ad',
                'relatedAdsByCar',
                'relatedAdsByPrice',
                'sellerAds',
                'conditionOptions',
            )
        );
    }

    /**
     * @param Ad $ad
     *
     * @return Collection
     */
    private function findRelatedAdsByMake(Ad $ad): Collection
    {
        $searchCriteria = [
            'make'         => $ad->shopAd->make->slug,
            'itemsPerPage' => self::RELATED_ADS_ITEMS_PER_PAGE,
        ];

        $results = $this->finder->find($ad->type, $searchCriteria);

        return $this->excludeCurrentAd(collect($results->items()), $ad);
    }

    /**
     * @param Ad $ad
     *
     * @return Collection
     */
    private function findRelatedAdsByPrice(Ad $ad): Collection
    {
        $searchCriteria = [
            'price_min'    => floatval($ad->shopAd->price) - self::RELATED_ADS_PRICE_RANGE_DEVIATION,
            'price_max'    => floatval($ad->shopAd->price) + self::RELATED_ADS_PRICE_RANGE_DEVIATION,
            'itemsPerPage' => self::RELATED_ADS_ITEMS_PER_PAGE,
        ];

        $results = $this->finder->find($ad->type, $searchCriteria);

        return $this->excludeCurrentAd(collect($results->items()), $ad);
    }

    /**
     * @param Ad $ad
     *
     * @return Collection
     */
    private function findFromSameSeller(Ad $ad): Collection
    {
        $searchCriteria                 = $this->getSellerCriteria($ad);
        $searchCriteria['itemsPerPage'] = self::RELATED_ADS_ITEMS_PER_PAGE;

        $results = $this->finder->find($ad->type, $searchCriteria);

        return $this->excludeCurrentAd(collect($results->items()), $ad);
    }

    private function getSellerCriteria(Ad $ad): array
    {
        if ($ad->shopAd->dealer instanceof Dealer) {
            return ['dealer_id' => $ad->shopAd->dealer_id];
        }

        return ['user_id' => $ad->user_id];
    }

    public function presentJsonAd(string $adType, string $slug): JsonResponse
    {
        $ad = Ad::with(['shopAd', 'shopAd.dealer', 'shopAd.dealerShowRoom'])
                ->whereSlug($slug)
                ->firstOrFail();

        return new JsonResponse($ad, JsonResponse::HTTP_OK);
    }
}
