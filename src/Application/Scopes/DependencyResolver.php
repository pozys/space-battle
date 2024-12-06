<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Scopes;

use Pozys\SpaceBattle\Interfaces\DependencyResolverInterface;

class DependencyResolver implements DependencyResolverInterface
{
    public function __construct(private array $scope) {}

    public function resolve(string $dependency, ...$args): mixed
    {
        while (true) {
            if (array_key_exists($dependency, $this->scope)) {
                return $this->scope[$dependency](...$args);
            }

            $this->scope = $this->scope['IoC.Scope.Parent']();
        }
    }
}
