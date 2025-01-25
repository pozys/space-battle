<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Tests;

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\EnqueueCommand;
use Pozys\SpaceBattle\CommandQueueHandler;
use Pozys\SpaceBattle\HardStopCommand;
use Pozys\SpaceBattle\Interfaces\CommandInterface;
use Pozys\SpaceBattle\SoftStopCommand;

final class EventLoopTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CommandQueueHandler::reset();
    }

    public function testStartEventLoop(): void
    {
        $wasCalled = false;
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock->expects($this->any())
            ->method('execute')
            ->willReturnCallback(function () use (&$wasCalled) {
                $wasCalled = true;
            });

        $this->addToQueue($commandMock);

        $this->assertFalse($wasCalled);

        CommandQueueHandler::handle();

        $this->assertTrue($wasCalled);
    }

    public function testHardStop(): void
    {
        $called = $this->createMock(CommandInterface::class);
        $called->expects($this->once())->method('execute');
        $this->addToQueue($called);

        $this->addToQueue(new HardStopCommand());

        $neverCalled = $this->createMock(CommandInterface::class);
        $neverCalled->expects($this->never())->method('execute');
        $this->addToQueue($neverCalled);

        CommandQueueHandler::handle(fn() => true);
    }

    public function testSoftStop(): void
    {
        $called = $this->createMock(CommandInterface::class);
        $called->expects($this->once())->method('execute');
        $this->addToQueue($called);

        $this->addToQueue(new SoftStopCommand());

        $calledAfterStopCommand = $this->createMock(CommandInterface::class);
        $calledAfterStopCommand->expects($this->once())->method('execute');
        $this->addToQueue($calledAfterStopCommand);

        CommandQueueHandler::handle(fn() => true);
    }

    private function addToQueue(CommandInterface $command): void
    {
        $enqueueCommand = new EnqueueCommand($command);

        $enqueueCommand->execute();
    }
}
