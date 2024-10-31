<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Exceptions\{GetAngleException, GetAngularVelocityException, RotateObjectException};
use Pozys\SpaceBattle\Interfaces\RotatingInterface;
use Pozys\SpaceBattle\RotateCommand;
use Pozys\SpaceBattle\ValueObjects\Angle;

final class RotateCommandTest extends TestCase
{
    public function testRotate(): void
    {
        $rotatingObject = $this->createConfiguredMock(
            RotatingInterface::class,
            ['getAngle' => new Angle(12), 'getAngularVelocity' =>  new Angle(-7)]
        );

        $rotatingObject->expects($this->once())->method('setAngle')->with(new Angle(5));

        $rotateCommand = new RotateCommand($rotatingObject);
        $rotateCommand->execute();
    }

    public function testCannotGetAngle(): void
    {
        $rotatingObject = $this->createMock(RotatingInterface::class);
        $rotatingObject->method('getAngle')->willThrowException(new GetAngleException());

        $this->expectException(GetAngleException::class);

        $moveCommand = new RotateCommand($rotatingObject);
        $moveCommand->execute();
    }

    public function testCannotGetAngularVelocity(): void
    {
        $rotatingObject = $this->createMock(RotatingInterface::class);
        $rotatingObject->method('getAngularVelocity')->willThrowException(new GetAngularVelocityException());

        $this->expectException(GetAngularVelocityException::class);

        $moveCommand = new RotateCommand($rotatingObject);
        $moveCommand->execute();
    }

    public function testCannotRotate(): void
    {
        $rotatingObject = $this->createMock(RotatingInterface::class);
        $rotatingObject->method('setAngle')->willThrowException(new RotateObjectException());

        $this->expectException(RotateObjectException::class);

        $moveCommand = new RotateCommand($rotatingObject);
        $moveCommand->execute();
    }
}
