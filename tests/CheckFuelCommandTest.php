<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\CheckFuelCommand;
use Pozys\SpaceBattle\Exceptions\CommandException;
use Pozys\SpaceBattle\Interfaces\FuelConsumingInterface;
use Pozys\SpaceBattle\ValueObjects\FuelLevel;

final class CheckFuelCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $fuelConsumingObject = $this->createConfiguredMock(
            FuelConsumingInterface::class,
            ['getFuelLevel' => new FuelLevel(1)]
        );

        $fuelConsumingObject->expects($this->atLeastOnce())->method('getFuelLevel');

        $checkFuelCommand = new CheckFuelCommand($fuelConsumingObject);

        $checkFuelCommand->execute();
    }

    #[DataProvider('TestDataProvider')]
    public function testExecuteThrowException(int $fuelLevel): void
    {
        $fuelConsumingObject = $this->createConfiguredMock(
            FuelConsumingInterface::class,
            ['getFuelLevel' => new FuelLevel($fuelLevel)]
        );

        $checkFuelCommand = new CheckFuelCommand($fuelConsumingObject);
        $this->expectException(CommandException::class);

        $checkFuelCommand->execute();
    }

    public static function TestDataProvider(): array
    {
        return [
            [0],
            [-1],
        ];
    }
}
