<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Scopes;

use Closure;
use Pozys\SpaceBattle\Container;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class RegisterDependencyCommand implements CommandInterface
{
    public function __construct(private readonly string $dependency, private readonly Closure $dependecyResolverStrategy) {}

    public function execute(): void
    {
        $currentScope = Container::resolve('IoC.Scope.Current');
        $currentScope->add($this->dependency, $this->dependecyResolverStrategy);

        Container::resolve('IoC.Scope.Current.Set', $currentScope);
    }
}
