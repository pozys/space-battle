<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\ValueObjects;

class Vector
{
    public function __construct(public readonly float $x, public readonly float $y) {}

    public function plus(Vector $vector): self
    {
        return new self($this->x + $vector->x, $this->y + $vector->y);
    }
}
