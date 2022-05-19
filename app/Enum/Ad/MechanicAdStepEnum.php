<?php
declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class MechanicAdStepEnum
{
    public const DETAILS = 1;

    public const DETAILS_STRING = 'DETAILS';

    public static function getAll(): array
    {
        return [
            self::DETAILS => self::DETAILS_STRING,
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
