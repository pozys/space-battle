<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\ValueObjects;

class Angle
{
    private const DIRECTIONS_NUMBER = 256;

    public function __construct(public readonly int $direction) {}

    public function plus(Angle $angle): self
    {
        return new self(($this->direction + $angle->direction) % self::DIRECTIONS_NUMBER, self::DIRECTIONS_NUMBER);
    }
}
