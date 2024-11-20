<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application;

use Pozys\SpaceBattle\Interfaces\CommandInterface;

class RetryTwiceCommand implements CommandInterface
{
    private readonly RetryCommand $retryCommand;

    public function __construct(private readonly CommandInterface $command)
    {
        $this->retryCommand = new RetryCommand($this->command);
    }

    public function execute(): void
    {
        try {
            $this->retryCommand->execute();
        } catch (\Throwable $th) {
            $this->command->execute();
        }
    }
}
