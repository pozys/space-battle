<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Interfaces;

use Pozys\SpaceBattle\ValueObjects\Angle;
use Pozys\SpaceBattle\ValueObjects\Vector;

interface ChangingVelocityInterface
{
    public function setVelocity(Vector $velocity): void;

    public function getVelocity(): Vector;

    public function getAngle(): Angle;
}
