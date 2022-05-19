<?php

declare(strict_types=1);

namespace App\Enum\Operation;

/**
 * Defines the possible values for operation name.
 *
 * @package App\Enum\Operation
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class OperationNameEnum
{
    public const PERFORM_GEOCODING_AUTO_ADS     = 'PERFORM_GEOCODING_AUTO_ADS';
    public const PERFORM_GEOCODING_MECHANIC_ADS = 'PERFORM_GEOCODING_MECHANIC_ADS';

    public static function getAll(): array
    {
        return [
            self::PERFORM_GEOCODING_AUTO_ADS,
            self::PERFORM_GEOCODING_MECHANIC_ADS,
        ];
    }
}
