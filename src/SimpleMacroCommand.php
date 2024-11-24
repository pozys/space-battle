<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Exceptions\CommandException;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class SimpleMacroCommand implements CommandInterface
{
    private readonly array $commands;

    public function __construct(CommandInterface ...$commands)
    {
        $this->commands = $commands;
    }

    public function execute(): void
    {
        foreach ($this->commands as $command) {
            try {
                $command->execute();
            } catch (\Throwable $th) {
                throw new CommandException(previous: $th);
            }
        }
    }
}
