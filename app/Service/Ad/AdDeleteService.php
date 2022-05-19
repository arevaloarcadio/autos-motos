<?php

declare(strict_types=1);

namespace App\Service\Ad;

use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\UnauthorizedException;

/**
 * @package App\Service\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdDeleteService
{
    public function deleteById(string $id): void
    {
        /** @var Ad $ad */
        $ad = Ad::query()->findOrFail($id);

        $this->delete($ad);
    }

    public function delete(Ad $ad): void
    {
        /** @var User $user */
        $user = Auth::user();
        if ( ! ($user->id === $ad->user->id) && false === $user->hasRole(RoleEnum::ADMIN)) {
            throw new UnauthorizedException();
        }

        $this->deleteImages($ad);
        $ad->delete();
    }

    private function deleteImages(Ad $ad): bool
    {
        if (false === Storage::disk('s3')->exists(sprintf('ads/%s', $ad->id))) {
            return true;
        }

        return Storage::disk('s3')->deleteDirectory(sprintf('ads/%s', $ad->id));
    }

    public function deleteAllFromVendor(Ad $ad): bool
    {
        $emailAddress = $ad->rentalOrMechanicEmailAddress();

        if (null === $emailAddress) {
            $this->delete($ad);

            return true;
        }

        $ads = Ad::query()
                 ->select('ads.*')
                 ->leftJoin('mechanic_ads as ma', 'ma.ad_id', '=', 'ads.id')
                 ->leftJoin('rental_ads as ra', 'ra.ad_id', '=', 'ads.id')
                 ->where('ma.email_address', '=', $emailAddress)
                 ->orWhere('ra.email_address', '=', $emailAddress)
                 ->get();

        foreach ($ads as $ad) {
            $this->delete($ad);
        }

        return true;
    }
}
