<?php
declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for shop ad step.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class ShopAdStepEnum
{
    public const DETAILS = 1;
    public const IMAGES  = 2;
    public const CONTACT = 3;

    public const DETAILS_STRING = 'DETAILS';
    public const IMAGES_STRING  = 'IMAGES';
    public const CONTACT_STRING = 'CONTACT';

    public static function getAll(): array
    {
        return [
            self::DETAILS => self::DETAILS_STRING,
            self::IMAGES  => self::IMAGES_STRING,
            self::CONTACT => self::CONTACT_STRING,
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
