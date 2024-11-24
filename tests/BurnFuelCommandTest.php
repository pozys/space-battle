<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\BurnFuelCommand;
use Pozys\SpaceBattle\Interfaces\FuelConsumingInterface;
use Pozys\SpaceBattle\ValueObjects\FuelLevel;

final class BurnFuelCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $fuelConsumingObject = $this->createConfiguredMock(
            FuelConsumingInterface::class,
            ['getFuelLevel' => new FuelLevel(10), 'getFuelConsumptionRate' => new FuelLevel(2)]
        );
        $fuelConsumingObject->expects($this->atLeastOnce())
            ->method('setFuelLevel')
            ->with($this->equalTo(new FuelLevel(8)));

        $checkFuelCommand = new BurnFuelCommand($fuelConsumingObject);

        $checkFuelCommand->execute();
    }
}
