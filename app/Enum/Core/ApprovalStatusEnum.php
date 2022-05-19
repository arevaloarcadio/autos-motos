<?php
declare(strict_types=1);

namespace App\Enum\Core;

/**
 * Defines the possible values for ad status.
 *
 * @package App\Enum\Core
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
final class ApprovalStatusEnum
{
    public const PENDING_APPROVAL = 0;
    public const APPROVED         = 10;
    public const REJECTED         = 20;

    public const PENDING_APPROVAL_TEXT = 'pending_approval';
    public const APPROVED_TEXT         = 'approved';
    public const REJECTED_TEXT         = 'rejected';

    public static function getKeyValuePairs(): array
    {
        return [
            self::PENDING_APPROVAL => self::PENDING_APPROVAL_TEXT,
            self::APPROVED         => self::APPROVED_TEXT,
            self::REJECTED         => self::REJECTED_TEXT,
        ];
    }

    public static function getString(int $status): ?string
    {
        $pairs = self::getKeyValuePairs();

        if (isset($pairs[$status])) {
            return $pairs[$status];
        }

        return null;
    }
}
