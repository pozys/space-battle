<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\AuthServer\Entities;

class User
{
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }
}
