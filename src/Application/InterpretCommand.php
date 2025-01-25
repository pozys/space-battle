<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application;

use Pozys\SpaceBattle\Container;
use Pozys\SpaceBattle\Interfaces\{CommandInterface, GameObjectInterface};

class InterpretCommand implements CommandInterface
{
    public function __construct(
        public readonly int $objectId,
        public readonly string $ruleId,
        public readonly array $args
    ) {}

    public function execute(): void
    {
        $object = $this->provideGameObject();
        $ruleName = $this->provideRuleName();

        $command = Container::resolve($ruleName, $object, ...$this->args);

        Container::resolve('Queue.Command.Add', $command)->execute();
    }

    private function provideGameObject(): GameObjectInterface
    {
        return Container::resolve('GameObject.Get', $this->objectId);
    }

    private function provideRuleName(): string
    {
        return Container::resolve('GameRule.Get', $this->ruleId);
    }
}
