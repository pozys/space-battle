<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

use Pozys\SpaceBattle\Interfaces\{ChangingVelocityInterface, CommandInterface};
use Pozys\SpaceBattle\ValueObjects\Vector;

class ChangeVelocityCommand implements CommandInterface
{
    public function __construct(private readonly ChangingVelocityInterface $changingVelocityObject) {}

    public function execute(): void
    {
        $angle = $this->changingVelocityObject->getAngle();
        $velocity = $this->changingVelocityObject->getVelocity();
        $velocityModule = sqrt($velocity->x * $velocity->x + $velocity->y * $velocity->y);

        $this->changingVelocityObject->setVelocity(new Vector(
            $velocityModule * cos($angle->toRadians()),
            $velocityModule * sin($angle->toRadians())
        ));
    }
}
