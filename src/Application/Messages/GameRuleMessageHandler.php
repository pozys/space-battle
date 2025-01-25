<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Messages;

use PhpAmqpLib\Message\AMQPMessage;
use Pozys\SpaceBattle\Application\InterpretCommand;
use Pozys\SpaceBattle\Application\Messages\DTOs\GameRuleMessage;
use Pozys\SpaceBattle\Container;

class GameRuleMessageHandler
{
    public function handle(AMQPMessage $message): void
    {
        $payload = json_decode($message->getBody(), true);
        $message = GameRuleMessage::fromAMQPMessage($payload);

        $this->setQueue($message->gameId);

        $interpretCommand = new InterpretCommand($message->objectId, $message->ruleId, $message->args);
        $interpretCommand->execute();
    }

    private function setQueue(int $gameId): void
    {
        $gameQueue = Container::resolve('GameQueue.Get', $gameId);

        $scope = Container::resolve('IoC.Scope.Create');
        Container::resolve('IoC.Scope.Current.Set', $scope);

        Container::resolve('IoC.Register', 'GameQueue.Get', fn() => $gameQueue);
    }
}
