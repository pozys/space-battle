<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Scopes;

use Pozys\SpaceBattle\Interfaces\DependencyResolverInterface;

class DependencyResolver implements DependencyResolverInterface
{
    public function __construct(private Scope $scope) {}

    public function resolve(string $dependency, ...$args): mixed
    {
        while (true) {
            if ($this->scope->getValue($dependency) !== null) {
                return $this->scope->getValue($dependency)(...$args);
            }

            $this->scope = $this->scope->getValue('IoC.Scope.Parent')();
        }
    }
}
