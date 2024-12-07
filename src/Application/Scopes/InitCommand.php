<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle;

namespace Pozys\SpaceBattle\Application\Scopes;

use Exception;
use Pozys\SpaceBattle\Container;
use Pozys\SpaceBattle\Interfaces\CommandInterface;

class InitCommand implements CommandInterface
{
    private static Scope $rootScope;

    private static ?Scope $currentScope;

    public function execute(): void
    {
        self::$rootScope = new Scope();

        self::$rootScope->add('IoC.Scope.Current.Set', fn(...$params) => self::$currentScope = $params[0]);

        self::$rootScope->add('IoC.Scope.Current.Clear', fn() => self::$currentScope = null);

        self::$rootScope->add('IoC.Scope.Current', fn(...$params) => self::$currentScope ?? self::$rootScope);

        self::$rootScope->add('IoC.Scope.Parent', fn() => throw new Exception('The root scope has no a parent scope.'));

        self::$rootScope->add('IoC.Scope.Create.Empty', fn() => new Scope());

        self::$rootScope->add('IoC.Scope.Create', function (...$params) {
            $newScope = Container::resolve('IoC.Scope.Create.Empty');

            $parentScope = empty($params) ? Container::resolve('IoC.Scope.Current') : $params[0];
            $newScope->add('IoC.Scope.Parent', fn() => $parentScope);

            return $newScope;
        });

        self::$rootScope->add('IoC.Register', fn(...$params) => (new RegisterDependencyCommand(...$params))->execute());

        Container::resolve(
            'Update Ioc Resolve Dependency Strategy',
            fn(callable $formerStrategy) => function (string $dependency, ...$params) {
                return (new DependencyResolver(self::$currentScope ?? self::$rootScope))->resolve($dependency, ...$params);
            }
        )->execute();
    }
}
