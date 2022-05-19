<?php

declare(strict_types=1);

namespace App\Enum\Operation;

/**
 * Defines the possible values for operation status.
 *
 * @package App\Enum\Operation
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
final class OperationStatusEnum
{
    public const PENDING    = 'PENDING';
    public const STARTED    = 'STARTED';
    public const SUCCESSFUL = 'SUCCESSFUL';
    public const FAILED     = 'FAILED';
}
