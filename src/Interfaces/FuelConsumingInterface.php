<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Interfaces;

use Pozys\SpaceBattle\ValueObjects\FuelLevel;

interface FuelConsumingInterface
{
    public function getFuelLevel(): FuelLevel;

    public function setFuelLevel(FuelLevel $level): self;

    public function getFuelConsumptionRate(): FuelLevel;
}
