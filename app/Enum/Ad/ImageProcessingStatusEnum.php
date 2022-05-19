<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * Defines the possible values for image processing status.
 *
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
final class ImageProcessingStatusEnum
{
    public const PENDING       = 'PENDING';
    public const SUCCESSFUL    = 'SUCCESSFUL';
    public const ERRORED       = 'ERRORED';
    public const STARTED       = 'STARTED';
    public const NOT_AVAILABLE = 'N/A';
}
