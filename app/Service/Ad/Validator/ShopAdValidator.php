<?php
declare(strict_types=1);

namespace App\Service\Ad\Validator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\ShopAdStepEnum;
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
class ShopAdValidator implements IAdValidator
{
    public function supports(string $adType): bool
    {
        return AdTypeEnum::SHOP_SLUG === $adType;
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
                'shop_ad.youtube_link.regex'    => __('ads.validation_youtube_link'),
                'images.min'                    => __('ads.validation_min_images'),
                'images.max'                    => __('ads.validation_max_images'),
                'shop_ad.mobile_number.regex'   => __('validation.invalid_phone_number'),
                'shop_ad.whatsapp_number.regex' => __('validation.invalid_phone_number'),
                'shop_ad.landline_number.regex' => __('validation.invalid_phone_number'),
            ],
            [
                'shop_ad.make_id'             => 'make',
                'shop_ad.model'               => 'model',
                'shop_ad.code'                => 'code',
                'shop_ad.condition'           => 'condition',
                'shop_ad.price'               => 'price',
                'shop_ad.category'            => 'category',
                'shop_ad.dealer_id'           => 'dealer',
                'shop_ad.dealer_show_room_id' => 'dealer show room',
                'shop_ad.first_name'          => 'first name',
                'shop_ad.last_name'           => 'last name',
                'shop_ad.email_address'       => 'email address',
                'shop_ad.address'             => 'address',
                'shop_ad.latitude'            => 'address',
                'shop_ad.longitude'           => 'address',
                'shop_ad.zip_code'            => 'zip code',
                'shop_ad.city'                => 'city',
                'shop_ad.country'             => 'country',
                'shop_ad.mobile_number'       => 'mobile number',
                'shop_ad.landline_number'     => 'landline number',
                'shop_ad.whatsapp_number'     => 'whatsapp number',
                'shop_ad.youtube_link'        => 'youtube link',
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
            case ShopAdStepEnum::DETAILS:
                return $this->getDetailsValidationRules();
            case ShopAdStepEnum::IMAGES:
                return $this->getImageRules();
            case ShopAdStepEnum::CONTACT:
                return $this->getContactValidationRules();
            default:
                return array_merge(
                    $this->getDetailsValidationRules(),
                    $this->getImageRules(),
                    $this->getContactValidationRules(),
                    ['type' => ['required', Rule::in(AdTypeEnum::getAllSlugs())]]
                );
        }
    }

    /**
     * @return array
     */
    private function getDetailsValidationRules(): array
    {
        $rules = [
            'title'                      => 'required|string|min:10',
            'description'                => 'required|string|min:30',
            'shop_ad.make_id'            => 'required',
            'shop_ad.model'              => 'nullable',
            'shop_ad.code'               => 'nullable',
            'shop_ad.manufacturer'       => 'required',
            'shop_ad.category'           => 'required',
            'shop_ad.condition'          => 'required',
            'shop_ad.price'              => 'required',
            'shop_ad.price_contains_vat' => 'nullable|boolean',
            'status'                     => 'nullable',
            'external_id'                => 'nullable',
            'source'                     => 'nullable',
            'images_processing_status'   => 'nullable',
        ];
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser instanceof User) {
            $rules['title']       = 'required|string|min:5';
            $rules['description'] = 'required|string|min:5';

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
            'shop_ad.youtube_link' => [
                'nullable',
                sprintf('regex:%s', RegexEnum::YOUTUBE_LINK),
            ],
            'images'               => $rules,
        ];
    }

    /**
     * @return array
     */
    private function getContactValidationRules(): array
    {
        $rules = [
            'shop_ad.dealer_id'           => 'nullable',
            'shop_ad.dealer_show_room_id' => 'required_with:shop_ad.dealer_id',
            'shop_ad.first_name'          => 'required_without:shop_ad.dealer_id',
            'shop_ad.last_name'           => 'required_without:shop_ad.dealer_id',
            'shop_ad.email_address'       => 'required|email',
            'shop_ad.address'             => 'required',
            'shop_ad.latitude'            => 'required',
            'shop_ad.longitude'           => 'required',
            'shop_ad.zip_code'            => 'required',
            'shop_ad.city'                => 'required',
            'shop_ad.country'             => 'required',
            'shop_ad.mobile_number'       => 'required|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'shop_ad.landline_number'     => 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'shop_ad.whatsapp_number'     => 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'user_id'                     => 'required|exists:users,id',
            'market_id'                   => 'required|exists:markets,id',
        ];

        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (true === $currentUser->hasRole(RoleEnum::ADMIN)) {
            $rules['shop_ad.mobile_number']   = 'required';
            $rules['shop_ad.landline_number'] = 'nullable';
            $rules['shop_ad.whatsapp_number'] = 'nullable';
        }

        return $rules;
    }
}
