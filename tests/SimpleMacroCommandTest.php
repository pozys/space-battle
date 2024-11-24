<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\{BurnFuelCommand, ChangeVelocityCommand, CheckFuelCommand, MoveCommand, RotateCommand};
use Pozys\SpaceBattle\Exceptions\CommandException;
use Pozys\SpaceBattle\Interfaces\CommandInterface;
use Pozys\SpaceBattle\SimpleMacroCommand;

final class SimpleMacroCommandTest extends TestCase
{
    public function testSuccessExecute(): void
    {
        $command1 = $this->createMock(CommandInterface::class);
        $command2 = $this->createMock(CommandInterface::class);
        $command3 = $this->createMock(CommandInterface::class);

        $commands = [$command1, $command2, $command3];

        $macroCommand = new SimpleMacroCommand(...$commands);

        $command1->expects($this->once())->method('execute');
        $command2->expects($this->once())->method('execute');
        $command3->expects($this->once())->method('execute');

        $macroCommand->execute();
    }

    public function testExceptionExecute(): void
    {
        $command1 = $this->createMock(CommandInterface::class);
        $command2 = $this->createMock(CommandInterface::class);
        $command3 = $this->createMock(CommandInterface::class);

        $commands = [$command1, $command2, $command3];

        $macroCommand = new SimpleMacroCommand(...$commands);

        $command1->expects($this->once())->method('execute');
        $command2->expects($this->once())->method('execute')->willThrowException(new CommandException());
        $command3->expects($this->never())->method('execute');

        $this->expectException(CommandException::class);

        $macroCommand->execute();
    }

    public function testMoveMacroCommand(): void
    {
        $checkFuelCommand = $this->createMock(CheckFuelCommand::class);
        $checkFuelCommand->expects($this->once())->method('execute');

        $moveCommand = $this->createMock(MoveCommand::class);
        $moveCommand->expects($this->once())->method('execute');

        $burnFuelCommand = $this->createMock(BurnFuelCommand::class);
        $burnFuelCommand->expects($this->once())->method('execute');

        $commands = [$checkFuelCommand, $moveCommand, $burnFuelCommand];

        $macroCommand = new SimpleMacroCommand(...$commands);
        $macroCommand->execute();
    }

    public function testFuelEmptyMoveMacroCommand(): void
    {
        $checkFuelCommand = $this->createMock(CheckFuelCommand::class);
        $checkFuelCommand->expects($this->once())->method('execute')->willThrowException(new CommandException());

        $moveCommand = $this->createMock(MoveCommand::class);
        $moveCommand->expects($this->never())->method('execute');

        $burnFuelCommand = $this->createMock(BurnFuelCommand::class);
        $burnFuelCommand->expects($this->never())->method('execute');

        $commands = [$checkFuelCommand, $moveCommand, $burnFuelCommand];

        $macroCommand = new SimpleMacroCommand(...$commands);

        $this->expectException(CommandException::class);

        $macroCommand->execute();
    }

    public function testRotateAndChangeVelocityMacroCommand(): void
    {
        $checkFuelCommand = $this->createMock(RotateCommand::class);
        $checkFuelCommand->expects($this->once())->method('execute');

        $moveCommand = $this->createMock(ChangeVelocityCommand::class);
        $moveCommand->expects($this->once())->method('execute');

        $commands = [$checkFuelCommand, $moveCommand];

        $macroCommand = new SimpleMacroCommand(...$commands);
        $macroCommand->execute();
    }
}
