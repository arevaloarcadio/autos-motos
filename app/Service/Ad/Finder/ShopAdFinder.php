<?php

declare(strict_types=1);

namespace App\Service\Ad\Finder;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\SellerTypeEnum;
use App\Enum\PaginationMetadataDefaultsEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Finder
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class ShopAdFinder implements IAdFinder
{
    use ApprovedAdTypeQueryable;

    private const DEFAULT_SEARCH_ORDER_BY = 'created_at;desc';

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::SHOP_SLUG;
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
            ->with(
                [
                    'shopAd',
                    'shopAd.dealer',
                    'shopAd.dealerShowRoom',
                    'shopAd.make',
                ]
            )
            ->select('ads.*')
            ->withCount('images')
            ->join('shop_ads as sa', 'sa.ad_id', '=', 'ads.id');

        if (isset($input['title'])) {
            $query->where('ads.title', 'LIKE', sprintf('%%%s%%', $input['title']));
        }
        if (isset($input['category'])) {
            $query->where('sa.category', '=', $input['category']);
        }
        if (isset($input['make'])) {
            $query->join('makes', 'sa.make_id', '=', 'makes.id')
                  ->where('makes.slug', '=', $input['make']);
        }
        if (isset($input['model'])) {
            $query->where('sa.model', 'LIKE', sprintf('%%%s%%', $input['model']));
        }
        if (isset($input['market'])) {
            $query->join('markets as m', 'ads.market_id', '=', 'm.id')
                  ->where('m.slug', '=', $input['market']);
        }
        if (isset($input['city'])) {
            $query->where('sa.city', 'LIKE', sprintf('%%%s%%', $input['city']));
        }
        if (isset($input['price_min'])) {
            $query->where('sa.price', '>=', $input['price_min']);
        }
        if (isset($input['price_max'])) {
            $query->where('sa.price', '<=', $input['price_max']);
        }
        if (isset($input['condition'])) {
            $query->where('sa.condition', '=', $input['condition']);
        }
        if (isset($input['seller_type']) &&
            in_array($input['seller_type'], [SellerTypeEnum::PRIVATE_SELLER, SellerTypeEnum::DEALERSHIP])) {
            switch ($input['seller_type']) {
                case SellerTypeEnum::DEALERSHIP:
                    $query->whereNotNull('dealer_id');
                    break;
                case SellerTypeEnum::PRIVATE_SELLER:
                    $query->whereNull('dealer_id');
                    break;
            }
        }
        if (isset($input['is_external'])) {
            if (false === $input['is_external']) {
                $query->whereNull('ads.external_id');
            }
            if (true === $input['is_external']) {
                $query->whereNotNull('ads.external_id');
            }
        }
        if (isset($input['dealer_id'])) {
            $query->where('sa.dealer_id', $input['dealer_id']);
        }
        if (isset($input['user_id'])) {
            $query->where('ads.user_id', $input['user_id']);
        }

        return $query;
    }

    private function applySorting(Builder $query, string $orderBy): Builder
    {
        $orderByOptions = [
            'created_at;desc' => 'ads.created_at;desc',
            'created_at;asc'  => 'ads.created_at;asc',
            'price;desc'      => 'sa.price;desc',
            'price;asc'       => 'sa.price;asc',
        ];
        $orderByValue   = $orderByOptions[self::DEFAULT_SEARCH_ORDER_BY];
        if (isset($orderByOptions[$orderBy])) {
            $orderByValue = $orderByOptions[$orderBy];
        }

        $orderByComponents = explode(';', $orderByValue);

        return $query->orderBy($orderByComponents[0], $orderByComponents[1])
                     ->orderBy('id', $orderByComponents[1]);
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
                'category'       => 'nullable',
                'condition'      => 'nullable',
                'make'           => 'nullable',
                'model'          => 'nullable',
                'price_min'      => 'nullable',
                'price_max'      => 'nullable',
                'seller_type'    => 'nullable',
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
        return view('shop-ad.shop-ad-list', compact('ads', 'adType', 'adSubType'));
    }
}
