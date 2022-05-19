<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for bed.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class BedEnum
{
    public const DOUBLE       = 'double';
    public const FRENCH_BEDS  = 'french_beds';
    public const BUNK_BEDS    = 'bunk_beds';
    public const SINGLE_BEDS  = 'single_beds';
    public const LIFTING_BEDS = 'lifting_beds';

    public static function getAll(): array
    {
        return [
            self::DOUBLE       => self::DOUBLE,
            self::FRENCH_BEDS  => self::FRENCH_BEDS,
            self::BUNK_BEDS    => self::BUNK_BEDS,
            self::SINGLE_BEDS  => self::SINGLE_BEDS,
            self::LIFTING_BEDS => self::LIFTING_BEDS,
        ];
    }
}
