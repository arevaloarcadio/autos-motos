<?php
declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for ad image version type.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
final class AdImageVersionTypeEnum
{
    public const LARGE              = 'large';
    public const CAROUSEL_THUMBNAIL = 'carousel-thumbnail';
    public const THUMBNAIL          = 'thumbnail';

    public const LARGE_MAX_WIDTH  = 1280;
    public const LARGE_MAX_HEIGHT = 720;

    public const CAROUSEL_THUMBNAIL_MAX_WIDTH  = 300;
    public const CAROUSEL_THUMBNAIL_MAX_HEIGHT = 185;

    public const THUMBNAIL_MAX_WIDTH  = 750;
    public const THUMBNAIL_MAX_HEIGHT = 533;

    public const COMPANY_IMAGE_WIDTH  = 500;
    public const COMPANY_IMAGE_HEIGHT = 355;

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::LARGE              => [
                'width'  => self::LARGE_MAX_WIDTH,
                'height' => self::LARGE_MAX_HEIGHT,
            ],
            self::CAROUSEL_THUMBNAIL => [
                'width'  => self::CAROUSEL_THUMBNAIL_MAX_WIDTH,
                'height' => self::CAROUSEL_THUMBNAIL_MAX_HEIGHT,
            ],
            self::THUMBNAIL          => [
                'width'  => self::THUMBNAIL_MAX_WIDTH,
                'height' => self::THUMBNAIL_MAX_HEIGHT,
            ],
        ];
    }

    public static function getMetadataByType(string $type): array
    {
        $types = self::getTypes();
        if (array_key_exists($type, $types)) {
            return $types[$type];
        }

        return [];
    }
}
