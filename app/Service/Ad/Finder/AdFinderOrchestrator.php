<?php

declare(strict_types=1);

namespace App\Service\Ad\Finder;

use App\Exceptions\InvalidAdTypeProvidedException;
use App\Models\Ad;
use App\Service\Ad\UserFavouriteAdService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Traversable;

/**
 * @package App\Service\Ad\Finder
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdFinderOrchestrator
{
    /**
     * @var IAdFinder[]
     */
    private $finders = [];

    /**
     * @var UserFavouriteAdService
     */
    private $userFavouriteAdService;

    public function __construct(Traversable $finders, UserFavouriteAdService $userFavouriteAdService)
    {
        $this->finders                = iterator_to_array($finders);
        $this->userFavouriteAdService = $userFavouriteAdService;
    }

    /**
     * @param string      $adType
     * @param string|null $adSubType
     * @param array       $input
     *
     * @return int
     * @throws InvalidAdTypeProvidedException
     */
    public function countAds(string $adType, ?string $adSubType, array $input): int
    {
        foreach ($this->getFinders() as $finder) {
            if (false === $finder->supports($adType)) {
                continue;
            }
            if ( ! (null === $adSubType)) {
                $input['subtype'] = $adSubType;
            }

            return $finder->count($adType, $input);
        }

        throw new InvalidAdTypeProvidedException();
    }

    /**
     * @param string      $adType
     * @param string|null $adSubType
     * @param array       $input
     *
     * @return LengthAwarePaginator
     * @throws InvalidAdTypeProvidedException
     */
    public function findAds(string $adType, ?string $adSubType, array $input): LengthAwarePaginator
    {
        foreach ($this->getFinders() as $finder) {
            if (false === $finder->supports($adType)) {
                continue;
            }

            if ( ! (null === $adSubType)) {
                $input['subtype'] = $adSubType;
            }
            /** @var LengthAwarePaginator $ads */
            $ads = $finder->find($adType, $input);
            $ads = $this->checkFavourites($ads);

            return $ads;
        }

        throw new InvalidAdTypeProvidedException();
    }

    /**
     * @param string      $adType
     * @param string|null $adSubType
     * @param array       $input
     *
     * @return View
     * @throws InvalidAdTypeProvidedException
     */
    public function findAndPresentAds(string $adType, ?string $adSubType, array $input): View
    {
        foreach ($this->getFinders() as $finder) {
            if (false === $finder->supports($adType)) {
                continue;
            }

            if ( ! (null === $adSubType)) {
                $input['subtype'] = $adSubType;
            }
            /** @var LengthAwarePaginator $ads */
            $ads = $finder->find($adType, $input);
            $ads = $this->checkFavourites($ads);

            return $finder->presentAds($ads, $adType, $adSubType);
        }

        throw new InvalidAdTypeProvidedException();
    }

    private function checkFavourites(LengthAwarePaginator $ads): LengthAwarePaginator
    {
        if (null === Auth::user()) {
            return $ads;
        }

        $favouriteAds = $this->userFavouriteAdService->findFavouritedAdIdsForCurrentUser();
        $idsArray     = array_column($favouriteAds->toArray(), 'id');

        $ads->getCollection()->transform(
            function (Ad $item) use ($idsArray) {
                $item->is_favourite = in_array($item->id, $idsArray);

                return $item;
            }
        );

        return $ads;
    }

    /**
     * Get the value of the finders property.
     *
     * @return IAdFinder[]
     */
    public function getFinders(): array
    {
        return $this->finders;
    }
}
