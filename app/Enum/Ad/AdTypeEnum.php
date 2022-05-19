<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdTypeEnum
{
    public const AUTO_NAME        = 'auto';
    public const AUTO_SLUG        = 'auto';
    public const MOTO_NAME        = 'moto';
    public const MOTO_SLUG        = 'moto';
    public const MOBILE_HOME_NAME = 'mobile_home';
    public const MOBILE_HOME_SLUG = 'mobile-home';
    public const TRUCK_NAME       = 'truck';
    public const TRUCK_SLUG       = 'truck';
    public const MECHANIC_NAME    = 'mechanic';
    public const MECHANIC_SLUG    = 'mechanic';
    public const RENTAL_NAME      = 'rental';
    public const RENTAL_SLUG      = 'rental';
    public const SHOP_NAME        = 'shop';
    public const SHOP_SLUG        = 'shop';

    /**
     * @return string[]
     */
    public static function getAllSlugs(): array
    {
        return [
            AdTypeEnum::AUTO_SLUG,
            AdTypeEnum::MOTO_SLUG,
            AdTypeEnum::MOBILE_HOME_SLUG,
            AdTypeEnum::TRUCK_SLUG,
            AdTypeEnum::MECHANIC_SLUG,
            AdTypeEnum::RENTAL_SLUG,
            AdTypeEnum::SHOP_SLUG,
        ];
    }
}
