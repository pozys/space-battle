<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Interfaces\CommandInterface;
use SplQueue;

class SoftStopCommand implements CommandInterface
{
    public function execute(): void
    {
        global $isRunning;
        $isRunning = fn(SplQueue $queue) => !$queue->isEmpty();
    }
}
