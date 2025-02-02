<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Interfaces\{CommandInterface, CommandQueueHandlerInterface};
use SplQueue;

class SoftStopCommand implements CommandInterface
{
    public function execute(): void
    {
        global $nextHandler;
        $currentHandler = clone $nextHandler;

        $nextHandler = new class($currentHandler) implements CommandQueueHandlerInterface {
            public function __construct(private readonly CommandQueueHandlerInterface $currentHandler) {}

            public function handle(SplQueue $queue): ?CommandQueueHandlerInterface
            {
                global $nextHandler;
                $nextHandler = $this;

                if ($queue->isEmpty()) {
                    return null;
                }

                $this->currentHandler->handle($queue);

                return $nextHandler;
            }
        };
    }
}
