<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pozys\SpaceBattle\Container;

final class ScopesTest extends TestCase
{
    public function testCanGetCurrentScope(): void
    {
        $currentScope = Container::resolve('IoC.Scope.Current');

        $this->assertNotEmpty($currentScope);
    }

    public function testCanSetCurrentScope(): void
    {
        $newScope = Container::resolve('IoC.Scope.Create');

        Container::resolve('IoC.Scope.Current.Set', $newScope);

        $this->assertSame($newScope, Container::resolve('IoC.Scope.Current'));
    }

    public function testCanClearCurrentScope(): void
    {
        $scope = Container::resolve('IoC.Scope.Create');

        Container::resolve('IoC.Scope.Current.Set', $scope);

        Container::resolve('IoC.Scope.Current.Clear');

        $this->assertNotSame($scope, Container::resolve('IoC.Scope.Current'));
    }

    public function testCanRegisterRootDependency(): void
    {
        Container::resolve('IoC.Register', 'someDependency', fn() => 1);

        $this->assertSame(1, Container::resolve('someDependency'));
    }

    public function testCanRegisterScopeDependency(): void
    {
        $newScope = Container::resolve('IoC.Scope.Create');

        Container::resolve('IoC.Scope.Current.Set', $newScope);

        Container::resolve('IoC.Register', 'someDependency', fn() => 1);

        $this->assertSame(1, Container::resolve('someDependency'));
    }

    public function testShouldFindDependencyInParentScopeWhenCurrentScopeDoesNotContainIt(): void
    {
        $test = false;

        $parentScope = Container::resolve('IoC.Scope.Create');
        Container::resolve('IoC.Register', 'someDependency', function () use (&$test) {
            $test = true;
        });

        $currentScope = Container::resolve('IoC.Scope.Create', $parentScope);

        Container::resolve('IoC.Scope.Current.Set', $currentScope);

        Container::resolve('someDependency');

        $this->assertTrue($test);
    }
}
