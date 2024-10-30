<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Interfaces;

use Pozys\SpaceBattle\ValueObjects\Vector;

interface MovingInterface
{
    public function getLocation(): Vector;

    public function getVelocity(): Vector;

    public function setLocation(): void;
}
