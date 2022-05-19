<?php

declare(strict_types=1);

namespace App\Service\Operation\Handler;

use App\Models\Operation;

/**
 * @package App\Service\Operation\Handler
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
interface IOperationHandler
{
    public function canHandle(Operation $operation): bool;

    public function handle(Operation $operation): Operation;
}
