<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class CabEnum
{
    public const LONG_DISTANCE_TRANSPORT = 'long_distance_transport';
    public const LOCAL_TRANSPORT         = 'local_transport';
    public const OTHER                   = 'other';

    public static function getAll(): array
    {
        return [
            self::LONG_DISTANCE_TRANSPORT => self::LONG_DISTANCE_TRANSPORT,
            self::LOCAL_TRANSPORT         => self::LOCAL_TRANSPORT,
            self::OTHER                   => self::OTHER,
        ];
    }
}
