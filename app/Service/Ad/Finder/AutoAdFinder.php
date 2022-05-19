<?php

declare(strict_types=1);

namespace App\Service\Ad\Finder;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\SellerTypeEnum;
use App\Enum\Core\ApprovalStatusEnum;
use App\Enum\PaginationMetadataDefaultsEnum;
use App\Models\Ad;
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
class AutoAdFinder implements IAdFinder
{
    use ApprovedAdTypeQueryable;

    private const DEFAULT_SEARCH_ORDER_BY = 'created_at;desc';

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::AUTO_SLUG;
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
        // $input is an array of search conditions
        $query = $this->getBaseQuery($adType);
        $input = $this->sanitizeInput($input);
        if (0 === count($input)) {
            $autoAdCount = $query->count() * 2;
            $adsCount    = Ad::query()
                             ->where('type', '!=', AdTypeEnum::AUTO_SLUG)
                             ->where('status', ApprovalStatusEnum::APPROVED)
                             ->count();

            return $autoAdCount + $adsCount;
        }
        $query = $this->getBaseQuery($adType);
        $query = $this->applyFilters($query, $input);

        // if the only filter in $input is 'markets', then double the results
        if (count($input)==1 && array_key_exists('markets',$input)) {
            return $query->count()*2;
        }
        return $query->count();
    }

    private function applyFilters(Builder $query, array $input): Builder
    {
        $query = $query
            ->with(
                [
                    'autoAd',
                    'autoAd.dealer',
                    'autoAd.dealerShowRoom',
                    'autoAd.make',
                    'autoAd.model',
                    'autoAd.bodyType',
                    'autoAd.transmissionType',
                    'autoAd.fuelType',
                    'autoAd.generation',
                    'autoAd.series',
                    'autoAd.trim',
                    'autoAd.driveType',
                ]
            )
            ->select('ads.*')
            ->withCount('images')
            ->join('auto_ads as aa', 'aa.ad_id', '=', 'ads.id');
        if (isset($input['markets'])) {
            $markets = explode(';', $input['markets']);
            $query->join('markets as m', 'ads.market_id', '=', 'm.id')
                  ->whereIn('m.slug', $markets);
        }

        if (isset($input['city'])) {
            $query->where('city', 'LIKE', sprintf('%%%s%%', $input['city']));
        }

        if (isset($input['cars'])) {
            $query->join('models AS cmo', 'aa.model_id', '=', 'cmo.id')
                  ->join('makes AS cma', 'aa.make_id', '=', 'cma.id');
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

        if (isset($input['power_hp_min'])) {
            $query->where('power_hp', '>=', $input['power_hp_min']);
        }
        if (isset($input['power_hp_max'])) {
            $query->where('power_hp', '<=', $input['power_hp_max']);
        }

        if (isset($input['fuel_consumption_min'])) {
            $query->where('fuel_consumption', '>=', $input['fuel_consumption_min']);
        }
        if (isset($input['fuel_consumption_max'])) {
            $query->where('fuel_consumption', '<=', $input['fuel_consumption_max']);
        }

        if (isset($input['doors_min'])) {
            $query->where('doors', '>=', $input['doors_min']);
        }
        if (isset($input['doors_max'])) {
            $query->where('doors', '<=', $input['doors_max']);
        }

        if (isset($input['seats_min'])) {
            $query->where('seats', '>=', $input['seats_min']);
        }
        if (isset($input['seats_max'])) {
            $query->where('doors', '<=', $input['seats_max']);
        }

        if (isset($input['owners_min'])) {
            $query->where('owners', '>=', $input['owners_min']);
        }
        if (isset($input['owners_max'])) {
            $query->where('owners', '<=', $input['owners_max']);
        }

        if (isset($input['fuel_types'])) {
            $fuelTypes = explode(';', $input['fuel_types']);
            $query->join('car_fuel_types as cft', 'aa.ad_fuel_type_id', '=', 'cft.id')
                  ->whereIn('cft.slug', $fuelTypes);
        }
        if (isset($input['transmissions'])) {
            $transmissionTypes = explode(';', $input['transmissions']);
            $query->join('car_transmission_types as ctt', 'aa.ad_transmission_type_id', '=', 'ctt.id')
                  ->whereIn('ctt.slug', $transmissionTypes);
        }
        if (isset($input['drivetrains'])) {
            $drivetrains = explode(';', $input['drivetrains']);
            $query->join('car_wheel_drive_types as cwdt', 'aa.ad_drive_type_id', '=', 'cwdt.id')
                  ->whereIn('cwdt.slug', $drivetrains);
        }
        if (isset($input['body_types'])) {
            $bodyTypes = explode(';', $input['body_types']);
            $query->join('car_body_types as cbt', 'aa.ad_body_type_id', '=', 'cbt.id')
                  ->whereIn('cbt.slug', $bodyTypes);
        }
        if (isset($input['conditions'])) {
            $conditions = explode(';', $input['conditions']);
            $query->whereIn('condition', $conditions);
        }
        if (isset($input['exterior_colors'])) {
            $exteriorColors = explode(';', $input['exterior_colors']);
            $query->whereIn('exterior_color', $exteriorColors);
        }
        if (isset($input['interior_colors'])) {
            $exteriorColors = explode(';', $input['interior_colors']);
            $query->whereIn('interior_color', $exteriorColors);
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
        if (isset($input['is_external'])) {
            if (false === $input['is_external']) {
                $query->whereNull('ads.external_id');
            }
            if (true === $input['is_external']) {
                $query->whereNotNull('ads.external_id');
            }
        }
        if (isset($input['dealer_id'])) {
            $query->where('aa.dealer_id', $input['dealer_id']);
        }
        if (isset($input['user_id'])) {
            $query->where('ads.user_id', $input['user_id']);
        }

        if (isset($input['distance']) &&
            intval($input['distance']) > 0 &&
            isset($input['latitude']) &&
            isset($input['longitude'])) {
            $query->whereRaw(
                "
                   ST_Distance_Sphere(
                        point(aa.longitude, aa.latitude),
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

        if (isset($input['images_processing_status'])){
            $query->where('images_processing_status',$input['images_processing_status']);
        }
        return $query;
    }

    private function applySorting(Builder $query, string $orderBy): Builder
    {
        $orderByOptions = [
            'created_at;desc' => 'ads.created_at;desc',
            'created_at;asc'  => 'ads.created_at;asc',
            'price;desc'      => 'aa.price;desc',
            'price;asc'       => 'aa.price;asc',
            'mileage;desc'    => 'aa.mileage;desc',
            'mileage;asc'     => 'aa.mileage;asc',
        ];

        if ('random' == $orderBy) {
            return $query->inRandomOrder()->orderBy('id', 'asc');
        }

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
                'markets'                 => 'nullable',
                'city'                    => 'nullable',
                'cars'                    => 'nullable',
                'price_min'               => 'nullable',
                'price_max'               => 'nullable',
                'year_min'                => 'nullable',
                'year_max'                => 'nullable',
                'mileage_min'             => 'nullable',
                'mileage_max'             => 'nullable',
                'engine_displacement_min' => 'nullable',
                'engine_displacement_max' => 'nullable',
                'power_hp_min'            => 'nullable',
                'power_hp_max'            => 'nullable',
                'doors_min'               => 'nullable',
                'doors_max'               => 'nullable',
                'seats_min'               => 'nullable',
                'seats_max'               => 'nullable',
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
                'exterior_colors'         => 'nullable',
                'interior_colors'         => 'nullable',
                'valid_inspection'        => 'nullable',
                'order_by'                => 'nullable',
                'dealer_id'               => 'nullable',
                'user_id'                 => 'nullable',
                'items_per_page'          => 'nullable',
                'is_external'             => 'nullable',
                'distance'                => 'nullable',
                'latitude'                => 'nullable',
                'longitude'               => 'nullable',
                'images_processing_status'=> 'nullable'
            ]
        );
    }

    public function presentAds(LengthAwarePaginator $ads, string $adType, ?string $adSubType): View
    {
        return view('auto-ad.auto-ad-list', compact('ads', 'adType', 'adSubType'));
    }
}
