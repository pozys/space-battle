<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Interfaces;

interface CommandInterface
{
    public function execute(): void;
}
