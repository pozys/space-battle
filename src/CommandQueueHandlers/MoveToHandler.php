<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\CommandQueueHandlers;

use Pozys\SpaceBattle\ExceptionHandlers\ExceptionHandler;
use Pozys\SpaceBattle\Interfaces\CommandQueueHandlerInterface;
use SplQueue;

class MoveToHandler implements CommandQueueHandlerInterface
{
    public function handle(SplQueue $queue): ?CommandQueueHandlerInterface
    {
        global $nextHandler;
        $nextHandler = $nextHandler ?? $this;

        if ($queue->isEmpty()) {
            return null;
        }

        $command = $queue->dequeue();

        try {
            HandlerManager::resolveHandler($this, $command);
        } catch (\Throwable $th) {
            ExceptionHandler::handle($command, $th)->execute();
        };

        return $nextHandler;
    }
}
