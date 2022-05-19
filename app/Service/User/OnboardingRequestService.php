<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Models\Ad;
use App\Models\OnboardingRequest;
use App\Notifications\OnboardingRequestCreated;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @package App\Service\User
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class OnboardingRequestService
{
    public function create(array $data): OnboardingRequest
    {
        $input = $this->validate($data);
        $this->expireExistingRequests($input['ad_id'], $input['email_address']);

        $request                   = new OnboardingRequest();
        $request->ad_id            = $input['ad_id'];
        $request->email_address    = $input['email_address'];
        $request->token            = Str::random(32);
        $request->token_expires_at = Carbon::now()->add(CarbonInterval::weeks(2))->endOfDay();
        $request->save();

        $request->notify(new OnboardingRequestCreated());

        return $request;
    }

    private function expireExistingRequests(string $adId, string $emailAddress): void
    {
        /** @var Ad $ad */
        $ad = Ad::query()->where('id', '=', $adId)->first();
        if (null === $ad) {
            return;
        }

        OnboardingRequest::query()
                         ->where('email_address', '=', $emailAddress)
                         ->orWhere('ad_id', '=', $adId)
                         ->update(['token_expires_at' => Carbon::now()->subtract(CarbonInterval::day())]);
    }

    private function validate(array $data): array
    {
        return Validator::make(
            $data,
            [
                'email_address' => 'required|email|unique:users,email',
                'ad_id'         => 'required|exists:ads,id',
            ],
            [
                'email_address.unique' => 'user_already_exists',
            ]
        )->validate();
    }

    public function checkToken(string $token): ?OnboardingRequest
    {
        $instance = OnboardingRequest::query()
                                     ->where('token', '=', $token)
                                     ->where('confirmed', '=', false)
                                     ->where('token_expires_at', '>', Carbon::now())
                                     ->first();

        if ($instance instanceof OnboardingRequest) {
            $instance->access_count += 1;
            $instance->save();

            return $instance;
        }

        return null;
    }
}
