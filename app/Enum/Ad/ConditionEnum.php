<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for condition.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class ConditionEnum
{
    public const NEW     = 'new';
    public const USED    = 'used';
    public const DAMAGED = 'damaged';
    public const OTHER   = 'other';

    public static function getAll(): array
    {
        return [
            self::NEW     => self::NEW,
            self::USED    => self::USED,
            self::DAMAGED => self::DAMAGED,
            self::OTHER   => self::OTHER,
        ];
    }

    public static function getAllTranslated(): array
    {
        return [
            self::NEW     => __(sprintf('condition.%s', self::NEW)),
            self::USED    => __(sprintf('condition.%s', self::USED)),
            self::DAMAGED => __(sprintf('condition.%s', self::DAMAGED)),
            self::OTHER   => __(sprintf('condition.%s', self::OTHER)),
        ];
    }

    public static function getAllForShop(): array
    {
        return [
            self::NEW   => self::NEW,
            self::USED  => self::USED,
            self::OTHER => self::OTHER,
        ];
    }

    public static function getAllTranslatedForShop(): array
    {
        return [
            self::NEW     => __(sprintf('condition.%s', self::NEW)),
            self::USED    => __(sprintf('condition.%s', self::USED)),
            self::OTHER   => __(sprintf('condition.%s', self::OTHER)),
        ];
    }
}
