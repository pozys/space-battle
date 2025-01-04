<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Messages\DTOs;

class GameRuleMessage
{
    public function __construct(
        public readonly int $gameId,
        public readonly int $objectId,
        public readonly string $ruleId,
        public readonly array $args
    ) {
        if (!$this->validateUUID($ruleId)) {
            throw new \InvalidArgumentException('Rule id must be a valid UUID');
        }
    }

    public static function fromAMQPMessage(array $payload): self
    {
        return new self(
            $payload['game_id'],
            $payload['object_id'],
            $payload['rule_id'],
            $payload['args']
        );
    }

    private function validateUUID(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid) === 1;
    }
}
