<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\CommandQueueHandlers\DefaultHandler;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class RunCommand implements CommandInterface
{
    public function execute(): void
    {
        global $nextHandler;
        $nextHandler = Container::resolve(DefaultHandler::class);
    }
}
