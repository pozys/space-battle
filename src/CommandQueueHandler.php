<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Closure;
use Pozys\SpaceBattle\ExceptionHandlers\ExceptionHandler;
use Pozys\SpaceBattle\Interfaces\CommandInterface;
use SplDoublyLinkedList;
use SplQueue;

class CommandQueueHandler
{
    private static SplQueue $queue;
    private static $runningCondition;

    public static function handle(?Closure $runningCondition = null): void
    {
        global $isRunning;
        $isRunning = $runningCondition ?? self::$runningCondition;

        while ($isRunning(self::$queue)) {
            $command = self::$queue->dequeue();
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
        self::$runningCondition = fn() => !self::$queue->isEmpty();
    }
}
