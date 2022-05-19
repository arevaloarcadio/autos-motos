<?php
declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for auto ad step.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class VehicleAdStepEnum
{
    public const TRUCK_TYPE   = 0;
    public const VEHICLE_DATA = 1;
    public const DETAILS      = 2;
    public const IMAGES       = 3;
    public const OPTIONS      = 4;
    public const CONTACT      = 5;

    public const TRUCK_TYPE_STRING   = 'TRUCK_TYPE';
    public const VEHICLE_DATA_STRING = 'VEHICLE_DATA';
    public const DETAILS_STRING      = 'DETAILS';
    public const IMAGES_STRING       = 'IMAGES';
    public const OPTIONS_STRING      = 'OPTIONS';
    public const CONTACT_STRING      = 'CONTACT';

    public static function getAll(): array
    {
        return [
            self::TRUCK_TYPE   => self::TRUCK_TYPE_STRING,
            self::VEHICLE_DATA => self::VEHICLE_DATA_STRING,
            self::DETAILS      => self::DETAILS_STRING,
            self::IMAGES       => self::IMAGES_STRING,
            self::OPTIONS      => self::OPTIONS_STRING,
            self::CONTACT      => self::CONTACT_STRING,
        ];
    }

    public static function getStringByStepNumber(?int $number = null): ?string
    {
        if (null === $number) {
            return null;
        }
        $options = self::getAll();

        return $options[$number] ?? null;
    }
}
