<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\EnqueueCommand;
use Pozys\SpaceBattle\CommandQueueHandler;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

final class EnqueueCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock->expects($this->never())->method('execute');

        $enqueueCommand = new EnqueueCommand($commandMock);

        CommandQueueHandler::reset();

        $enqueueCommand->execute();

        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock->expects($this->once())->method('execute');

        $enqueueCommand = new EnqueueCommand($commandMock);

        CommandQueueHandler::reset();

        $enqueueCommand->execute();

        CommandQueueHandler::handle();
    }
}
