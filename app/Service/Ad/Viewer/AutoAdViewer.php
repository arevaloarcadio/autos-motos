<?php

declare(strict_types=1);

namespace App\Service\Ad\Viewer;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\ColorEnum;
use App\Enum\Ad\ConditionEnum;
use App\Models\Ad;
use App\Models\AutoOption;
use App\Models\Dealer;
use App\Service\Ad\Finder\AutoAdFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Viewer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AutoAdViewer implements IAdViewer
{
    use ExcludeCurrentAdTrait;

    private const RELATED_ADS_PRICE_RANGE_DEVIATION = 10000;
    private const RELATED_ADS_ITEMS_PER_PAGE        = 11;

    /**
     * @var AutoAdFinder
     */
    private $autoAdFinder;

    public function __construct(AutoAdFinder $autoAdFinder)
    {
        $this->autoAdFinder = $autoAdFinder;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::AUTO_SLUG;
    }

    public function presentAd(string $adType, string $slug): View
    {
        $ad = Ad::with(['autoAd', 'autoAd.dealer', 'autoAd.dealerShowRoom'])
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
            'auto-ad.auto-ad-view',
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
        $searchCriteria = [
            'cars'         => sprintf('%s,%s', $ad->autoAd->make->slug, $ad->autoAd->make->slug),
            'itemsPerPage' => self::RELATED_ADS_ITEMS_PER_PAGE,
        ];

        $results = $this->autoAdFinder->find($ad->type, $searchCriteria);

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
            'price_min'    => floatval($ad->autoAd->price) - self::RELATED_ADS_PRICE_RANGE_DEVIATION,
            'price_max'    => floatval($ad->autoAd->price) + self::RELATED_ADS_PRICE_RANGE_DEVIATION,
            'itemsPerPage' => self::RELATED_ADS_ITEMS_PER_PAGE,
        ];

        $results = $this->autoAdFinder->find($ad->type, $searchCriteria);

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

        $results = $this->autoAdFinder->find($ad->type, $searchCriteria);

        return $this->excludeCurrentAd(collect($results->items()), $ad);
    }

    private function getSellerCriteria(Ad $ad): array
    {
        if ($ad->autoAd->dealer instanceof Dealer) {
            return ['dealer_id' => $ad->autoAd->dealer_id];
        }

        return ['user_id' => $ad->user_id];
    }

    public function presentJsonAd(string $adType, string $slug): JsonResponse
    {
        $ad = Ad::with(['autoAd', 'autoAd.dealer', 'autoAd.dealerShowRoom'])
                ->whereSlug($slug)
                ->firstOrFail();

        return new JsonResponse($ad, JsonResponse::HTTP_OK);
    }
}
