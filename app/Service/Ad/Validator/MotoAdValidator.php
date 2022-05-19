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
 * @package App\Service\CarAd
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class MotoAdValidator implements IAdValidator
{
    public function supports(string $adType): bool
    {
        return AdTypeEnum::MOTO_SLUG === $adType;
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
                'moto_ad.inspection_valid_until_month.required_with' => __('validation.invalid_date'),
                'moto_ad.inspection_valid_until_year.required_with'  => __('validation.invalid_date'),
                'moto_ad.youtube_link.regex'                         => __('ads.validation_youtube_link'),
                'images.min'                                         => __('ads.validation_min_images'),
                'images.max'                                         => __('ads.validation_max_images'),
            ],
            [
                'moto_ad.make_id'                      => 'make',
                'moto_ad.custom_make'                  => 'custom make',
                'moto_ad.model_id'                     => 'model',
                'moto_ad.custom_model'                 => 'custom model',
                'moto_ad.body_type_id'                 => 'body type',
                'moto_ad.drive_type_id'                => 'drivetrain',
                'moto_ad.transmission_type_id'         => 'transmission',
                'moto_ad.fuel_type_id'                 => 'fuel',
                'moto_ad.first_registration_year'      => 'first registration',
                'moto_ad.first_registration_month'     => 'first registration',
                'moto_ad.market_id'                    => 'country',
                'moto_ad.gears'                        => 'gears',
                'moto_ad.cylinders'                    => 'cylinders',
                'moto_ad.mileage'                      => 'mileage',
                'moto_ad.color'                        => 'color',
                'moto_ad.condition'                    => 'condition',
                'moto_ad.weight_kg'                    => 'weight kg',
                'moto_ad.inspection_valid_until_year'  => 'inspection valid until',
                'moto_ad.inspection_valid_until_month' => 'inspection valid until',
                'moto_ad.last_customer_service_year'   => 'last customer service',
                'moto_ad.last_customer_service_month'  => 'last customer service',
                'moto_ad.price'                        => 'price',
                'moto_ad.dealer_id'                    => 'dealer',
                'moto_ad.dealer_show_room_id'          => 'dealer show room',
                'moto_ad.first_name'                   => 'first name',
                'moto_ad.last_name'                    => 'last name',
                'moto_ad.email_address'                => 'email address',
                'moto_ad.address'                      => 'address',
                'moto_ad.zip_code'                     => 'zip code',
                'moto_ad.city'                         => 'city',
                'moto_ad.country'                      => 'country',
                'moto_ad.mobile_number'                => 'mobile number',
                'moto_ad.landline_number'              => 'landline number',
                'moto_ad.whatsapp_number'              => 'whatsapp number',
                'moto_ad.youtube_link'                 => 'youtube link',
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
            'moto_ad.has_custom_make'          => 'required|boolean',
            'moto_ad.make_id'                  => 'required_if:moto_ad.has_custom_make,false',
            'moto_ad.custom_make'              => 'required_if:moto_ad.has_custom_make,true',
            'moto_ad.has_custom_model'         => 'required|boolean',
            'moto_ad.model_id'                 => 'required_if:moto_ad.has_custom_model,false',
            'moto_ad.custom_model'             => 'required_if:moto_ad.has_custom_model,true',
            'moto_ad.body_type_id'             => 'required',
            'moto_ad.fuel_type_id'             => 'required',
            'moto_ad.drive_type_id'            => 'nullable',
            'moto_ad.transmission_type_id'     => 'nullable',
            'moto_ad.first_registration_year'  => 'required|integer',
            'moto_ad.first_registration_month' => 'required|integer',
            'moto_ad.engine_displacement'      => 'nullable|integer',
            'moto_ad.power_kw'                 => 'nullable|integer',
            'moto_ad.gears'                    => 'nullable|integer',
            'moto_ad.cylinders'                => 'nullable|integer',
            'moto_ad.fuel_consumption'         => 'nullable|numeric',
            'moto_ad.co2_emissions'            => 'nullable|numeric',
        ];
    }

    /**
     * @return array
     */
    private function getDetailsValidationRules(): array
    {
        $rules = [
            'title'                                => 'required|string|min:10',
            'description'                          => 'required|string|min:30',
            'moto_ad.mileage'                      => 'required|integer|min:0',
            'moto_ad.weight_kg'                    => 'nullable|integer|min:0',
            'moto_ad.color'                        => 'required',
            'moto_ad.condition'                    => 'required',
            'moto_ad.price'                        => 'required',
            'moto_ad.price_contains_vat'           => 'nullable|boolean',
            'moto_ad.owners'                       => 'nullable',
            'moto_ad.emission_class'               => 'nullable|string',
            'moto_ad.inspection_valid_until_month' => 'nullable|required_with:moto_ad.inspection_valid_until_year',
            'moto_ad.inspection_valid_until_year'  => 'nullable|required_with:moto_ad.inspection_valid_until_month',
            'moto_ad.last_customer_service_month'  => 'nullable|required_with:moto_ad.last_customer_service_year',
            'moto_ad.last_customer_service_year'   => 'nullable|required_with:moto_ad.last_customer_service_month',
            'status'                               => 'nullable',
            'external_id'                          => 'nullable',
            'source'                               => 'nullable',
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
            'moto_ad.youtube_link' => [
                'nullable',
                sprintf('regex:%s', RegexEnum::YOUTUBE_LINK),
            ],
            'images'               => $rules,
        ];
    }

    /**
     * @return array
     */
    private function getOptionsValidationRules(): array
    {
        return [
            'moto_ad.options' => 'nullable',
        ];
    }

    /**
     * @return array
     */
    private function getContactValidationRules(): array
    {
        return [
            'moto_ad.dealer_id'           => 'nullable',
            'moto_ad.dealer_show_room_id' => 'required_with:moto_ad.dealer_id',
            'moto_ad.first_name'          => 'required_without:moto_ad.dealer_id',
            'moto_ad.last_name'           => 'required_without:moto_ad.dealer_id',
            'moto_ad.email_address'       => 'required|email',
            'moto_ad.address'             => 'required',
            'moto_ad.zip_code'            => 'required',
            'moto_ad.city'                => 'required',
            'moto_ad.country'             => 'required',
            'moto_ad.mobile_number'       => 'required',
            'moto_ad.landline_number'     => 'nullable',
            'moto_ad.whatsapp_number'     => 'nullable',
            'user_id'                     => 'required|exists:users,id',
            'market_id'                   => 'required|exists:markets,id',
        ];
    }
}
