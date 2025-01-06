<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Tests;

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Application\Auth\JWTParser;
use Pozys\SpaceBattle\Application\AuthMiddleware;
use Pozys\SpaceBattle\AuthServer\JWTProvider;
use Pozys\SpaceBattle\Bootstrap;
use Symfony\Component\HttpFoundation\{Request, Response};

final class AuthMiddlewareTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        (new Bootstrap())();
    }

    public function testActionIsAuthorized(): void
    {
        $token = (new JWTProvider())->issueToken(['space_battle_id' => 1]);

        $request =  Request::create(
            '/?spaceBattleId=1',
            'POST',
            ['spaceBattleId' => 1],
            server: ['HTTP_AUTHORIZATION' => 'Bearer ' . $token->toString()]
        );

        $middleware = new AuthMiddleware(new JWTParser());

        $okResponse = new Response(status: Response::HTTP_OK);
        $response = $middleware($request, static fn() => $okResponse);

        $this->assertEquals($okResponse->getStatusCode(), $response->getStatusCode());
    }

    public function testActionIsNotAuthorized(): void
    {
        $token = (new JWTProvider())->issueToken(['space_battle_id' => 1]);

        $request =  Request::create(
            '/?spaceBattleId=2',
            'POST',
            ['spaceBattleId' => 1],
            server: ['HTTP_AUTHORIZATION' => 'Bearer ' . $token->toString()]
        );

        $middleware = new AuthMiddleware(new JWTParser());

        $okResponse = new Response(status: Response::HTTP_OK);
        $response = $middleware($request, static fn() => $okResponse);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testActionIsNotAuthorizedWithoutToken(): void
    {
        $request =  Request::create(
            '/?spaceBattleId=1',
            'POST',
            ['spaceBattleId' => 1]
        );

        $middleware = new AuthMiddleware(new JWTParser());

        $okResponse = new Response(status: Response::HTTP_OK);
        $response = $middleware($request, static fn() => $okResponse);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testWrongToken(): void
    {
        $request =  Request::create(
            '/?spaceBattleId=1',
            'POST',
            ['spaceBattleId' => 1],
            server: ['HTTP_AUTHORIZATION' => 'Bearer wrong']
        );

        $middleware = new AuthMiddleware(new JWTParser());

        $okResponse = new Response(status: Response::HTTP_OK);
        $response = $middleware($request, static fn() => $okResponse);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
