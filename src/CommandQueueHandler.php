<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\ExceptionHandlers\ExceptionHandler;
use Pozys\SpaceBattle\Interfaces\CommandInterface;
use SplDoublyLinkedList;
use SplQueue;

class CommandQueueHandler
{
    private static SplQueue $queue;

    public static function handle(): void
    {
        foreach (self::$queue as $command) {
            try {
                $command->execute();
            } catch (\Throwable $th) {
                ExceptionHandler::handle($command, $th)->execute();
            }
        }
    }

    public static function append(CommandInterface $command): void
    {
        self::$queue->enqueue($command);
    }

    public static function reset(): void
    {
        self::$queue = new SplQueue();
        self::$queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
    }
}
