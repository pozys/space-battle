<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Closure;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class UpdateIocResolveDependencyStrategyCommand implements CommandInterface
{
    public function __construct(private readonly Closure $updater) {}

    public function execute(): void
    {
        Container::setStrategy(($this->updater)(Container::getStrategy()));
    }
}
