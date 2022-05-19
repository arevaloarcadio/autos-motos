<?php

declare(strict_types=1);

namespace App\Service\Ad\Validator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\TruckTypeEnum;
use App\Enum\Ad\VehicleAdStepEnum;
use App\Enum\Core\RegexEnum;
use App\Enum\User\RoleEnum;
use App\Models\User;
use App\Service\Ad\AdTypeStorageFacade;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @package App\Service\Ad\Validator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TruckAdValidator implements IAdValidator
{
    public function supports(string $adType): bool
    {
        return AdTypeEnum::TRUCK_SLUG === $adType;
    }

    /**
     * @param array    $input
     * @param int|null $step
     * @param bool     $isUpdate
     *
     * @return array
     * @throws ValidationException
     */
    public function validate(array $input, ?int $step = null, bool $isUpdate = false): array
    {
        return $this->validator($input, $step)->validate();
    }

    /**
     * @param array    $data
     * @param int|null $step
     *
     * @return ValidatorInterface
     */
    protected function validator(array $data, ?int $step = null): ValidatorInterface
    {
        $rules = $this->getRulesByStep($step);

        return Validator::make(
            $data,
            $rules,
            [
                'truck_ad.inspection_valid_until_month.required_with' => __('validation.invalid_date'),
                'truck_ad.inspection_valid_until_year.required_with'  => __('validation.invalid_date'),
                'truck_ad.youtube_link.regex'                         => __('ads.validation_youtube_link'),
                'images.min'                                          => __('ads.validation_min_images'),
                'images.max'                                          => __('ads.validation_max_images'),
            ],
            [
                'truck_ad.truck_type'                   => 'truck type',
                'truck_ad.make_id'                      => 'make',
                'truck_ad.custom_make'                  => 'custom make',
                'truck_ad.model'                        => 'model',
                'truck_ad.custom_model'                 => 'custom model',
                'truck_ad.transmission_type_id'         => 'transmission',
                'truck_ad.fuel_type_id'                 => 'fuel',
                'truck_ad.vehicle_category_id'          => 'vehicle category',
                'truck_ad.construction_year'            => 'construction year',
                'truck_ad.first_registration_year'      => 'first registration',
                'truck_ad.first_registration_month'     => 'first registration',
                'truck_ad.market_id'                    => 'country',
                'truck_ad.seats'                        => 'seats',
                'truck_ad.beds'                         => 'beds',
                'truck_ad.sleeping_places'              => 'sleeping places',
                'truck_ad.color'                        => 'color',
                'truck_ad.condition'                    => 'condition',
                'truck_ad.length_cm'                    => 'length cm',
                'truck_ad.width_cm'                     => 'width cm',
                'truck_ad.height_cm'                    => 'height cm',
                'truck_ad.max_weight_allowed_kg'        => 'max weight allowed kg',
                'truck_ad.payload_kg'                   => 'payload kg',
                'truck_ad.inspection_valid_until_year'  => 'inspection valid until',
                'truck_ad.inspection_valid_until_month' => 'inspection valid until',
                'truck_ad.price'                        => 'price',
                'truck_ad.dealer_id'                    => 'dealer',
                'truck_ad.dealer_show_room_id'          => 'dealer show room',
                'truck_ad.first_name'                   => 'first name',
                'truck_ad.last_name'                    => 'last name',
                'truck_ad.email_address'                => 'email address',
                'truck_ad.address'                      => 'address',
                'truck_ad.zip_code'                     => 'zip code',
                'truck_ad.city'                         => 'city',
                'truck_ad.country'                      => 'country',
                'truck_ad.mobile_number'                => 'mobile number',
                'truck_ad.landline_number'              => 'landline number',
                'truck_ad.whatsapp_number'              => 'whatsapp number',
                'truck_ad.youtube_link'                 => 'youtube link',
            ]
        );
    }

    /**
     * @param int|null $step
     *
     * @return array
     */
    private function getRulesByStep(?int $step = null): array
    {
        switch (VehicleAdStepEnum::getStringByStepNumber($step)) {
            case VehicleAdStepEnum::TRUCK_TYPE_STRING:
                return $this->getTruckTypeValidationRules();
            case VehicleAdStepEnum::VEHICLE_DATA_STRING:
                return $this->getVehicleDataValidationRules();
            case VehicleAdStepEnum::DETAILS_STRING:
                return $this->getDetailsValidationRules();
            case VehicleAdStepEnum::IMAGES_STRING:
                return $this->getImageRules();
            case VehicleAdStepEnum::OPTIONS_STRING:
                return $this->getOptionsValidationRules();
            case VehicleAdStepEnum::CONTACT_STRING:
                return $this->getContactValidationRules();
            default:
                return array_merge(
                    $this->getTruckTypeValidationRules(),
                    $this->getVehicleDataValidationRules(),
                    $this->getDetailsValidationRules(),
                    $this->getImageRules(),
                    $this->getOptionsValidationRules(),
                    $this->getContactValidationRules(),
                    ['type' => ['required', Rule::in(AdTypeEnum::getAllSlugs())]]
                );
        }
    }

    /**
     * @return array
     */
    private function getTruckTypeValidationRules(): array
    {
        return [
            'truck_ad.truck_type' => [
                'required',
                Rule::in(
                    AdTypeStorageFacade::getAllSubTypeSlugsByTypeSlug(AdTypeEnum::TRUCK_SLUG)
                ),
            ],
        ];
    }

    /**
     * @return array
     */
    private function getVehicleDataValidationRules(): array
    {
        return [
            'truck_ad.has_custom_make'          => 'required|boolean',
            'truck_ad.make_id'                  => 'required_if:truck_ad.has_custom_make,false',
            'truck_ad.custom_make'              => 'required_if:truck_ad.has_custom_make,true',
            'truck_ad.model'                    => 'required|string',
            'truck_ad.fuel_type_id'             => 'nullable',
            'truck_ad.vehicle_category_id'      => 'required',
            'truck_ad.transmission_type_id'     => 'nullable',
            'truck_ad.construction_year'        => 'nullable|integer',
            'truck_ad.first_registration_year'  => sprintf(
                'required_unless:truck_ad.truck_type,%s,%s,%s|integer',
                TruckTypeEnum::CONSTRUCTION_MACHINE,
                TruckTypeEnum::AGRICULTURE_VEHICLE,
                TruckTypeEnum::FORKLIFT
            ),
            'truck_ad.first_registration_month' => sprintf(
                'required_unless:truck_ad.truck_type,%s,%s,%s|integer',
                TruckTypeEnum::CONSTRUCTION_MACHINE,
                TruckTypeEnum::AGRICULTURE_VEHICLE,
                TruckTypeEnum::FORKLIFT
            ),
            'truck_ad.power_kw'                 => 'nullable|integer',
            'truck_ad.fuel_consumption'         => 'nullable|numeric|min:0',
            'truck_ad.emission_class'           => 'nullable|string',
            'truck_ad.co2_emissions'            => 'nullable|numeric|min:0',
        ];
    }

    /**
     * @return array
     */
    private function getDetailsValidationRules(): array
    {
        $rules = [
            'title'                                 => 'required|string|min:10',
            'description'                           => 'required|string|min:30',
            'truck_ad.mileage'                      => sprintf(
                'required_unless:truck_ad.truck_type,%s,%s|integer|min:0',
                TruckTypeEnum::TRAILER,
                TruckTypeEnum::SEMI_TRAILER
            ),
            'truck_ad.cab'                          => 'nullable|string',
            'truck_ad.seats'                        => 'nullable|integer|min:0',
            'truck_ad.loading_space_length_mm'      => 'nullable|numeric|min:0',
            'truck_ad.loading_space_width_mm'       => 'nullable|numeric|min:0',
            'truck_ad.loading_space_height_mm'      => 'nullable|numeric|min:0',
            'truck_ad.loading_volume_m3'            => 'nullable|numeric|min:0',
            'truck_ad.payload_kg'                   => 'nullable|numeric|min:0',
            'truck_ad.empty_weight_kg'              => 'nullable|numeric|min:0',
            'truck_ad.permanent_total_weight_kg'    => 'nullable|numeric|min:0',
            'truck_ad.allowed_pulling_weight_kg'    => 'nullable|numeric|min:0',
            'truck_ad.axes'                         => 'nullable|integer|min:0',
            'truck_ad.construction_height_mm'       => 'nullable|numeric|min:0',
            'truck_ad.lifting_height_mm'            => 'nullable|numeric|min:0',
            'truck_ad.load_capacity_kg'             => 'nullable|numeric|min:0',
            'truck_ad.operating_hours'              => 'nullable|integer|min:0',
            'truck_ad.operating_weight_kg'          => 'nullable|numeric|min:0',
            'truck_ad.lifting_capacity_kg_m'        => 'nullable|numeric|min:0',
            'truck_ad.max_weight_allowed_kg'        => 'nullable|numeric|min:0',
            'truck_ad.wheel_formula'                => 'nullable|string',
            'truck_ad.hydraulic_system'             => 'nullable|string',
            'truck_ad.interior_color'               => 'nullable',
            'truck_ad.exterior_color'               => 'nullable',
            'truck_ad.owners'                       => 'nullable',
            'truck_ad.condition'                    => 'required',
            'truck_ad.inspection_valid_until_month' => 'nullable|required_with:truck_ad.inspection_valid_until_year',
            'truck_ad.inspection_valid_until_year'  => 'nullable|required_with:truck_ad.inspection_valid_until_month',
            'truck_ad.price'                        => 'required',
            'truck_ad.price_contains_vat'           => 'nullable|boolean',
            'status'                                => 'nullable',
            'external_id'                           => 'nullable',
            'source'                                => 'nullable',
        ];
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser instanceof User) {
            $rules['title']       = 'required|string|min:5';
            $rules['description'] = 'required|string|min:5';
        }

        return $rules;
    }

    private function getImageRules(): array
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (true === $currentUser->hasRole(RoleEnum::ADMIN)) {
            return ['images' => 'nullable'];
        }

        $rules = [
            'required',
            'array',
            'min:3',
        ];

        if (false === $currentUser->hasRole(RoleEnum::ADMIN) && $currentUser->dealer_id === null) {
            $rules[] = 'max: 7';
        }

        return [
            'truck_ad.youtube_link' => [
                'nullable',
                sprintf('regex:%s', RegexEnum::YOUTUBE_LINK),
            ],
            'images'                => $rules,
        ];
    }

    /**
     * @return array
     */
    private function getOptionsValidationRules(): array
    {
        return [
            'truck_ad.options' => 'nullable',
        ];
    }

    /**
     * @return array
     */
    private function getContactValidationRules(): array
    {
        return [
            'truck_ad.dealer_id'           => 'nullable',
            'truck_ad.dealer_show_room_id' => 'required_with:truck_ad.dealer_id',
            'truck_ad.first_name'          => 'required_without:truck_ad.dealer_id',
            'truck_ad.last_name'           => 'required_without:truck_ad.dealer_id',
            'truck_ad.email_address'       => 'required|email',
            'truck_ad.address'             => 'required',
            'truck_ad.zip_code'            => 'required',
            'truck_ad.city'                => 'required',
            'truck_ad.country'             => 'required',
            'truck_ad.mobile_number'       => 'required',
            'truck_ad.landline_number'     => 'nullable',
            'truck_ad.whatsapp_number'     => 'nullable',
            'user_id'                      => 'required|exists:users,id',
            'market_id'                    => 'required|exists:markets,id',
        ];
    }
}
