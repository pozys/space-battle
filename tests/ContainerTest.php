<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Container;

final class ContainerTest extends TestCase
{
    public function testIoCShouldUpdateDependencyStrategy(): void
    {
        $test = false;

        Container::resolve(
            'Update Ioc Resolve Dependency Strategy',
            function (callable $formerStrategy) use (&$test): callable {
                $test = true;
                return $formerStrategy;
            }
        )->execute();

        $this->assertTrue($test);
    }

    public function testIoCThrowsExceptionWhenUnknownDependency(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Container::resolve('Unknown Dependency');
    }
}
