<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Closure;

class Container
{
    private static ?Closure $strategy;

    public static function resolve(string $dependency, ...$params): mixed
    {
        return self::getStrategy()($dependency, ...$params);
    }

    public static function setStrategy(?callable $strategy): void
    {
        self::$strategy = $strategy;
    }

    public static function getStrategy(): callable
    {
        return self::$strategy ?? self::defaultStrategy();
    }

    private static function defaultStrategy(): callable
    {
        return function (string $dependency, ...$params): mixed {
            if ('Update Ioc Resolve Dependency Strategy' !== $dependency) {
                throw new \InvalidArgumentException("Unknown dependency '$dependency'");
            }

            return new UpdateIocResolveDependencyStrategyCommand($params[0]);
        };
    }
}
