<?php
declare(strict_types=1);

namespace App\Enum\Data;

/**
 * @package App\Enum\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
final class SpecificationNameEnum
{
    public const BODY_TYPE    = 'Body type';
    public const TRANSMISSION = 'Gearbox type';
    public const FUEL_TYPE    = 'Fuel';
    public const WHEEL_DRIVE  = 'Drive wheels';

    public const BODY_TYPE_ALIAS    = 'body_type';
    public const TRANSMISSION_ALIAS = 'transmission_type';
    public const FUEL_TYPE_ALIAS    = 'fuel_type';
    public const WHEEL_DRIVE_ALIAS  = 'wheel_drive_type';

    public static function getAll(): array
    {
        return [
            self::BODY_TYPE_ALIAS    => self::BODY_TYPE,
            self::TRANSMISSION_ALIAS => self::TRANSMISSION,
            self::WHEEL_DRIVE_ALIAS  => self::WHEEL_DRIVE,
            self::FUEL_TYPE_ALIAS    => self::FUEL_TYPE,
        ];
    }

    public static function getNameByAlias(string $alias): ?string
    {
        $names = self::getAll();

        if (isset($names[$alias])) {
            return $names[$alias];
        }

        return null;
    }
}
