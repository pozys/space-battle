<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Exceptions\{GetAngleException, GetAngularVelocityException, RotateObjectException};
use Pozys\SpaceBattle\Interfaces\RotatingInterface;

class RotateCommand
{
    public function __construct(private RotatingInterface $rotatingObject) {}

    public function execute(): void
    {
        try {
            $currentAngle = $this->rotatingObject->getAngle();
        } catch (GetAngleException $th) {
            throw $th;
        }

        try {
            $angularVelocity = $this->rotatingObject->getAngularVelocity();
        } catch (GetAngularVelocityException $th) {
            throw $th;
        }

        $newAngle = $currentAngle->plus($angularVelocity);

        try {
            $this->rotatingObject->setAngle($newAngle);
        } catch (RotateObjectException $th) {
            throw $th;
        }
    }
}
