<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Tests;

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\AuthServer\{JWTProvider, SpaceBattleManager};
use Pozys\SpaceBattle\AuthServer\Entities\User;
use Pozys\SpaceBattle\Bootstrap;
use Pozys\SpaceBattle\Interfaces\SpaceBattleRepositoryInterface;

final class SpaceBattleManagerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        (new Bootstrap())();
    }

    public function testRegisterBattle(): void
    {
        $repository = $this->createMock(SpaceBattleRepositoryInterface::class);
        $repository->method('createSpaceBattle')
            ->with(1, 2)
            ->willReturn(1);

        $manager = new SpaceBattleManager($repository, new JWTProvider());

        $this->assertSame(1, $manager->registerBattle(1, 2));
    }

    public function testAuthenticate(): void
    {
        $repository = $this->createMock(SpaceBattleRepositoryInterface::class);
        $repository->method('getSpaceBattlePlayers')->willReturn([1, 2]);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        $manager = new SpaceBattleManager($repository, new JWTProvider());

        $this->assertSame(1, $manager->authenticate($user, 1)->claims()->get('space_battle_id'));
    }

    public function testAuthenticateFails(): void
    {
        $repository = $this->createMock(SpaceBattleRepositoryInterface::class);
        $repository->method('getSpaceBattlePlayers')->willReturn([1, 2]);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(3);

        $manager = new SpaceBattleManager($repository, new JWTProvider());

        $this->expectException(\RuntimeException::class);
        $manager->authenticate($user, 1);
    }
}
