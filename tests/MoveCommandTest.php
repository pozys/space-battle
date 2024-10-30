<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Exceptions\{GetLocationException, GetVelocityException, MoveObjectException};
use Pozys\SpaceBattle\Interfaces\MovingInterface;
use Pozys\SpaceBattle\MoveCommand;
use Pozys\SpaceBattle\ValueObjects\Vector;

final class MoveCommandTest extends TestCase
{
    public function testMove(): void
    {
        $movingObject = $this->createConfiguredMock(
            MovingInterface::class,
            ['getLocation' => new Vector(12, 5), 'getVelocity' =>  new Vector(-7, 3)]
        );

        $movingObject->expects($this->once())->method('setLocation')->with(new Vector(5, 8));

        $moveCommand = new MoveCommand($movingObject);
        $moveCommand->execute();
    }

    public function testCannotGetLocation(): void
    {
        $movingObject = $this->createMock(MovingInterface::class);
        $movingObject->method('getLocation')->willThrowException(new GetLocationException());

        $this->expectException(GetLocationException::class);

        $moveCommand = new MoveCommand($movingObject);
        $moveCommand->execute();
    }

    public function testCannotGetVelocity(): void
    {
        $movingObject = $this->createMock(MovingInterface::class);
        $movingObject->method('getVelocity')->willThrowException(new GetVelocityException());

        $this->expectException(GetVelocityException::class);

        $moveCommand = new MoveCommand($movingObject);
        $moveCommand->execute();
    }

    public function testCannotMove(): void
    {
        $movingObject = $this->createMock(MovingInterface::class);
        $movingObject->method('setLocation')->willThrowException(new MoveObjectException());

        $this->expectException(MoveObjectException::class);

        $moveCommand = new MoveCommand($movingObject);
        $moveCommand->execute();
    }
}
