<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

class Container
{
    private static $container = [];

    public static function resolve(string $key, ...$params): object
    {
        return self::resolve('IoC', $key, ...$params);
    }
}
