<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Tests;

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\EnqueueCommand;
use Pozys\SpaceBattle\CommandQueueHandler;
use Pozys\SpaceBattle\Application\Scopes\InitCommand;
use Pozys\SpaceBattle\CommandQueueHandlers\DefaultHandler;
use Pozys\SpaceBattle\Container;
use Pozys\SpaceBattle\Interfaces\{CommandInterface, CommandQueueHandlerInterface};
use SplQueue;

final class EventLoopTest extends TestCase
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

    private function addToQueue(CommandInterface $command): void
    {
        $enqueueCommand = new EnqueueCommand($command);

        $enqueueCommand->execute();
    }
}
