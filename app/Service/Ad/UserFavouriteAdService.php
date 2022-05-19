<?php

declare(strict_types=1);

namespace App\Service\Ad;

use App\Enum\User\RoleEnum;
use App\Input\PaginationMetadataInput;
use App\Models\Ad;
use App\Models\UserFavouriteAd;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * @package App\Service\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class UserFavouriteAdService
{
    public function countAllForCurrentUser(): int
    {
        if (null === Auth::user()) {
            throw new AuthenticationException();
        }

        return $this->getFavouritedAdsQuery(true)->count();
    }

    public function findAllForCurrentUser(PaginationMetadataInput $input, ?string $term = null): LengthAwarePaginator
    {
        $query = $this->getFavouritedAdsQuery(false, $term);

        /** @var LengthAwarePaginator $ads */
        $ads = $query->orderBy('ads.created_at', 'asc')
                     ->paginate($input->getItemsPerPage());

        $ads->getCollection()->transform(
            function (Ad $item) {
                $item->is_favourite = true;

                return $item;
            }
        );

        return $ads;
    }

    public function findFavouritedAdIdsForCurrentUser(): Collection
    {
        $query = $this->getFavouritedAdsQuery(true);

        return $query->get();
    }

    private function getFavouritedAdsQuery(bool $selectOnlyIds = false, ?string $term = null): Builder
    {
        $query = Ad::query()
                   ->with(
                       $selectOnlyIds ? [] : [
                           'autoAd',
                           'motoAd',
                           'mobileHomeAd',
                           'truckAd',
                           'mechanicAd',
                           'rentalAd',
                           'shopAd',
                       ]
                   )
                   ->select($selectOnlyIds ? 'ads.id' : 'ads.*')
                   ->withCount('images')
                   ->join('user_favourite_ads', 'ads.id', '=', 'user_favourite_ads.ad_id')
                   ->where('user_favourite_ads.user_id', '=', Auth::user()->id);

        if ( ! (null === $term)) {
            $query->where('title', 'like', sprintf('%%%s%%', $term));
        }

        return $query;
    }

    public function create(array $input): UserFavouriteAd
    {
        $input['user_id'] = Auth::user()->id;

        /** @var UserFavouriteAd $userFavouriteAd */
        $userFavouriteAd = UserFavouriteAd::query()->firstOrCreate($input);

        return $userFavouriteAd;
    }

    public function deleteByAdId(string $adId): void
    {
        UserFavouriteAd::query()
                       ->where('ad_id', '=', $adId)
                       ->where('user_id', '=', Auth::user()->id)
                       ->delete();
    }
}
