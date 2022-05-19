<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Ad;
use App\Models\RentalAd;
use App\Models\OnboardingRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * @package App\Service\User
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class RegisterOnboardingService
{
    /**
     * @var UserCreateService
     */
    private $userService;

    /**
     * @var OnboardingRequestService
     */
    private $onboardingRequestService;

    public function __construct(UserCreateService $userService, OnboardingRequestService $onboardingRequestService)
    {
        $this->userService              = $userService;
        $this->onboardingRequestService = $onboardingRequestService;
    }

    public function register(array $data, string $token): User
    {
        $onboardingRequest = $this->onboardingRequestService->checkToken($token);
        $user              = $this->userService->create($data);

        $emailAddressOnAd = $onboardingRequest->ad->rentalOrMechanicEmailAddress();

        if (null === $emailAddressOnAd) {
            $onboardingRequest->ad->user()->associate($user);
            $onboardingRequest->ad->save();
            $this->markOnboardingRequestAsConfirmed($onboardingRequest);

            return $user;
        }

        $this->assignAdsToUser($emailAddressOnAd, $user);
        $this->markOnboardingRequestAsConfirmed($onboardingRequest);

        return $user;
    }

    public function assignAdsToUser(string $emailAddress, User $user): void
    {
        Ad::query()->join('mechanic_ads as ma', 'ma.ad_id', '=', 'ads.id')
          ->where('ma.email_address', '=', $emailAddress)
          ->update(['ads.user_id' => $user->id]);
        Ad::query()->join('rental_ads as ra', 'ra.ad_id', '=', 'ads.id')
          ->where('ra.email_address', '=', $emailAddress)
          ->update(['ads.user_id' => $user->id]);
    }

    private function markOnboardingRequestAsConfirmed(OnboardingRequest $onboardingRequest): OnboardingRequest
    {
        $onboardingRequest->confirmed = true;
        $onboardingRequest->save();

        return $onboardingRequest;
    }

    public function assignAdsToUserFromInput(array $input): User
    {
        $input = Validator::make(
            $input,
            [
                'email_address' => 'required|email',
                'ad_id'         => 'required|exists:ads,id',
            ]
        )->validate();
        /** @var Ad $ad */
        $ad = Ad::query()->where('id', '=', $input['ad_id'])->firstOrFail();
        /** @var User $user */
        $user = User::query()->where('email', '=', $input['email_address'])->firstOrFail();

        $emailAddressOnAd = $ad->rentalOrMechanicEmailAddress();

        if (null === $emailAddressOnAd) {
            $ad->user()->associate($user);
            $ad->save();

            return $user;
        }

        $this->assignAdsToUser($emailAddressOnAd, $user);

        return $user;
    }
}
