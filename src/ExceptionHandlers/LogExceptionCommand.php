<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\ExceptionHandlers;

use Exception;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class LogExceptionCommand implements CommandInterface
{
    public function __construct(private readonly Exception $ex) {}
    // public function __construct(private readonly CommandInterface $command, private readonly Exception $ex) {}

    public function execute(): void
    {
        // TODO: implement
    }
}
