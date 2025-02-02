<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\CommandQueueHandlers;

use Closure;
use Pozys\SpaceBattle\Container;
use Pozys\SpaceBattle\Interfaces\{CommandInterface, CommandQueueHandlerInterface};

class HandlerManager
{
    private static array $serviceCommands = [];
    private static array $serviceCommandsHandlers = [];
    private static array $defaultCommandsHandlers = [];

    public static function registerServiceCommand(CommandInterface $command): void
    {
        self::$serviceCommands[get_class($command)] = $command;

        foreach (self::$serviceCommandsHandlers as $queueHandler => $handler) {
            Container::resolve('IoC.Register', self::resolveServiceCommandHandlerDependencyName($queueHandler, $command), $handler);
        }
    }

    public static function registerServiceCommandHandler(string $queueHandler, Closure $handler): void
    {
        self::$serviceCommandsHandlers[$queueHandler] = $handler;

        foreach (self::$serviceCommands as $command) {
            Container::resolve('IoC.Register', self::resolveServiceCommandHandlerDependencyName($queueHandler, $command), fn() => $handler);
        }
    }

    public static function registerDefaultCommandHandler(string $queueHandler, Closure $handler): void
    {
        self::$defaultCommandsHandlers[$queueHandler] = $handler;

        Container::resolve('IoC.Register', self::resolveDefaultCommandHandlerDependencyName($queueHandler), fn() => $handler);
    }

    public static function resolveHandler(CommandQueueHandlerInterface $queueHandler, CommandInterface $command, ...$arguments): void
    {
        $handler = self::resolveServiceCommandHandler($queueHandler, $command, ...$arguments)
            ?? self::resolveDefaultCommandHandler($queueHandler, $command, ...$arguments)
            ?? Container::resolve('CommandHandler.default', $command, $queueHandler, ...$arguments);


        $handler($command, ...$arguments);
    }

    private static function resolveServiceCommandHandler(CommandQueueHandlerInterface $queueHandler, CommandInterface $command, ...$arguments): ?Closure
    {
        if (isset(self::$serviceCommandsHandlers[get_class($queueHandler)]) && isset(self::$serviceCommands[get_class($command)])) {
            return Container::resolve(self::resolveServiceCommandHandlerDependencyName(get_class($queueHandler), $command), $command, $queueHandler, ...$arguments);
        }

        return null;
    }

    private static function resolveDefaultCommandHandler(CommandQueueHandlerInterface $queueHandler, CommandInterface $command, ...$arguments): ?Closure
    {
        if (array_key_exists(get_class($command), self::$serviceCommands)) {
            return null;
        }

        if (array_key_exists(get_class($queueHandler), self::$defaultCommandsHandlers)) {
            return Container::resolve(self::resolveDefaultCommandHandlerDependencyName(get_class($queueHandler)), $command, $queueHandler, ...$arguments);
        }

        return null;
    }

    private static function resolveServiceCommandHandlerDependencyName(string $queueHandler, CommandInterface $command): string
    {
        return $queueHandler . '.' . get_class($command);
    }

    private static function resolveDefaultCommandHandlerDependencyName(string $queueHandler): string
    {
        return $queueHandler . '.default';
    }
}
