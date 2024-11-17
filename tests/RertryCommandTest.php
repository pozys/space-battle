<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\RetryCommand;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

final class RertryCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $commandMock = $this->createMock(CommandInterface::class);

        $rertryCommand = new RetryCommand($commandMock);

        $commandMock->expects($this->once())->method('execute');

        $rertryCommand->execute();
    }
}
