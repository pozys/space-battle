<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\AuthServer;

use DateTimeImmutable;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Encoding\{ChainedFormatter, JoseEncoder};
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;

class JWTProvider
{
    public function issueToken(array $claims): Token
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $signingKey = InMemory::plainText($_ENV['JWT_SECRET']);

        $now = new DateTimeImmutable();
        $builder = $tokenBuilder
            ->issuedAt($now)
            ->expiresAt($now->modify($_ENV['JWT_TOKEN_LIFETIME'] . ' seconds'));

        return $this->applyClaims($builder, $claims)->getToken($algorithm, $signingKey);
    }

    private function applyClaims(Builder $tokenBuilder, array $claims): Builder
    {
        foreach ($claims as $key => $value) {
            $tokenBuilder->withClaim($key, $value);
        }

        return $tokenBuilder;
    }
}
