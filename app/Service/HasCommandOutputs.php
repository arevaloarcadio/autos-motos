<?php
declare(strict_types=1);

namespace App\Service;

use Illuminate\Console\Command;

/**
 * @package App\Service
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 * @date    12/12/2020
 */
trait HasCommandOutputs
{
    /**
     * @param Command $command
     * @param string  $message
     */
    protected function outputProgress(Command $command, string $message): void
    {
        $command->info(
            sprintf(
                '%s - RAM Used: %s',
                $message,
                $this->getMemoryUsed()
            )
        );
    }

    protected function getMemoryUsed(): string
    {
        return sprintf("%sMB", intval(memory_get_usage(true) / 1024 / 1024));
    }
}
