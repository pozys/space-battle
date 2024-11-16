<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application;

use Pozys\SpaceBattle\ExceptionHandlers\ExceptionHandler;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class SafeExecutionCommand implements CommandInterface
{
    public function __construct(private readonly CommandInterface $command) {}

    public function execute(): void
    {
        try {
            $this->command->execute();
        } catch (\Throwable $th) {
            ExceptionHandler::handle($this->command, $th)->execute();
        }
    }
}
