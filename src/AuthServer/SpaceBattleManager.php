<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\AuthServer;

use Lcobucci\JWT\Token\Plain;
use Pozys\SpaceBattle\AuthServer\Entities\User;
use Pozys\SpaceBattle\Interfaces\SpaceBattleRepositoryInterface;

class SpaceBattleManager
{
    public function __construct(
        private readonly SpaceBattleRepositoryInterface $repository,
        private readonly JWTProvider $jwt
    ) {}

    public function registerBattle(int ...$players): int
    {
        return $this->repository->createSpaceBattle(...$players);
    }

    public function authenticate(User $player, int $spaceBattleId): Plain
    {
        $players = $this->repository->getSpaceBattlePlayers($spaceBattleId);

        if (!in_array($player->getId(), $players, true)) {
            throw new \RuntimeException('Player is not in battle');
        }

        return $this->jwt->issueToken(['space_battle_id' => $spaceBattleId]);
    }
}
