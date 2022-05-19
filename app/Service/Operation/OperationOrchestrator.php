<?php

declare(strict_types=1);

namespace App\Service\Operation;

use App\Enum\Operation\OperationStatusEnum;
use App\Exceptions\UnsupportedOperationException;
use App\Models\Operation;
use App\Service\Operation\Handler\IOperationHandler;
use Illuminate\Support\Facades\Cache;
use Throwable;
use Traversable;

/**
 * @package App\Service\Operation
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class OperationOrchestrator
{
    /**
     * @var IOperationHandler[]
     */
    private $handlers = [];

    public function __construct(Traversable $handlers)
    {
        $this->handlers = iterator_to_array($handlers);
    }

    public function run(Operation $operation): Operation
    {
        $lock = Cache::lock(sprintf('operation_run_%s', $operation->id), 300);

        if (false === $lock->get()) {
            return $operation;
        }

        $operation->status = OperationStatusEnum::STARTED;
        $operation->save();

        try {
            foreach ($this->handlers as $handler) {
                if (false === $handler->canHandle($operation)) {
                    continue;
                }


                $output = $handler->handle($operation);

                $operation->status = OperationStatusEnum::SUCCESSFUL;
                $operation->save();

                $lock->release();

                return $output;
            }

            throw new UnsupportedOperationException();
        } catch (Throwable $exception) {
            $operation->status      = OperationStatusEnum::FAILED;
            $operation->status_text = $exception->getMessage();

            $operation->save();

            $lock->release();
        }


        return $operation;
    }
}
