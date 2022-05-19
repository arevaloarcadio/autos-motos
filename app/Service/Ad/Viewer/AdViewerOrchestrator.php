<?php

declare(strict_types=1);

namespace App\Service\Ad\Viewer;

use App\Exceptions\InvalidAdTypeProvidedException;
use App\Models\Ad;
use Illuminate\Http\JsonResponse;
use Traversable;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Viewer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdViewerOrchestrator
{
    /**
     * @var IAdViewer[]
     */
    private $viewers = [];

    public function __construct(Traversable $viewers)
    {
        $this->viewers = iterator_to_array($viewers);
    }

    /**
     * @param string $adType
     * @param string $slug
     *
     * @return View
     * @throws InvalidAdTypeProvidedException
     */
    public function presentAd(string $adType, string $slug): View
    {
        foreach ($this->getViewers() as $viewer) {
            if ($viewer->supports($adType)) {
                return $viewer->presentAd($adType, $slug);
            }
        }

        throw new InvalidAdTypeProvidedException();
    }

    /**
     * @param string $adType
     * @param string $slug
     *
     * @return JsonResponse
     * @throws InvalidAdTypeProvidedException
     */
    public function presentJsonAd(string $adType, string $slug): JsonResponse
    {
        foreach ($this->getViewers() as $viewer) {
            if ($viewer->supports($adType)) {
                return $viewer->presentJsonAd($adType, $slug);
            }
        }

        throw new InvalidAdTypeProvidedException();
    }

    /**
     * Get the value of the viewers property.
     *
     * @return IAdViewer[]
     */
    public function getViewers(): array
    {
        return $this->viewers;
    }
}
