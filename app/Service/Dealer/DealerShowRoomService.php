<?php

declare(strict_types=1);

namespace App\Service\Dealer;

use App\Manager\Dealer\DealerShowRoomManager;
use App\Models\DealerShowRoom;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Support\Facades\Validator;

/**
 * @package App\Service\Dealer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class DealerShowRoomService
{
    public function create(array $input): DealerShowRoom
    {
        $dealerShowRoom = new DealerShowRoom($input);
        $dealerShowRoom->save();

        return $dealerShowRoom;
    }

    public function validateAndCreate(array $input): DealerShowRoom
    {
        $this->validator($input)->validate();

        return $this->create($input);
    }

    public function update(string $id, array $input): DealerShowRoom
    {
        $this->validator($input)->validate();

        /** @var DealerShowRoom $showRoom */
        $showRoom = DealerShowRoom::query()->findOrFail($id);
        $showRoom->update($input);

        return $showRoom;
    }

    protected function validator(array $data): ValidatorInterface
    {
        $rules = [
            'name'            => 'required',
            'address'         => 'required',
            'zip_code'        => 'required',
            'city'            => 'required',
            'market_id'       => 'required|exists:markets,id',
            'email_address'   => 'required|email',
            'mobile_number'   => 'required|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'whatsapp_number' => 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'landline_number' => 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'latitude'        => 'required',
            'longitude'       => 'required',
        ];

        return Validator::make(
            $data,
            $rules,
            [
                'address.required'      => __('validation.choose_address'),
                'latitude.required'     => __('validation.choose_address'),
                'longitude.required'    => __('validation.choose_address'),
                'mobile_number.regex'   => __('validation.invalid_phone_number'),
                'whatsapp_number.regex' => __('validation.invalid_phone_number'),
                'landline_number.regex' => __('validation.invalid_phone_number'),
            ],
            [
                'latitude'  => 'address',
                'longitude' => 'address',
            ]
        );
    }
}
