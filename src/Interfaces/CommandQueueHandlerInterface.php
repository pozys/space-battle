<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Interfaces;

use SplQueue;

interface CommandQueueHandlerInterface
{
    public function handle(SplQueue $queue): ?CommandQueueHandlerInterface;
}
