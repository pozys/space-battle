<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application;

use Pozys\SpaceBattle\CommandQueueHandler;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class EnqueueCommand implements CommandInterface
{
    public function __construct(private readonly CommandInterface $command) {}

    public function execute(): void
    {
        CommandQueueHandler::append($this->command);
    }
}
