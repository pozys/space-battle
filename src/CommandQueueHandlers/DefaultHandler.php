<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\CommandQueueHandlers;

use Pozys\SpaceBattle\ExceptionHandlers\ExceptionHandler;
use Pozys\SpaceBattle\Interfaces\CommandQueueHandlerInterface;
use SplQueue;

class DefaultHandler implements CommandQueueHandlerInterface
{
    public function handle(SplQueue $queue): ?CommandQueueHandlerInterface
    {
        global $nextHandler;
        $nextHandler = $nextHandler ?? $this;

        if ($queue->isEmpty()) {
            return $nextHandler;
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
