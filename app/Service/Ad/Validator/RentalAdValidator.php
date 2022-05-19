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
class RentalAdValidator implements IAdValidator
{
    public function supports(string $adType): bool
    {
        return AdTypeEnum::RENTAL_SLUG === $adType;
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
                'images.min'                      => __('ads.validation_min_images'),
                'images.max'                      => __('ads.validation_max_images'),
                'rental_ad.mobile_number.regex'   => __('validation.invalid_phone_number'),
                'rental_ad.whatsapp_number.regex' => __('validation.invalid_phone_number'),
            ],
            [
                'rental_ad.address'         => 'address',
                'rental_ad.zip_code'        => 'zip code',
                'rental_ad.city'            => 'city',
                'rental_ad.country'         => 'country',
                'rental_ad.mobile_number'   => 'mobile number',
                'rental_ad.whatsapp_number' => 'whatsapp number',
                'rental_ad.website_url'     => 'website url',
                'rental_ad.latitude'        => 'address',
                'rental_ad.longitude'       => 'address',
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
            'title'                     => 'required|string|min:10',
            'description'               => 'required|string|min:30',
            'rental_ad.address'         => 'required',
            'rental_ad.zip_code'        => 'required',
            'rental_ad.city'            => 'required',
            'rental_ad.country'         => 'required',
            'rental_ad.latitude'        => 'required',
            'rental_ad.longitude'       => 'required',
            'rental_ad.mobile_number'   => 'required|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'rental_ad.whatsapp_number' => 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'rental_ad.website_url'     => 'nullable|url',
            'rental_ad.email_address'   => 'nullable|email',
            'user_id'                   => 'required|exists:users,id',
            'market_id'                 => 'required|exists:markets,id',
            'status'                    => 'nullable',
            'external_id'               => 'nullable',
            'source'                    => 'nullable',
            'type'                      => 'required',
            'images'                    => 'required|array|min:1|max:1',
        ];

        if (true === $isUpdate) {
            $rules['images'] = 'sometimes|array|min:1|max:1';
        }

        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser instanceof User) {
            $rules['title']               = 'required|string|min:5';
            $rules['description']         = 'required|string|min:5';
            $rules['rental_ad.latitude']  = 'nullable';
            $rules['rental_ad.longitude'] = 'nullable';
        }

        return $rules;
    }
}
