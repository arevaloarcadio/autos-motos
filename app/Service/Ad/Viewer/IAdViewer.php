<?php
declare(strict_types=1);

namespace App\Service\Ad\Viewer;

use App\Models\Ad;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Viewer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
interface IAdViewer
{
    public function supports(string $adType): bool;
    
    public function presentAd(string $adType, string $slug): View;

    public function presentJsonAd(string $adType, string $slug): JsonResponse;
}
