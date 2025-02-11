<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\EnqueueCommand;
use Pozys\SpaceBattle\Application\Scopes\InitCommand;
use Pozys\SpaceBattle\CommandQueueHandler;
use Pozys\SpaceBattle\CommandQueueHandlers\DefaultHandler;
use Pozys\SpaceBattle\Container;
use Pozys\SpaceBattle\Interfaces\{CommandInterface, CommandQueueHandlerInterface};

final class EnqueueCommandTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        (new InitCommand())->execute();
        Container::resolve('IoC.Register', DefaultHandler::class, fn(): ?CommandQueueHandlerInterface => new class implements CommandQueueHandlerInterface {
            public function handle(SplQueue $queue): ?CommandQueueHandlerInterface
            {
                (new DefaultHandler())->handle($queue);

                return null;
            }
        });
    }

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
