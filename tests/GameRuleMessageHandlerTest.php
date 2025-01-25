<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Tests;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\Messages\GameRuleMessageHandler;
use Pozys\SpaceBattle\Application\Scopes\InitCommand;
use Pozys\SpaceBattle\Container;
use Pozys\SpaceBattle\Interfaces\{CommandInterface, GameObjectInterface};
use Pozys\SpaceBattle\SimpleMacroCommand;

final class GameRuleMessageHandlerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        (new InitCommand())->execute();
    }

    public function testSuccessHandler(): void
    {
        $gameId = 1;
        $objectId = 1;
        $ruleId = 'c6f6e199-6c9c-4f93-87a6-1fb12cee9591';
        $args = ['arg1', 'arg2'];

        $queue = ['1', '2'];

        Container::resolve('IoC.Register', 'GameQueue.Get', function (int $id) use ($queue, $gameId) {
            $queues = [
                $gameId => $queue,
                1000 => []
            ];

            return $queues[$id];
        });

        $ruleName = 'someRule';

        Container::resolve('IoC.Register', 'GameRule.Get', function (string $uuid) use ($ruleId, $ruleName): string {
            $rules = [
                $ruleId => $ruleName,
                'someOtherUuid' => 'someOtherRule'
            ];

            return $rules[$uuid];
        });

        $gameObject = $this->createMock(GameObjectInterface::class);
        $someOtherObject = $this->createMock(GameObjectInterface::class);

        Container::resolve(
            'IoC.Register',
            'GameObject.Get',
            function (int $id) use ($objectId, $gameObject, $someOtherObject): GameObjectInterface {
                $objects = [
                    $objectId => $gameObject,
                    'someOtherUuid' => $someOtherObject
                ];

                return $objects[$id];
            }
        );

        $macroCommand = $this->createMock(SimpleMacroCommand::class);

        $expectedGameObject = null;
        $expectedArgs = [];

        Container::resolve(
            'IoC.Register',
            $ruleName,
            function (
                GameObjectInterface $gameObject,
                ...$args
            ) use (
                $macroCommand,
                &$expectedGameObject,
                &$expectedArgs
            ) {
                $expectedGameObject = $gameObject;
                $expectedArgs = $args;

                return $macroCommand;
            }
        );

        $addToQueueCommand = $this->createMock(CommandInterface::class);
        $addToQueueCommand->expects($this->once())->method('execute');

        $expectedCommand = null;

        Container::resolve(
            'IoC.Register',
            'Queue.Command.Add',
            function (CommandInterface $command) use ($addToQueueCommand, &$expectedCommand) {
                $expectedCommand = $command;

                return $addToQueueCommand;
            }
        );

        $payload = [
            'game_id' => $gameId,
            'object_id' => $objectId,
            'rule_id' => $ruleId,
            'args' => $args
        ];

        $message = new AMQPMessage(json_encode($payload));

        $handler = new GameRuleMessageHandler();
        $handler->handle($message);

        $actualGameQueue = Container::resolve('GameQueue.Get');

        $this->assertSame($queue, $actualGameQueue);
        $this->assertSame($gameObject, $expectedGameObject);
        $this->assertSame($args, $expectedArgs);
        $this->assertSame($macroCommand, $expectedCommand);
    }
}
