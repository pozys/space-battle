<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Interfaces;

use Pozys\SpaceBattle\ValueObjects\Angle;

interface RotatingInterface
{
    public function getAngle(): Angle;

    public function getAngularVelocity(): Angle;

    public function setAngle(Angle $angle): void;
}
