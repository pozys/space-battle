<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\LogExceptionCommand;
use Pozys\SpaceBattle\Interfaces\CommandInterface;
use Psr\Log\LoggerInterface;

final class LogExceptionCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $commandStub = $this->createStub(CommandInterface::class);
        $exceptionStub = $this->createStub(Exception::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $logExceptionCommand = new LogExceptionCommand($commandStub, $exceptionStub, $loggerMock);

        $loggerMock->expects($this->once())->method('error');

        $logExceptionCommand->execute();
    }
}
