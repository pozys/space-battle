<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Scopes;

use Pozys\SpaceBattle\Interfaces\CommandInterface;

class SetCurrentScopeCommand implements CommandInterface
{
    public function __construct(private array $scope) {}

    public function execute(): void
    {
        InitCommand::setCurrentScope($this->scope);
    }
}
