<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Scopes;

use Pozys\SpaceBattle\Interfaces\CommandInterface;

class ClearCurrentScopeCommand implements CommandInterface
{
    public function __construct() {}

    public function execute(): void
    {
        InitCommand::setCurrentScope(null);
    }
}
