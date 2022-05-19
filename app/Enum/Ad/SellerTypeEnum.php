<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for seller type.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class SellerTypeEnum
{
    public const PRIVATE_SELLER = 'private_seller';
    public const DEALERSHIP     = 'dealership';


    public static function getAll(): array
    {
        return [
            self::PRIVATE_SELLER => self::PRIVATE_SELLER,
            self::DEALERSHIP     => self::DEALERSHIP,
        ];
    }
}
