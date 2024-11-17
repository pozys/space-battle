<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Application;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\{Level, Logger};
use Pozys\SpaceBattle\Interfaces\CommandInterface;
use Psr\Log\LoggerInterface;

class LogExceptionCommand implements CommandInterface
{
    public function __construct(
        private readonly CommandInterface $command,
        private readonly Exception $ex,
        private ?LoggerInterface $logger = null
    ) {
        $this->logger ??= $this->buildDefaultLogger();
    }

    public function execute(): void
    {

        $this->logger->error($this->ex->getMessage(), [
            'exception' => $this->ex,
            'command' => $this->command,
        ]);
    }

    private function buildDefaultLogger(): LoggerInterface
    {
        $logger = new Logger('exceptions');

        return $logger->pushHandler(new StreamHandler('php://stderr', Level::Error));
    }
}
