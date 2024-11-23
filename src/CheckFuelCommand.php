<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Exceptions\CommandException;
use Pozys\SpaceBattle\Interfaces\{CommandInterface, FuelConsumingInterface};

class CheckFuelCommand implements CommandInterface
{
    public function __construct(private readonly FuelConsumingInterface $fuelConsumingObject) {}

    public function execute(): void
    {
        if (!$this->hasEnoughFuel()) {
            throw new CommandException('Not enough fuel');
        }
    }

    private function hasEnoughFuel(): bool
    {
        return !$this->fuelConsumingObject->getFuelLevel()->isEmpty();
    }
}
