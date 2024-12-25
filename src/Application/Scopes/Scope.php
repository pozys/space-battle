<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Scopes;

use Closure;

class Scope
{
    private array $dependencies = [];

    public function add(string $dependency, Closure $strategy): void
    {
        $this->dependencies[$dependency] = $strategy;
    }

    public function getValue(string $dependency): ?Closure
    {
        if (!array_key_exists($dependency, $this->dependencies)) {
            return null;
        }

        return $this->dependencies[$dependency];
    }
}
