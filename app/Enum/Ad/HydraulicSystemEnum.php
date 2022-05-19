<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for condition.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class HydraulicSystemEnum
{
    public const TILTING_HYDRAULICS = 'tilting_hydraulics';
    public const MOVING_FLOOR       = 'moving_floor';
    public const TANK_TRUCK         = 'tank_truck';
    public const OTHER              = 'other';

    public static function getAll(): array
    {
        return [
            self::TILTING_HYDRAULICS => self::TILTING_HYDRAULICS,
            self::MOVING_FLOOR       => self::MOVING_FLOOR,
            self::TANK_TRUCK         => self::TANK_TRUCK,
            self::OTHER              => self::OTHER,
        ];
    }
}
