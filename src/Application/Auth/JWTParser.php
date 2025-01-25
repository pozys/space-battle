<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application\Auth;

use InvalidArgumentException;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\{Parser, Plain};
use Throwable;

class JWTParser
{
    public function parseClaims(string $token): array
    {
        $parser = new Parser(new JoseEncoder());

        try {
            $parsedToken = $parser->parse($token);
        } catch (Throwable $e) {
            throw new InvalidArgumentException('Invalid token', 0, $e);
        }

        assert($parsedToken instanceof Plain);

        return $parsedToken->claims()->all();
    }
}
