<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application;

use Closure;
use Pozys\SpaceBattle\Application\Auth\JWTParser;
use Symfony\Component\HttpFoundation\{Request, Response};

class AuthMiddleware
{
    private const AUTH_HEADER = 'Authorization';
    private const BEARER = 'Bearer ';

    public function __construct(private readonly JWTParser $parser) {}

    public function __invoke(Request $request, Closure $next): Response
    {
        $unauthorizedResponse = new Response(status: Response::HTTP_UNAUTHORIZED);

        if (!$request->headers->has(self::AUTH_HEADER)) {
            return $unauthorizedResponse;
        }
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return $unauthorizedResponse;
        }

        try {
            $claims = $this->parser->parseClaims($token);
        } catch (\Throwable $th) {
            return $unauthorizedResponse;
        }

        if (!$this->actionIsAuthorized($request, $claims)) {
            return $unauthorizedResponse;
        }

        return $next($request);
    }

    private function getTokenFromRequest(Request $request): string
    {
        return trim(
            substr($request->headers->get(self::AUTH_HEADER), strlen(self::BEARER))
        );
    }

    private function actionIsAuthorized(Request $request, array $claims): bool
    {
        $requestedBattleId = (int) $request->get('spaceBattleId');
        $allowedBattleId = (int) $claims['space_battle_id'] ?? null;

        if (!($allowedBattleId && $requestedBattleId)) {
            return false;
        }

        return $allowedBattleId === $requestedBattleId;
    }
}
