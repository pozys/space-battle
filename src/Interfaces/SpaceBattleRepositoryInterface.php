<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Interfaces;

interface SpaceBattleRepositoryInterface
{
    public function createSpaceBattle(int ...$players): int;

    public function getSpaceBattlePlayers(int $spaceBattleId): array;
}
