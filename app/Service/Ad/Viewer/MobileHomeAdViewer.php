<?php

declare(strict_types=1);

namespace App\Service\Ad\Viewer;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\ColorEnum;
use App\Enum\Ad\ConditionEnum;
use App\Models\Ad;
use App\Models\AutoOption;
use App\Models\Dealer;
use App\Service\Ad\Finder\MobileHomeAdFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Viewer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MobileHomeAdViewer implements IAdViewer
{
    use ExcludeCurrentAdTrait;

    private const RELATED_ADS_PRICE_RANGE_DEVIATION = 10000;
    private const RELATED_ADS_ITEMS_PER_PAGE        = 11;

    /**
     * @var MobileHomeAdFinder
     */
    private $mobileHomeAdFinder;

    public function __construct(MobileHomeAdFinder $mobileHomeAdFinder)
    {
        $this->mobileHomeAdFinder = $mobileHomeAdFinder;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::MOBILE_HOME_SLUG;
    }

    public function presentAd(string $adType, string $slug): View
    {
        $ad = Ad::with(['mobileHomeAd', 'mobileHomeAd.dealer', 'mobileHomeAd.dealerShowRoom'])
                ->whereSlug($slug)
                ->firstOrFail();

        $relatedAdsByCar   = $this->findRelatedAdsByMakeAndModel($ad);
        $relatedAdsByPrice = $this->findRelatedAdsByPrice($ad);
        $sellerAds         = $this->findFromSameSeller($ad);

        $conditionOptions = [];
        $colorOptions     = [];
        $featureOptions   = [];

        if (Auth::user() && Auth::user()->id === $ad->user_id) {
            $conditionOptions = ConditionEnum::getAllTranslated();
            $colorOptions     = ColorEnum::getAll();
            $featureOptions   = AutoOption::with('children')
                                          ->whereNull('parent_id')
                                          ->orderBy('internal_name', 'ASC')
                                          ->get();
        }

        return view(
            'mobile-home-ad.mobile-home-ad-view',
            compact(
                'ad',
                'relatedAdsByCar',
                'relatedAdsByPrice',
                'sellerAds',
                'conditionOptions',
                'colorOptions',
                'featureOptions'
            )
        );
    }

    /**
     * @param Ad $ad
     *
     * @return Collection
     */
    private function findRelatedAdsByMakeAndModel(Ad $ad): Collection
    {
        if (null === $ad->mobileHomeAd->make) {
            return new Collection();
        }
        $searchCriteria = [
            'cars'         => sprintf('%s,%s', $ad->mobileHomeAd->make->slug, $ad->mobileHomeAd->make->slug),
            'itemsPerPage' => self::RELATED_ADS_ITEMS_PER_PAGE,
        ];

        $results = $this->mobileHomeAdFinder->find($ad->type, $searchCriteria);

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
            'price_min'    => floatval($ad->mobileHomeAd->price) - self::RELATED_ADS_PRICE_RANGE_DEVIATION,
            'price_max'    => floatval($ad->mobileHomeAd->price) + self::RELATED_ADS_PRICE_RANGE_DEVIATION,
            'itemsPerPage' => self::RELATED_ADS_ITEMS_PER_PAGE,
        ];

        $results = $this->mobileHomeAdFinder->find($ad->type, $searchCriteria);

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

        $results = $this->mobileHomeAdFinder->find($ad->type, $searchCriteria);

        return $this->excludeCurrentAd(collect($results->items()), $ad);
    }

    private function getSellerCriteria(Ad $ad): array
    {
        if ($ad->mobileHomeAd->dealer instanceof Dealer) {
            return ['dealer_id' => $ad->mobileHomeAd->dealer_id];
        }

        return ['user_id' => $ad->user_id];
    }

    public function presentJsonAd(string $adType, string $slug): JsonResponse
    {
        $ad = Ad::with(['mobileHomeAd', 'mobileHomeAd.dealer', 'mobileHomeAd.dealerShowRoom'])
                ->whereSlug($slug)
                ->firstOrFail();

        return new JsonResponse($ad, JsonResponse::HTTP_OK);
    }
}
