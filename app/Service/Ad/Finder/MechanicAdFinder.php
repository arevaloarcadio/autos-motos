<?php

declare(strict_types=1);

namespace App\Service\Ad\Finder;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\SellerTypeEnum;
use App\Enum\PaginationMetadataDefaultsEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Finder
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MechanicAdFinder implements IAdFinder
{
    use ApprovedAdTypeQueryable;

    private const DEFAULT_SEARCH_ORDER_BY = 'created_at;desc';

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::MECHANIC_SLUG;
    }

    public function find(string $adType, array $input): LengthAwarePaginator
    {
        $query = $this->getBaseQuery($adType);
        $input = $this->sanitizeInput($input);
        $query = $this->applyFilters($query, $input);
        $query = $this->applySorting($query, $input['order_by'] ?? self::DEFAULT_SEARCH_ORDER_BY);

        $itemsPerPage = $input['items_per_page'] ?? PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE;

        return $query->paginate(intval($itemsPerPage));
    }

    public function count(string $adType, array $input): int
    {
        $query = $this->getBaseQuery($adType);
        $input = $this->sanitizeInput($input);
        $query = $this->applyFilters($query, $input);

        return $query->count();
    }

    private function applyFilters(Builder $query, array $input): Builder
    {
        $query = $query
            ->with(['mechanicAd'])
            ->select('ads.*')
            ->join('mechanic_ads as ma', 'ma.ad_id', '=', 'ads.id');

        if (isset($input['title'])) {
            $query->where('ads.title', 'LIKE', sprintf('%%%s%%', $input['title']));
        }
        if (isset($input['market'])) {
            $query->join('markets as m', 'ads.market_id', '=', 'm.id')
                  ->where('m.slug', '=', $input['market']);
        }
        if (isset($input['city'])) {
            $query->where('ma.city', 'LIKE', sprintf('%%%s%%', $input['city']));
        }
        if (isset($input['distance']) &&
            intval($input['distance']) > 0 &&
            isset($input['latitude']) &&
            isset($input['longitude'])) {
            $query->whereRaw(
                "
                   ST_Distance_Sphere(
                        point(ma.longitude, ma.latitude),
                        point(?, ?)
                    ) * .001 < ?
                ",
                [
                    $input['longitude'],
                    $input['latitude'],
                    $input['distance'],
                ]
            );
        }

        return $query;
    }

    private function applySorting(Builder $query, string $orderBy): Builder
    {
        $orderByOptions = [
            'created_at;desc'     => 'ads.created_at;desc',
            'created_at;asc'      => 'ads.created_at;asc',
            'alphabetically;asc'  => 'ads.title;asc',
            'alphabetically;desc' => 'ads.title;desc',
        ];
        $orderByValue   = $orderByOptions[self::DEFAULT_SEARCH_ORDER_BY];
        if (isset($orderByOptions[$orderBy])) {
            $orderByValue = $orderByOptions[$orderBy];
        }

        $orderByComponents = explode(';', $orderByValue);

        return $query->orderBy($orderByComponents[0], $orderByComponents[1]);
    }

    /**
     * @param array $input
     *
     * @return array
     */
    private function sanitizeInput(array $input): array
    {
        $input = $this->validator($input)->validate();

        return array_filter(
            $input,
            function ($filter) {
                return $filter !== null;
            }
        );
    }

    /**
     * @param array $filters
     *
     * @return ValidatorInterface
     */
    public function validator(array $filters): ValidatorInterface
    {
        return Validator::make(
            $filters,
            [
                'title'          => 'nullable',
                'market'         => 'nullable',
                'city'           => 'nullable',
                'distance'       => 'nullable',
                'latitude'       => 'nullable',
                'longitude'      => 'nullable',
                'order_by'       => 'nullable',
                'dealer_id'      => 'nullable',
                'user_id'        => 'nullable',
                'items_per_page' => 'nullable',
                'is_external'    => 'nullable',
            ]
        );
    }

    public function presentAds(LengthAwarePaginator $ads, string $adType, ?string $adSubType): View
    {
        return view('mechanic-ad.mechanic-ad-list', compact('ads', 'adType', 'adSubType'));
    }
}
