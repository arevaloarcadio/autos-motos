<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class TruckTypeEnum
{
    public const TRUCK_UP_TO_7PT5_T   = 'truck-up-to-7pt5-t';
    public const TRUCK_OVER_7PT5_T    = 'truck-over-7pt5-t';
    public const SEMI_TRAILER_TRUCK   = 'semi-trailer-truck';
    public const TRAILER              = 'trailer';
    public const SEMI_TRAILER         = 'semi-trailer';
    public const CONSTRUCTION_MACHINE = 'construction-machine';
    public const BUS                  = 'bus';
    public const AGRICULTURE_VEHICLE  = 'agriculture-vehicle';
    public const FORKLIFT             = 'forklift';


    /**
     * @return string[]
     */
    public static function getAll(): array
    {
        return [
            TruckTypeEnum::TRUCK_UP_TO_7PT5_T,
            TruckTypeEnum::TRUCK_OVER_7PT5_T,
            TruckTypeEnum::SEMI_TRAILER_TRUCK,
            TruckTypeEnum::TRAILER,
            TruckTypeEnum::SEMI_TRAILER,
            TruckTypeEnum::CONSTRUCTION_MACHINE,
            TruckTypeEnum::BUS,
            TruckTypeEnum::AGRICULTURE_VEHICLE,
            TruckTypeEnum::FORKLIFT,
        ];
    }
}
