<?php
declare(strict_types=1);

namespace App\Service\Ad\Validator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\MechanicAdStepEnum;
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
class MechanicAdValidator implements IAdValidator
{
    public function supports(string $adType): bool
    {
        return AdTypeEnum::MECHANIC_SLUG === $adType;
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
        return $this->validator($input, $isUpdate)->validate();
    }

    /**
     * @param array $data
     * @param bool  $isUpdate
     *
     * @return ValidatorInterface
     */
    protected function validator(array $data, bool $isUpdate): ValidatorInterface
    {
        $rules = $this->getRules($isUpdate);

        return Validator::make(
            $data,
            $rules,
            [
                'images.min'                        => __('ads.validation_min_images'),
                'images.max'                        => __('ads.validation_max_images'),
                'mechanic_ad.mobile_number.regex'   => __('validation.invalid_phone_number'),
                'mechanic_ad.whatsapp_number.regex' => __('validation.invalid_phone_number'),
            ],
            [
                'mechanic_ad.address'         => 'address',
                'mechanic_ad.zip_code'        => 'zip code',
                'mechanic_ad.city'            => 'city',
                'mechanic_ad.country'         => 'country',
                'mechanic_ad.mobile_number'   => 'mobile number',
                'mechanic_ad.whatsapp_number' => 'whatsapp number',
                'mechanic_ad.website_url'     => 'website url',
                'mechanic_ad.latitude'        => 'address',
                'mechanic_ad.longitude'       => 'address',
            ]
        );
    }

    /**
     * @param bool $isUpdate
     *
     * @return array
     */
    private function getRules(bool $isUpdate): array
    {
        $rules = [
            'title'                       => 'required|string|min:10',
            'description'                 => 'required|string|min:30',
            'mechanic_ad.address'         => 'required',
            'mechanic_ad.zip_code'        => 'required',
            'mechanic_ad.city'            => 'required',
            'mechanic_ad.country'         => 'required',
            'mechanic_ad.latitude'        => 'required',
            'mechanic_ad.longitude'       => 'required',
            'mechanic_ad.mobile_number'   => 'required|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'mechanic_ad.whatsapp_number' => 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'mechanic_ad.website_url'     => 'nullable|url',
            'mechanic_ad.email_address'   => 'nullable|email',
            'user_id'                     => 'required|exists:users,id',
            'market_id'                   => 'required|exists:markets,id',
            'status'                      => 'nullable',
            'external_id'                 => 'nullable',
            'source'                      => 'nullable',
            'type'                        => 'required',
            'images'                      => 'required|array|min:1|max:1',
        ];

        if (true === $isUpdate) {
            $rules['images'] = 'sometimes|array|min:1|max:1';
        }

        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser instanceof User) {
            $rules['title']                 = 'required|string|min:5';
            $rules['description']           = 'required|string|min:5';
            $rules['mechanic_ad.latitude']  = 'nullable';
            $rules['mechanic_ad.longitude'] = 'nullable';
        }

        return $rules;
    }
}
