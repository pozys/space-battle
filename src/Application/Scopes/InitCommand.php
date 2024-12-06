<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

namespace Pozys\SpaceBattle\Application\Scopes;

use Exception;
use Pozys\SpaceBattle\Container;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class InitCommand implements CommandInterface
{
    private static array $rootScope = [];

    private static ?array $currentScope = null;

    public function __construct() {}

    public function execute(): void
    {
        self::$rootScope['IoC.Scope.Current.Set'] = fn(...$params) => (new SetCurrentScopeCommand($params[0]))->execute();

        self::$rootScope['IoC.Scope.Current.Clear'] = fn() => (new ClearCurrentScopeCommand())->execute();

        self::$rootScope['IoC.Scope.Current'] = fn() => self::$currentScope ?? self::$rootScope;

        self::$rootScope['IoC.Scope.Parent'] = fn() => throw new Exception('The root scope has no a parent scope.');

        self::$rootScope['IoC.Scope.Create.Empty'] = fn() => [];

        self::$rootScope['IoC.Scope.Create'] = function (...$params) {
            $newScope = Container::resolve('IoC.Scope.Create.Empty');

            $parentScope = empty($params) ? Container::resolve('IoC.Scope.Current') : $params[0];
            $newScope['IoC.Scope.Parent'] = fn() => $parentScope;

            return $newScope;
        };

        self::$rootScope['IoC.Register'] = fn(...$params) => (new RegisterDependencyCommand(...$params))->execute();

        Container::resolve(
            'Update Ioc Resolve Dependency Strategy',
            fn(callable $formerStrategy) => function (string $dependency, ...$params) {
                $scope = self::$currentScope ?? self::$rootScope;

                return (new DependencyResolver($scope))->resolve($dependency, ...$params);
            }
        )->execute();
    }

    public static function setCurrentScope(?array $currentScope): void
    {
        self::$currentScope = $currentScope;
    }
}
