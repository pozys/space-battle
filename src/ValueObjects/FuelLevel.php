<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\ValueObjects;

class FuelLevel
{
    public function __construct(public readonly int $amount) {}

    public function spendFuel(FuelLevel $fuelConsumptionRate): FuelLevel
    {
        return new self($this->amount - $fuelConsumptionRate->amount);
    }

    public function isEmpty(): bool
    {
        return $this->amount <= 0;
    }
}
