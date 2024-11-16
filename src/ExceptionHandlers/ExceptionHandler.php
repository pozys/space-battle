<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\ExceptionHandlers;

use Exception;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class ExceptionHandler
{
    private static array $handlersStore = [];

    public static function handle(CommandInterface $command, Exception $exception): CommandInterface
    {
        return self::discoverHandler(get_class($command), get_class($exception))($command, $exception);
    }

    public static function registerHandler(string $commandType, string $exceptionType, callable $handler): void
    {
        self::$handlersStore[$commandType][$exceptionType] = $handler;
    }

    private static function discoverHandler(string $commandType, string $exceptionType): callable
    {
        return self::$handlersStore[$commandType][$exceptionType] ?? throw new Exception();
    }
}
