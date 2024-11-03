<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\ExceptionHandlers\LogExceptionCommand;
use Pozys\SpaceBattle\Exceptions\GetLocationException;
use Pozys\SpaceBattle\Interfaces\MovingInterface;
use Pozys\SpaceBattle\MoveCommand;

final class ExceptionHanlderTest extends TestCase
{
    public function testLogExceptionCommand(): void
    {
        $movingObject = $this->createMock(MovingInterface::class);
        $movingObject->method('getLocation')->willThrowException(new GetLocationException());

        $logExceptionCommand = $this->createMock(LogExceptionCommand::class);
        $logExceptionCommand->expects($this->once())->method('execute');

        $this->expectException(GetLocationException::class);

        $moveCommand = new MoveCommand($movingObject);
        $moveCommand->execute();
    }
}
