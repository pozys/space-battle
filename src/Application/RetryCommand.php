<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application;

use Pozys\SpaceBattle\Interfaces\CommandInterface;

class RetryCommand implements CommandInterface
{
    public function __construct(private readonly CommandInterface $command) {}

    public function execute(): void
    {
        $this->command->execute();
    }
}
