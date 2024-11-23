<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Interfaces\ChangingVelocityInterface;
use Pozys\SpaceBattle\ChangeVelocityCommand;
use Pozys\SpaceBattle\ValueObjects\{Angle, Vector};

final class ChangeVelocityCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $changingVelocityObject = $this->createMock(ChangingVelocityInterface::class);

        $changingVelocityObject->method('getAngle')
            ->willReturn(new Angle(63));
        $changingVelocityObject->method('getVelocity')
            ->willReturn(new Vector(3, 4));
        $changingVelocityObject->expects($this->once())->method('setVelocity')
            ->with($this->equalTo(new Vector(5 * cos(45), 5 * sin(45))));

        $сhangeVelocityCommand = new ChangeVelocityCommand($changingVelocityObject);

        $сhangeVelocityCommand->execute();
    }

    public function testImmobileObject(): void
    {
        $changingVelocityObject = $this->createMock(ChangingVelocityInterface::class);

        $changingVelocityObject->method('getAngle')
            ->willReturn(new Angle(63));
        $changingVelocityObject->method('getVelocity')
            ->willReturn(new Vector(0, 0));
        $changingVelocityObject->expects($this->once())->method('setVelocity')
            ->with($this->equalTo(new Vector(0, 0)));

        $сhangeVelocityCommand = new ChangeVelocityCommand($changingVelocityObject);

        $сhangeVelocityCommand->execute();
    }
}
