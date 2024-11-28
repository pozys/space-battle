<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Interfaces\CommandInterface;

class RegisterDependencyCommand implements CommandInterface
{
    public function __construct(private readonly string $key, private readonly callable $strategy) {}

    public function execute(): void
    {
        $currentScope = Container::resolve('IoC.Scope.Current');
        $currentScope->add($this->key, $this->strategy);
    }
}
