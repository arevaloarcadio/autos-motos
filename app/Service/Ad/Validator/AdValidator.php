<?php

declare(strict_types=1);

namespace App\Service\Ad\Validator;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\User\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @package App\Service\Ad\Validator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdValidator
{
    public function validate(array $input): array
    {
        return $this->validator($input)->validate();
    }

    public function validateImages(array $input): array
    {
        return Validator::make(
            $input,
            ['images' => 'nullable']
        )->validate();
    }

    private function validator(array $input): ValidatorInterface
    {
        return Validator::make(
            $input,
            $this->getRules()
        );
    }

    private function getRules(): array
    {
        $rules = [
            'title'                    => 'required|string|min:10',
            'description'              => 'required|string|min:30',
            'type'                     => ['required', Rule::in(AdTypeEnum::getAllSlugs())],
            'user_id'                  => 'required_without:external_id|exists:users,id',
            'market_id'                => 'required|exists:markets,id',
            'images'                   => 'nullable',
            'external_id'              => 'nullable',
            'source'                   => 'nullable',
            'status'                   => 'nullable',
            'images_processing_status' => 'nullable',
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
}
