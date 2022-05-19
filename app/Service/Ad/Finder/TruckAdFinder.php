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
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Finder
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TruckAdFinder implements IAdFinder
{
    use ApprovedAdTypeQueryable;
    use MinMaxCriteriaFinderTrait;

    private const DEFAULT_SEARCH_ORDER_BY = 'created_at;desc';

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::TRUCK_SLUG;
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
        return view('truck-ad.truck-ad-list', compact('ads', 'adType', 'adSubType'));
    }

    private function applyFilters(Builder $query, array $input): Builder
    {
        $query = $query
            ->with(
                [
                    'truckAd',
                    'truckAd.dealer',
                    'truckAd.dealerShowRoom',
                    'truckAd.make',
                    'truckAd.vehicleCategory',
                    'truckAd.transmissionType',
                    'truckAd.fuelType',
                ]
            )
            ->select('ads.*')
            ->withCount('images')
            ->join('truck_ads as ta', 'ta.ad_id', '=', 'ads.id');

        if (isset($input['subtype'])) {
            $query->where('ta.truck_type', '=', $input['subtype']);
        }

        if (isset($input['markets'])) {
            $markets = explode(';', $input['markets']);
            $query->join('markets as m', 'ads.market_id', '=', 'm.id')
                  ->whereIn('m.slug', $markets);
        }

        if (isset($input['makes'])) {
            $makes = explode(';', $input['makes']);
            $query->leftJoin('makes as ma', 'ta.make_id', '=', 'ma.id')
                  ->whereIn('ma.slug', $makes);
        }

        if (isset($input['model'])) {
            $query->where('ta.model', 'LIKE', sprintf('%%%s%%', $input['model']));
        }

        $query = $this->applyMinMaxCriteriaSearch($query, $input, 'year', 'first_registration_year');

        $technicalFields = [
            'price',
            'construction_year',
            'mileage',
            'power_kw',
            'fuel_consumption',
            'seats',
            'axes',
            'owners',
            'loading_space_length_mm',
            'loading_space_height_mm',
            'loading_space_width_mm',
            'loading_volume_m3',
            'payload_kg',
            'empty_weight_kg',
            'permanent_total_weight_kg',
            'allowed_pulling_weight_kg',
            'construction_height_mm',
            'lifting_height_mm',
            'load_capacity_kg',
            'operating_hours',
            'operating_weight_kg',
            'lifting_capacity_kg_m',
            'max_weight_allowed_kg',
        ];

        foreach ($technicalFields as $field) {
            $query = $this->applyMinMaxCriteriaSearch($query, $input, $field, $field);
        }

        if (isset($input['fuel_types'])) {
            $fuelTypes = explode(';', $input['fuel_types']);
            $query->join('car_fuel_types as cft', 'ta.fuel_type_id', '=', 'cft.id')
                  ->whereIn('cft.slug', $fuelTypes);
        }

        if (isset($input['transmissions'])) {
            $transmissionTypes = explode(';', $input['transmissions']);
            $query->join('car_transmission_types as ctt', 'ta.transmission_type_id', '=', 'ctt.id')
                  ->whereIn('ctt.slug', $transmissionTypes);
        }

        if (isset($input['vehicle_categories'])) {
            $vehicleCategories = explode(';', $input['vehicle_categories']);
            $query->join('vehicle_categories as vc', 'ta.vehicle_category_id', '=', 'vc.id')
                  ->whereIn('vc.slug', $vehicleCategories);
        }

        if (isset($input['conditions'])) {
            $conditions = explode(';', $input['conditions']);
            $query->whereIn('condition', $conditions);
        }

        if (isset($input['wheel_formulas'])) {
            $wheelFormulas = explode(';', $input['wheel_formulas']);
            $query->whereIn('wheel_formula', $wheelFormulas);
        }

        if (isset($input['hydraulic_systems'])) {
            $hydraulicSystems = explode(';', $input['hydraulic_systems']);
            $query->whereIn('hydraulic_system', $hydraulicSystems);
        }

        if (isset($input['emission_classes'])) {
            $emissionClasses = explode(';', $input['emission_classes']);
            $query->whereIn('emission_classes', $emissionClasses);
        }

        if (isset($input['interior_colors'])) {
            $interiorColors = explode(';', $input['interior_colors']);
            $query->whereIn('interior_color', $interiorColors);
        }

        if (isset($input['exterior_colors'])) {
            $exteriorColors = explode(';', $input['exterior_colors']);
            $query->whereIn('exterior_color', $exteriorColors);
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
            $query->where('ta.dealer_id', $input['dealer_id']);
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
            'price;desc'      => 'ta.price;desc',
            'price;asc'       => 'ta.price;asc',
            'mileage;desc'    => 'ta.mileage;desc',
            'mileage;asc'     => 'ta.mileage;asc',
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
                'subtype'                       => 'nullable',
                'markets'                       => 'nullable',
                'makes'                         => 'nullable',
                'model'                         => 'nullable',
                'price_min'                     => 'nullable',
                'price_max'                     => 'nullable',
                'year_min'                      => 'nullable',
                'year_max'                      => 'nullable',
                'construction_year_min'         => 'nullable',
                'construction_year_max'         => 'nullable',
                'mileage_min'                   => 'nullable',
                'mileage_max'                   => 'nullable',
                'power_kw_min'                  => 'nullable',
                'power_kw_max'                  => 'nullable',
                'seats_min'                     => 'nullable',
                'seats_max'                     => 'nullable',
                'axes_min'                      => 'nullable',
                'axes_max'                      => 'nullable',
                'owners_min'                    => 'nullable',
                'owners_max'                    => 'nullable',
                'fuel_consumption_min'          => 'nullable',
                'fuel_consumption_max'          => 'nullable',
                'conditions'                    => 'nullable',
                'seller_type'                   => 'nullable',
                'vehicle_categories'            => 'nullable',
                'transmissions'                 => 'nullable',
                'fuel_types'                    => 'nullable',
                'colors'                        => 'nullable',
                'valid_inspection'              => 'nullable',
                'wheel_formulas'                => 'nullable',
                'hydraulic_systems'             => 'nullable',
                'emission_classes'              => 'nullable',
                'loading_space_length_mm_min'   => 'nullable',
                'loading_space_length_mm_max'   => 'nullable',
                'loading_space_height_mm_min'   => 'nullable',
                'loading_space_height_mm_max'   => 'nullable',
                'loading_space_width_mm_min'    => 'nullable',
                'loading_space_width_mm_max'    => 'nullable',
                'loading_volume_m3_min'         => 'nullable',
                'loading_volume_m3_max'         => 'nullable',
                'payload_kg_min'                => 'nullable',
                'payload_kg_max'                => 'nullable',
                'empty_weight_kg_min'           => 'nullable',
                'empty_weight_kg_max'           => 'nullable',
                'permanent_total_weight_kg_min' => 'nullable',
                'permanent_total_weight_kg_max' => 'nullable',
                'allowed_pulling_weight_kg_min' => 'nullable',
                'allowed_pulling_weight_kg_max' => 'nullable',
                'construction_height_mm_min'    => 'nullable',
                'construction_height_mm_max'    => 'nullable',
                'lifting_height_mm_min'         => 'nullable',
                'lifting_height_mm_max'         => 'nullable',
                'load_capacity_kg_min'          => 'nullable',
                'load_capacity_kg_max'          => 'nullable',
                'operating_hours_min'           => 'nullable',
                'operating_hours_max'           => 'nullable',
                'operating_weight_kg_min'       => 'nullable',
                'operating_weight_kg_max'       => 'nullable',
                'lifting_capacity_kg_m_min'     => 'nullable',
                'lifting_capacity_kg_m_max'     => 'nullable',
                'max_weight_allowed_kg_min'     => 'nullable',
                'max_weight_allowed_kg_max'     => 'nullable',
                'order_by'                      => 'nullable',
                'dealer_id'                     => 'nullable',
                'user_id'                       => 'nullable',
                'items_per_page'                => 'nullable',
            ]
        );
    }
}
