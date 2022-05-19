<?php

declare(strict_types=1);

namespace App\Service\Ad\Validator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\VehicleAdStepEnum;
use App\Enum\Core\RegexEnum;
use App\Enum\User\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @package App\Service\Ad\Validator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MobileHomeAdValidator implements IAdValidator
{
    public function supports(string $adType): bool
    {
        return AdTypeEnum::MOBILE_HOME_SLUG === $adType;
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
                'mobile_home_ad.inspection_valid_until_month.required_with' => __('validation.invalid_date'),
                'mobile_home_ad.inspection_valid_until_year.required_with'  => __('validation.invalid_date'),
                'mobile_home_ad.youtube_link.regex'                         => __('ads.validation_youtube_link'),
                'images.min'                                                => __('ads.validation_min_images'),
                'images.max'                                                => __('ads.validation_max_images'),
            ],
            [
                'mobile_home_ad.make_id'                      => 'make',
                'mobile_home_ad.custom_make'                  => 'custom make',
                'mobile_home_ad.model_id'                     => 'model',
                'mobile_home_ad.custom_model'                 => 'custom model',
                'mobile_home_ad.transmission_type_id'         => 'transmission',
                'mobile_home_ad.fuel_type_id'                 => 'fuel',
                'mobile_home_ad.vehicle_category_id'          => 'vehicle category',
                'mobile_home_ad.construction_year'            => 'construction year',
                'mobile_home_ad.first_registration_year'      => 'first registration',
                'mobile_home_ad.first_registration_month'     => 'first registration',
                'mobile_home_ad.market_id'                    => 'country',
                'mobile_home_ad.seats'                        => 'seats',
                'mobile_home_ad.beds'                         => 'beds',
                'mobile_home_ad.sleeping_places'              => 'sleeping places',
                'mobile_home_ad.color'                        => 'color',
                'mobile_home_ad.condition'                    => 'condition',
                'mobile_home_ad.length_cm'                    => 'length cm',
                'mobile_home_ad.width_cm'                     => 'width cm',
                'mobile_home_ad.height_cm'                    => 'height cm',
                'mobile_home_ad.max_weight_allowed_kg'        => 'max weight allowed kg',
                'mobile_home_ad.payload_kg'                   => 'payload kg',
                'mobile_home_ad.inspection_valid_until_year'  => 'inspection valid until',
                'mobile_home_ad.inspection_valid_until_month' => 'inspection valid until',
                'mobile_home_ad.price'                        => 'price',
                'mobile_home_ad.dealer_id'                    => 'dealer',
                'mobile_home_ad.dealer_show_room_id'          => 'dealer show room',
                'mobile_home_ad.first_name'                   => 'first name',
                'mobile_home_ad.last_name'                    => 'last name',
                'mobile_home_ad.email_address'                => 'email address',
                'mobile_home_ad.address'                      => 'address',
                'mobile_home_ad.zip_code'                     => 'zip code',
                'mobile_home_ad.city'                         => 'city',
                'mobile_home_ad.country'                      => 'country',
                'mobile_home_ad.mobile_number'                => 'mobile number',
                'mobile_home_ad.landline_number'              => 'landline number',
                'mobile_home_ad.whatsapp_number'              => 'whatsapp number',
                'mobile_home_ad.youtube_link'                 => 'youtube link',
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
        switch ($step) {
            case VehicleAdStepEnum::VEHICLE_DATA:
                return $this->getVehicleDataValidationRules();
            case VehicleAdStepEnum::DETAILS:
                return $this->getDetailsValidationRules();
            case VehicleAdStepEnum::IMAGES:
                return $this->getImageRules();
            case VehicleAdStepEnum::OPTIONS:
                return $this->getOptionsValidationRules();
            case VehicleAdStepEnum::CONTACT:
                return $this->getContactValidationRules();
            default:
                return array_merge(
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
    private function getVehicleDataValidationRules(): array
    {
        return [
            'mobile_home_ad.has_custom_make'          => 'required|boolean',
            'mobile_home_ad.make_id'                  => 'required_if:mobile_home_ad.has_custom_make,false',
            'mobile_home_ad.custom_make'              => 'required_if:mobile_home_ad.has_custom_make,true',
            'mobile_home_ad.has_custom_model'         => 'required|boolean',
            'mobile_home_ad.model_id'                 => 'required_if:mobile_home_ad.has_custom_model,false',
            'mobile_home_ad.custom_model'             => 'required_if:mobile_home_ad.has_custom_model,true',
            'mobile_home_ad.fuel_type_id'             => 'required',
            'mobile_home_ad.vehicle_category_id'      => 'required',
            'mobile_home_ad.transmission_type_id'     => 'nullable',
            'mobile_home_ad.construction_year'        => 'nullable|integer',
            'mobile_home_ad.first_registration_year'  => 'required|integer',
            'mobile_home_ad.first_registration_month' => 'required|integer',
            'mobile_home_ad.engine_displacement'      => 'nullable|integer',
            'mobile_home_ad.power_kw'                 => 'nullable|integer',
            'mobile_home_ad.fuel_consumption'         => 'nullable|numeric|min:0',
            'mobile_home_ad.co2_emissions'            => 'nullable|numeric|min:0',
        ];
    }

    /**
     * @return array
     */
    private function getDetailsValidationRules(): array
    {
        $rules = [
            'title'                                       => 'required|string|min:10',
            'description'                                 => 'required|string|min:30',
            'mobile_home_ad.mileage'                      => 'required|integer|min:0',
            'mobile_home_ad.color'                        => 'nullable',
            'mobile_home_ad.condition'                    => 'required',
            'mobile_home_ad.price'                        => 'required',
            'mobile_home_ad.price_contains_vat'           => 'nullable|boolean',
            'mobile_home_ad.owners'                       => 'nullable',
            'mobile_home_ad.seats'                        => 'nullable|integer|min:0',
            'mobile_home_ad.sleeping_places'              => 'nullable|integer|min:0',
            'mobile_home_ad.beds'                         => 'nullable|string',
            'mobile_home_ad.axes'                         => 'nullable|integer|min:0',
            'mobile_home_ad.emission_class'               => 'nullable|string',
            'mobile_home_ad.inspection_valid_until_month' => 'nullable|required_with:mobile_home_ad.inspection_valid_until_year',
            'mobile_home_ad.inspection_valid_until_year'  => 'nullable|required_with:mobile_home_ad.inspection_valid_until_month',
            'mobile_home_ad.length_cm'                    => 'nullable|numeric|min:0',
            'mobile_home_ad.width_cm'                     => 'nullable|numeric|min:0',
            'mobile_home_ad.height_cm'                    => 'nullable|numeric|min:0',
            'mobile_home_ad.payload_kg'                   => 'nullable|numeric|min:0',
            'mobile_home_ad.max_weight_allowed_kg'        => 'nullable|numeric|min:0',
            'status'                                      => 'nullable',
            'external_id'                                 => 'nullable',
            'source'                                      => 'nullable',
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
            'mobile_home_ad.youtube_link' => [
                'nullable',
                sprintf('regex:%s', RegexEnum::YOUTUBE_LINK),
            ],
            'images'                      => $rules,
        ];
    }

    /**
     * @return array
     */
    private function getOptionsValidationRules(): array
    {
        return [
            'mobile_home_ad.options' => 'nullable',
        ];
    }

    /**
     * @return array
     */
    private function getContactValidationRules(): array
    {
        return [
            'mobile_home_ad.dealer_id'           => 'nullable',
            'mobile_home_ad.dealer_show_room_id' => 'required_with:mobile_home_ad.dealer_id',
            'mobile_home_ad.first_name'          => 'required_without:mobile_home_ad.dealer_id',
            'mobile_home_ad.last_name'           => 'required_without:mobile_home_ad.dealer_id',
            'mobile_home_ad.email_address'       => 'required|email',
            'mobile_home_ad.address'             => 'required',
            'mobile_home_ad.zip_code'            => 'required',
            'mobile_home_ad.city'                => 'required',
            'mobile_home_ad.country'             => 'required',
            'mobile_home_ad.mobile_number'       => 'required',
            'mobile_home_ad.landline_number'     => 'nullable',
            'mobile_home_ad.whatsapp_number'     => 'nullable',
            'user_id'                            => 'required|exists:users,id',
            'market_id'                          => 'required|exists:markets,id',
        ];
    }
}
