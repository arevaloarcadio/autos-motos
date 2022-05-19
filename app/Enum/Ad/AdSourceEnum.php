<?php

declare(strict_types=1);

namespace App\Enum\Ad;

/**
 * @package App\Enum\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdSourceEnum
{
    public const PORTAL             = 'PORTAL';
    public const PORTAL_CLUB_IMPORT = 'PORTAL_CLUB_IMPORT';
    public const INVENTARIO_IMPORT  = 'INVENTARIO_IMPORT';
    public const MULTI_POST_IMPORT  = 'MULTI_POST_IMPORT';
    public const ANCOVE_IMPORT      = 'ANCOVE_IMPORT';
    public const RENTALS_IMPORT     = 'RENTALS_IMPORT';
    public const MECHANICS_IMPORT   = 'MECHANICS_IMPORT';
}
