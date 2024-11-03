<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\ExceptionHandlers\LogExceptionCommand;
use Pozys\SpaceBattle\Exceptions\{GetLocationException, GetVelocityException, MoveObjectException};
use Pozys\SpaceBattle\Interfaces\MovingInterface;

class MoveCommand
{
    public function __construct(private MovingInterface $movingObject) {}

    public function execute(): void
    {
        try {
            $currentLocation = $this->movingObject->getLocation();
        } catch (GetLocationException $th) {
            (new LogExceptionCommand($th))->execute();
            throw $th;
        }

        try {
            $velocity = $this->movingObject->getVelocity();
        } catch (GetVelocityException $th) {
            throw $th;
        }

        $newPosition = $currentLocation->plus($velocity);

        try {
            $this->movingObject->setLocation($newPosition);
        } catch (MoveObjectException $th) {
            throw $th;
        }
    }
}
