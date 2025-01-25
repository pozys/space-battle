<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Interfaces\CommandInterface;

class HardStopCommand implements CommandInterface
{
    public function execute(): void
    {
        global $isRunning;
        $isRunning = fn() => false;
    }
}
