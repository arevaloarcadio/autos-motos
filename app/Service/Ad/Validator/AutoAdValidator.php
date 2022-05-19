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
class AutoAdValidator implements IAdValidator
{
    public function supports(string $adType): bool
    {
        return AdTypeEnum::AUTO_SLUG === $adType;
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
                'auto_ad.additional_vehicle_info.required_if'        => __('validation.required'),
                'auto_ad.inspection_valid_until_month.required_with' => __('validation.invalid_date'),
                'auto_ad.inspection_valid_until_year.required_with'  => __('validation.invalid_date'),
                'auto_ad.youtube_link.regex'                         => __('ads.validation_youtube_link'),
                'images.min'                                         => __('ads.validation_min_images'),
                'images.max'                                         => __('ads.validation_max_images'),
                'auto_ad.mobile_number.regex'                        => __('validation.invalid_phone_number'),
                'auto_ad.whatsapp_number.regex'                      => __('validation.invalid_phone_number'),
                'auto_ad.landline_number.regex'                      => __('validation.invalid_phone_number'),
            ],
            [
                'auto_ad.make_id'                      => 'make',
                'auto_ad.model_id'                     => 'model',
                'auto_ad.generation_id'                => 'generation',
                'auto_ad.series_id'                    => 'series',
                'auto_ad.trim_id'                      => 'trim',
                'auto_ad.additional_vehicle_info'      => 'additional vehicle info',
                'auto_ad.ad_body_type_id'              => 'body type',
                'auto_ad.ad_drive_type_id'             => 'drivetrain',
                'auto_ad.ad_transmission_type_id'      => 'transmission',
                'auto_ad.ad_fuel_type_id'              => 'fuel',
                'auto_ad.first_registration_year'      => 'first registration',
                'auto_ad.first_registration_month'     => 'first registration',
                'auto_ad.market_id'                    => 'country',
                'auto_ad.doors'                        => 'doors',
                'auto_ad.seats'                        => 'seats',
                'auto_ad.vin'                          => 'vin',
                'auto_ad.mileage'                      => 'mileage',
                'auto_ad.interior_color'               => 'interior color',
                'auto_ad.exterior_color'               => 'exterior color',
                'auto_ad.condition'                    => 'condition',
                'auto_ad.inspection_valid_until_year'  => 'inspection valid until',
                'auto_ad.inspection_valid_until_month' => 'inspection valid until',
                'auto_ad.price'                        => 'price',
                'auto_ad.dealer_id'                    => 'dealer',
                'auto_ad.dealer_show_room_id'          => 'dealer show room',
                'auto_ad.first_name'                   => 'first name',
                'auto_ad.last_name'                    => 'last name',
                'auto_ad.email_address'                => 'email address',
                'auto_ad.address'                      => 'address',
                'auto_ad.zip_code'                     => 'zip code',
                'auto_ad.city'                         => 'city',
                'auto_ad.country'                      => 'country',
                'auto_ad.mobile_number'                => 'mobile number',
                'auto_ad.landline_number'              => 'landline number',
                'auto_ad.whatsapp_number'              => 'whatsapp number',
                'auto_ad.youtube_link'                 => 'youtube link',
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
            'auto_ad.make_id'                  => 'required',
            'auto_ad.model_id'                 => 'required',
            'auto_ad.generation_id'            => 'required_if:auto_ad.no_generation,false',
            'auto_ad.series_id'                => 'required_if:auto_ad.no_generation,false',
            'auto_ad.trim_id'                  => 'required_if:auto_ad.no_trim,false',
            'auto_ad.additional_vehicle_info'  => 'required_if:auto_ad.no_generation,true,auto_ad.no_trim,true',
            'auto_ad.equipment_id'             => 'nullable',
            'auto_ad.ad_body_type_id'          => 'nullable',
            'auto_ad.ad_drive_type_id'         => 'nullable',
            'auto_ad.ad_transmission_type_id'  => 'nullable',
            'auto_ad.ad_fuel_type_id'          => 'nullable',
            'auto_ad.first_registration_year'  => 'required|integer',
            'auto_ad.first_registration_month' => 'required|integer',
            'auto_ad.engine_displacement'      => 'nullable|integer',
            'auto_ad.power_hp'                 => 'nullable|integer',
            'auto_ad.doors'                    => 'nullable|integer',
            'auto_ad.seats'                    => 'nullable|integer',
            'auto_ad.fuel_consumption'         => 'nullable|numeric',
            'auto_ad.co2_emissions'            => 'nullable|numeric',
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
            'auto_ad.vin'                          => 'nullable',
            'auto_ad.mileage'                      => 'required|integer|min:0',
            'auto_ad.interior_color'               => 'required',
            'auto_ad.exterior_color'               => 'required',
            'auto_ad.condition'                    => 'required',
            'auto_ad.price'                        => 'required',
            'auto_ad.price_contains_vat'           => 'nullable|boolean',
            'auto_ad.owners'                       => 'nullable',
            'auto_ad.inspection_valid_until_month' => 'nullable|required_with:auto_ad.inspection_valid_until_year',
            'auto_ad.inspection_valid_until_year'  => 'nullable|required_with:auto_ad.inspection_valid_until_month',
            'status'                               => 'nullable',
            'external_id'                          => 'nullable',
            'source'                               => 'nullable',
            'images_processing_status'             => 'nullable',
        ];
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser instanceof User) {
            $rules['title']                  = 'required|string|min:5';
            $rules['description']            = 'required|string|min:5';
            $rules['auto_ad.interior_color'] = 'nullable';
            if ($currentUser->hasRole(RoleEnum::ADMIN)) {
                $rules['description'] = 'nullable';
            }
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
            'auto_ad.youtube_link' => [
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
            'auto_ad.options' => 'nullable',
        ];
    }

    /**
     * @return array
     */
    private function getContactValidationRules(): array
    {
        $rules = [
            'auto_ad.dealer_id'           => 'nullable',
            'auto_ad.dealer_show_room_id' => 'required_with:auto_ad.dealer_id',
            'auto_ad.first_name'          => 'required_without:auto_ad.dealer_id',
            'auto_ad.last_name'           => 'required_without:auto_ad.dealer_id',
            'auto_ad.email_address'       => 'required|email',
            'auto_ad.address'             => 'required',
            'auto_ad.zip_code'            => 'required',
            'auto_ad.city'                => 'required',
            'auto_ad.country'             => 'required',
            'auto_ad.mobile_number'       => 'required|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'auto_ad.landline_number'     => 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'auto_ad.whatsapp_number'     => 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'user_id'                     => 'required|exists:users,id',
            'market_id'                   => 'required|exists:markets,id',
        ];

        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (true === $currentUser->hasRole(RoleEnum::ADMIN)) {
            $rules['auto_ad.mobile_number']   = 'required';
            $rules['auto_ad.landline_number'] = 'nullable';
            $rules['auto_ad.whatsapp_number'] = 'nullable';
        }

        return $rules;
    }
}
