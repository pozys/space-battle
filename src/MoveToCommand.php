<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\CommandQueueHandlers\MoveToHandler;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class MoveToCommand implements CommandInterface
{
    public function execute(): void
    {
        global $nextHandler;
        $nextHandler = Container::resolve(MoveToHandler::class);;
    }
}
