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
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Finder
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MotoAdFinder implements IAdFinder
{
    use ApprovedAdTypeQueryable;

    private const DEFAULT_SEARCH_ORDER_BY = 'created_at;desc';

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::MOTO_SLUG;
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

    public function presentAds(LengthAwarePaginator $ads, string $adType, ?string $adSubType): View
    {
        return view('moto-ad.moto-ad-list', compact('ads', 'adType', 'adSubType'));
    }

    private function applyFilters(Builder $query, array $input): Builder
    {
        $query = $query
            ->with(
                [
                    'motoAd',
                    'motoAd.dealer',
                    'motoAd.dealerShowRoom',
                    'motoAd.make',
                    'motoAd.model',
                    'motoAd.bodyType',
                    'motoAd.transmissionType',
                    'motoAd.fuelType',
                    'motoAd.driveType',
                ]
            )
            ->select('ads.*')
            ->withCount('images')
            ->join('moto_ads as ma', 'ma.ad_id', '=', 'ads.id');
        if (isset($input['markets'])) {
            $markets = explode(';', $input['markets']);
            $query->join('markets as m', 'ads.market_id', '=', 'm.id')
                  ->whereIn('m.slug', $markets);
        }
        if (isset($input['cars'])) {
            $query->leftJoin('models AS cmo', 'ma.model_id', '=', 'cmo.id')
                  ->leftJoin('makes AS cma', 'ma.make_id', '=', 'cma.id');
            $components = collect(explode(';', $input['cars']));
            $components = $components->map(
                function ($component) {
                    return explode(',', $component);
                }
            );
            $query->where(
                function (Builder $q) use ($components) {
                    foreach ($components as $component) {
                        $q->orWhere(
                            function (Builder $q2) use ($component) {
                                $q2->where('cma.slug', $component[0]);
                                if (isset($component[1])) {
                                    $modelComponents = explode('|', $component[1]);
                                    $q2->whereIn('cmo.slug', $modelComponents);
                                }
                                if (isset($component[2])) {
                                    $q2->where('cg.external_id', $component[2]);
                                }
                                if (isset($component[3])) {
                                    $q2->where('cs.external_id', $component[3]);
                                }
                            }
                        );
                    }
                }
            );
        }

        if (isset($input['price_min'])) {
            $query->where('price', '>=', $input['price_min']);
        }
        if (isset($input['price_max'])) {
            $query->where('price', '<=', $input['price_max']);
        }

        if (isset($input['year_min'])) {
            $query->where('first_registration_year', '>=', $input['year_min']);
        }
        if (isset($input['year_max'])) {
            $query->where('first_registration_year', '<=', $input['year_max']);
        }

        if (isset($input['mileage_min'])) {
            $query->where('mileage', '>=', $input['mileage_min']);
        }
        if (isset($input['mileage_max'])) {
            $query->where('mileage', '<=', $input['mileage_max']);
        }

        if (isset($input['engine_displacement_min'])) {
            $query->where('engine_displacement', '>=', $input['engine_displacement_min']);
        }
        if (isset($input['engine_displacement_max'])) {
            $query->where('engine_displacement', '<=', $input['engine_displacement_max']);
        }

        if (isset($input['power_kw_min'])) {
            $query->where('power_kw', '>=', $input['power_kw_min']);
        }
        if (isset($input['power_kw_max'])) {
            $query->where('power_kw', '<=', $input['power_kw_max']);
        }

        if (isset($input['fuel_consumption_min'])) {
            $query->where('fuel_consumption', '>=', $input['fuel_consumption_min']);
        }
        if (isset($input['fuel_consumption_max'])) {
            $query->where('fuel_consumption', '<=', $input['fuel_consumption_max']);
        }

        if (isset($input['gears_min'])) {
            $query->where('gears', '>=', $input['gears_min']);
        }
        if (isset($input['gears_max'])) {
            $query->where('gears', '<=', $input['gears_max']);
        }

        if (isset($input['cylinders_min'])) {
            $query->where('cylinders', '>=', $input['cylinders_min']);
        }
        if (isset($input['cylinders_max'])) {
            $query->where('doors', '<=', $input['cylinders_max']);
        }

        if (isset($input['owners_min'])) {
            $query->where('owners', '>=', $input['owners_min']);
        }
        if (isset($input['owners_max'])) {
            $query->where('owners', '<=', $input['owners_max']);
        }

        if (isset($input['fuel_types'])) {
            $fuelTypes = explode(';', $input['fuel_types']);
            $query->join('car_fuel_types as cft', 'ma.fuel_type_id', '=', 'cft.id')
                  ->whereIn('cft.slug', $fuelTypes);
        }
        if (isset($input['transmissions'])) {
            $transmissionTypes = explode(';', $input['transmissions']);
            $query->join('car_transmission_types as ctt', 'ma.transmission_type_id', '=', 'ctt.id')
                  ->whereIn('ctt.slug', $transmissionTypes);
        }
        if (isset($input['drivetrains'])) {
            $drivetrains = explode(';', $input['drivetrains']);
            $query->join('car_wheel_drive_types as cwdt', 'ma.drive_type_id', '=', 'cwdt.id')
                  ->whereIn('cwdt.slug', $drivetrains);
        }
        if (isset($input['body_types'])) {
            $bodyTypes = explode(';', $input['body_types']);
            $query->join('car_body_types as cbt', 'ma.body_type_id', '=', 'cbt.id')
                  ->whereIn('cbt.slug', $bodyTypes);
        }
        if (isset($input['conditions'])) {
            $conditions = explode(';', $input['conditions']);
            $query->whereIn('condition', $conditions);
        }
        if (isset($input['colors'])) {
            $colors = explode(';', $input['colors']);
            $query->whereIn('color', $colors);
        }

        if (isset($input['valid_inspection'])) {
            $validInspectionAfter = Carbon::now()->addMonths($input['valid_inspection'])->endOfDay();
            $query->whereRaw(
                sprintf(
                    '%s >= %s',
                    'STR_TO_DATE(CONCAT(inspection_valid_until_year,"-",LPAD(inspection_valid_until_month,2,"00"),"-","01"), "%Y-%m-%d")',
                    sprintf('"%s"', $validInspectionAfter->format('Y-m-d'))
                )
            );
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
        if (isset($input['dealer_id'])) {
            $query->where('ma.dealer_id', $input['dealer_id']);
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
            'price;desc'      => 'ma.price;desc',
            'price;asc'       => 'ma.price;asc',
            'mileage;desc'    => 'ma.mileage;desc',
            'mileage;asc'     => 'ma.mileage;asc',
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
                'markets'                 => 'nullable',
                'cars'                    => 'nullable',
                'price_min'               => 'nullable',
                'price_max'               => 'nullable',
                'year_min'                => 'nullable',
                'year_max'                => 'nullable',
                'mileage_min'             => 'nullable',
                'mileage_max'             => 'nullable',
                'engine_displacement_min' => 'nullable',
                'engine_displacement_max' => 'nullable',
                'power_kw_min'            => 'nullable',
                'power_kw_max'            => 'nullable',
                'gears_min'               => 'nullable',
                'gears_max'               => 'nullable',
                'cylinders_min'           => 'nullable',
                'cylinders_max'           => 'nullable',
                'owners_min'              => 'nullable',
                'owners_max'              => 'nullable',
                'fuel_consumption_min'    => 'nullable',
                'fuel_consumption_max'    => 'nullable',
                'conditions'              => 'nullable',
                'seller_type'             => 'nullable',
                'body_types'              => 'nullable',
                'transmissions'           => 'nullable',
                'drivetrains'             => 'nullable',
                'fuel_types'              => 'nullable',
                'colors'                  => 'nullable',
                'valid_inspection'        => 'nullable',
                'order_by'                => 'nullable',
                'dealer_id'               => 'nullable',
                'user_id'                 => 'nullable',
                'items_per_page'          => 'nullable',
            ]
        );
    }
}
