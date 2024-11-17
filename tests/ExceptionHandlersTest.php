<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\{EnqueueCommand, LogExceptionCommand, RetryCommand, SafeExecutionCommand, RetryTwiceCommand};
use Pozys\SpaceBattle\CommandQueueHandler;
use Pozys\SpaceBattle\ExceptionHandlers\ExceptionHandler;
use Pozys\SpaceBattle\Exceptions\GetLocationException;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

final class ExceptionHandlersTest extends TestCase
{
    public function testEnqueueLogExceptionCommand(): void
    {
        $exceptionClass = GetLocationException::class;

        $command = $this->createMock(CommandInterface::class);
        $command->method('execute')->willThrowException(new $exceptionClass());

        $exceptionStub = $this->createStub(Exception::class);

        $logExceptionCommandMock = $this->getMockBuilder(LogExceptionCommand::class)
            ->setConstructorArgs([$command, $exceptionStub])
            ->getMock();

        $enqueueCommandMock = $this->getMockBuilder(EnqueueCommand::class)
            ->setConstructorArgs([$logExceptionCommandMock])
            ->getMock();
        $enqueueCommandMock->expects($this->once())->method('execute');

        ExceptionHandler::registerHandler(
            get_class($command),
            $exceptionClass,
            fn() => $enqueueCommandMock
        );

        CommandQueueHandler::reset();
        CommandQueueHandler::append($command);
        CommandQueueHandler::handle();
    }

    public function testEnqueueRetryCommand(): void
    {
        $exceptionClass = GetLocationException::class;

        $command = $this->createMock(CommandInterface::class);
        $command->method('execute')->willThrowException(new $exceptionClass());

        $retryCommandMock = $this->getMockBuilder(RetryCommand::class)
            ->setConstructorArgs([$command])
            ->getMock();

        $enqueueCommandMock = $this->getMockBuilder(EnqueueCommand::class)
            ->setConstructorArgs([$retryCommandMock])
            ->getMock();
        $enqueueCommandMock->expects($this->once())->method('execute');

        ExceptionHandler::registerHandler(
            get_class($command),
            $exceptionClass,
            fn() => $enqueueCommandMock
        );

        CommandQueueHandler::reset();
        CommandQueueHandler::append($command);
        CommandQueueHandler::handle();
    }

    public function testRetryLogExceptionCommand(): void
    {
        $exceptionClass = GetLocationException::class;

        $command = $this->createMock(CommandInterface::class);
        $command->expects($this->exactly(2))->method('execute')->willThrowException(new $exceptionClass());

        ExceptionHandler::registerHandler(
            get_class($command),
            $exceptionClass,
            fn(CommandInterface $cmd) => new SafeExecutionCommand(new RetryCommand($cmd))
        );

        $logExceptionCommand = $this->createMock(LogExceptionCommand::class);
        $logExceptionCommand->expects($this->once())->method('execute');

        ExceptionHandler::registerHandler(
            RetryCommand::class,
            $exceptionClass,
            fn() => $logExceptionCommand
        );

        CommandQueueHandler::reset();
        CommandQueueHandler::append($command);
        CommandQueueHandler::handle();
    }

    public function testRetryTwiceLogExceptionCommand(): void
    {
        $exceptionClass = GetLocationException::class;
        $command = $this->createMock(CommandInterface::class);
        $command->expects($this->exactly(3))->method('execute')->willThrowException(new $exceptionClass());

        ExceptionHandler::registerHandler(
            get_class($command),
            $exceptionClass,
            fn(CommandInterface $cmd) => new SafeExecutionCommand(new RetryTwiceCommand($cmd))
        );

        $logExceptionCommand = $this->createMock(LogExceptionCommand::class);
        $logExceptionCommand->expects($this->once())->method('execute');

        ExceptionHandler::registerHandler(
            RetryTwiceCommand::class,
            $exceptionClass,
            fn() => $logExceptionCommand
        );

        CommandQueueHandler::reset();
        CommandQueueHandler::append($command);
        CommandQueueHandler::handle();
    }
}
