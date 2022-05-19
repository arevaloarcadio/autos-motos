<?php
declare(strict_types=1);

namespace App\Enum;

/**
 * Defines the possible values for pagination metadata defaults.
 *
 * @package App\Enum
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
final class PaginationMetadataDefaultsEnum
{
    public const ITEMS_PER_PAGE  = 25;
    public const ORDER_BY_COLUMN = 'created_at';
    public const ORDER_BY_DIR    = 'desc';
}
