<?php

declare(strict_types=1);

namespace App\Service\Ad\Viewer;

use App\Enum\Ad\AdTypeEnum;
use App\Models\Ad;
use App\Service\Ad\Finder\RentalAdFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @package App\Service\Ad\Viewer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class RentalAdViewer implements IAdViewer
{
    /**
     * @var RentalAdFinder
     */
    private $finder;

    public function __construct(RentalAdFinder $finder)
    {
        $this->finder = $finder;
    }

    public function supports(string $adType): bool
    {
        return $adType === AdTypeEnum::RENTAL_SLUG;
    }

    public function presentAd(string $adType, string $slug): View
    {
        throw new NotFoundHttpException();
    }

    public function presentJsonAd(string $adType, string $slug): JsonResponse
    {
        $ad = Ad::with(['rentalAd'])
                ->whereSlug($slug)
                ->firstOrFail();

        return new JsonResponse($ad, JsonResponse::HTTP_OK);
    }
}
