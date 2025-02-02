<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Tests;

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\EnqueueCommand;
use Pozys\SpaceBattle\Application\Scopes\InitCommand;
use Pozys\SpaceBattle\CommandQueueHandler;
use Pozys\SpaceBattle\CommandQueueHandlers\{DefaultHandler, HandlerManager, MoveToHandler};
use Pozys\SpaceBattle\{Container, HardStopCommand};
use Pozys\SpaceBattle\Interfaces\{CommandInterface, CommandQueueHandlerInterface};
use Pozys\SpaceBattle\{MoveToCommand, RunCommand, SoftStopCommand};

final class CommandQueueHandlerStatesTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        (new InitCommand())->execute();

        HandlerManager::registerServiceCommand(new HardStopCommand());
        HandlerManager::registerServiceCommand(new SoftStopCommand());
        HandlerManager::registerServiceCommand(new MoveToCommand());
        HandlerManager::registerServiceCommand(new RunCommand());
    }

    protected function setUp(): void
    {
        parent::setUp();

        Container::resolve('IoC.Register', DefaultHandler::class, fn(): ?CommandQueueHandlerInterface => new DefaultHandler());

        CommandQueueHandler::reset();
    }

    public function testHardStopCommandStopsQueue(): void
    {
        $called = $this->createMock(CommandInterface::class);
        $called->expects($this->once())->method('execute');
        $this->addToQueue($called);

        $this->addToQueue(new HardStopCommand());

        $neverCalled = $this->createMock(CommandInterface::class);
        $neverCalled->expects($this->never())->method('execute');
        $this->addToQueue($neverCalled);

        CommandQueueHandler::handle();
    }

    public function testDefaultHandlerWithSoftStopCommand(): void
    {
        $called = $this->createMock(CommandInterface::class);
        $called->expects($this->once())->method('execute');
        $this->addToQueue($called);

        $this->addToQueue(new SoftStopCommand());

        $calledAfterStopCommand = $this->createMock(CommandInterface::class);
        $calledAfterStopCommand->expects($this->once())->method('execute');
        $this->addToQueue($calledAfterStopCommand);

        CommandQueueHandler::handle();
    }

    public function testMoveToCommandSuccess(): void
    {
        $moveToHandler = $this->createMock(MoveToHandler::class);
        $moveToHandler->expects($this->atLeast(1))->method('handle');

        Container::resolve('IoC.Register', MoveToHandler::class, fn(): CommandQueueHandlerInterface => $moveToHandler);

        $this->addToQueue(new MoveToCommand());

        CommandQueueHandler::handle();
    }

    public function testMoveToCommandExecutionStrategy(): void
    {
        $called = $this->createMock(CommandInterface::class);
        $called->expects($this->once())->method('execute');
        $this->addToQueue($called);

        HandlerManager::registerDefaultCommandHandler(MoveToHandler::class, fn() => null);

        Container::resolve('IoC.Register', MoveToHandler::class, fn(): CommandQueueHandlerInterface => new MoveToHandler());

        $this->addToQueue(new MoveToCommand());

        $neverCalled = $this->createMock(CommandInterface::class);
        $neverCalled->expects($this->never())->method('execute');
        $this->addToQueue($neverCalled);

        CommandQueueHandler::handle();
    }

    public function testRunCommandSuccess(): void
    {
        $defaultHandler = $this->createMock(DefaultHandler::class);
        $defaultHandler->expects($this->atLeast(1))->method('handle')
            ->willReturnCallback(fn() => null);

        Container::resolve('IoC.Register', DefaultHandler::class, fn(): CommandQueueHandlerInterface => $defaultHandler);

        $this->addToQueue(new MoveToCommand());

        $this->addToQueue(new RunCommand());

        CommandQueueHandler::handle();
    }

    private function addToQueue(CommandInterface $command): void
    {
        (new EnqueueCommand($command))->execute();
    }
}
