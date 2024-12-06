<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Interfaces;

interface DependencyResolverInterface
{
    public function resolve(string $dependency, ...$args): mixed;
}
