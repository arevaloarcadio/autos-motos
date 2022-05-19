<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for color.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class ColorEnum
{
    public const RED    = 'red';
    public const BLUE   = 'blue';
    public const SILVER = 'silver';
    public const WHITE  = 'white';
    public const BLACK  = 'black';
    public const BROWN  = 'brown';
    public const GRAY   = 'gray';
    public const GOLD   = 'gold';
    public const GREEN  = 'green';
    public const BEIGE  = 'beige';
    public const OTHER  = 'other';
    
    public static function getAll(): array
    {
        return [
            self::RED    => self::RED,
            self::BLUE   => self::BLUE,
            self::SILVER => self::SILVER,
            self::WHITE  => self::WHITE,
            self::BLACK  => self::BLACK,
            self::BROWN  => self::BROWN,
            self::GRAY   => self::GRAY,
            self::GOLD   => self::GOLD,
            self::GREEN  => self::GREEN,
            self::BEIGE  => self::BEIGE,
            self::OTHER  => self::OTHER,
        ];
    }
}
