<?php

declare(strict_types=1);

namespace App\Service\Ad;

use App\DTO\AdSubTypeDto;
use App\DTO\AdTypeDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @package App\Service\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 *
 * @method static Collection getTypes()
 * @method static string[] getAllSubTypeSlugs()
 * @method static string[] getAllSubTypeSlugsByTypeSlug(string $slug)
 * @method static AdTypeDto|null getTypeBySlug(string $slug)
 * @method static AdSubTypeDto|null getSubTypeBySlug(string $slug)
 * @method static string[] getAllVehicleLeavesSlugs()
 */
class AdTypeStorageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'adTypeStorage';
    }
}
