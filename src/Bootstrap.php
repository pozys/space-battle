<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Dotenv\Dotenv;

class Bootstrap
{
    public function __invoke(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ .  '/..');
        $dotenv->load();
    }
}
