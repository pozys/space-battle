<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Interfaces\{CommandInterface, FuelConsumingInterface};

class BurnFuelCommand implements CommandInterface
{
    public function __construct(private readonly FuelConsumingInterface $fuelConsumingObject) {}

    public function execute(): void
    {
        $fuelConsumptionRate = $this->fuelConsumingObject->getFuelConsumptionRate();
        $newLevel = $this->fuelConsumingObject->getFuelLevel()->spendFuel($fuelConsumptionRate);
        $this->fuelConsumingObject->setFuelLevel($newLevel);
    }
}
