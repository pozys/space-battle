<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\CommandQueueHandlers\DefaultHandler;
use Pozys\SpaceBattle\Interfaces\{CommandInterface, CommandQueueHandlerInterface};
use SplDoublyLinkedList;
use SplQueue;

class CommandQueueHandler
{
    private static SplQueue $queue;
    private static ?CommandQueueHandlerInterface $handler;

    public static function handle(): void
    {
        while (self::$handler !== null) {
            self::$handler = self::$handler->handle(self::$queue);
        }
    }

    public static function append(CommandInterface $command): void
    {
        self::$queue->enqueue($command);
    }

    public static function reset(): void
    {
        Container::resolve('IoC.Register', 'CommandHandler.default', fn(CommandInterface $command) => fn() => $command->execute());

        self::$queue = new SplQueue();
        self::$queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
        self::$handler = Container::resolve(DefaultHandler::class);
    }
}
