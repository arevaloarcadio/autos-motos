<?php
declare(strict_types=1);

namespace App\Enum\Core;

/**
 * Defines the possible values for regex.
 *
 * @package App\Enum\Core
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
final class RegexEnum
{
    public const YOUTUBE_LINK = '/^https:\/\/(?:www\.)?youtube.com\/embed\/[A-z0-9]{11}$/m';
}
