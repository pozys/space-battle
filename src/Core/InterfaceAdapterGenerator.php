<?php

declare(strict_types=1);

namespace Pozys\SpaceBattle\Core;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PsrPrinter;
use Nette\PhpGenerator\Type;
use Pozys\SpaceBattle\Interfaces\GameObjectInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class InterfaceAdapterGenerator
{
    private string $baseDependencyName = 'Spaceship.Operations.MovingInterface';
    private readonly ClassType $class;
    private readonly ReflectionClass $reflector;

    public function __construct(private readonly string $interfaceName, string $className)
    {
        $this->verifyInterfaceExists();
        $this->reflector = new ReflectionClass($interfaceName);

        $this->class = (new ClassType($className))->addImplement($interfaceName);
    }

    public function generate(): string
    {
        $class = $this->addConstructor($this->class);
        $class = $this->addMethods($class);

        return $this->print();
    }

    private function verifyInterfaceExists(): void
    {
        if (!interface_exists($this->interfaceName)) {
            throw new \Exception("Interface {$this->interfaceName} does not exist");
        }
    }

    private function addConstructor(ClassType $class): ClassType
    {
        $method = $class->addMethod('__construct');
        $method->addPromotedParameter('object')
            ->setPrivate()
            ->setReadOnly()
            ->setType(GameObjectInterface::class);

        return $class;
    }

    private function addMethods(ClassType $class): ClassType
    {
        foreach ($this->reflector->getMethods() as $method) {
            $class = $this->addMethod($class, $method);
        }

        return $class;
    }

    private function addMethod(ClassType $class, ReflectionMethod $reflectionMethod): ClassType
    {
        $method = $class->addMethod($reflectionMethod->getName())->setPublic();

        $method = $this->addParameters($method, $reflectionMethod);

        $method = $this->addReturnType($method, $reflectionMethod);

        $method = $this->setBody($method, $reflectionMethod);

        return $class;
    }

    private function addParameters(Method $method, ReflectionMethod $reflectionMethod): Method
    {
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $parameter = $method->addParameter($reflectionParameter->getName());

            $parameter->setType($this->getType($reflectionParameter->getType()));
        }

        return $method;
    }

    private function addReturnType(Method $method, ReflectionMethod $reflectionMethod): Method
    {
        $method = $method->setReturnType($this->getType($reflectionMethod->getReturnType()));

        return $method;
    }

    private function print(): string
    {
        $printer = new PsrPrinter();

        return $printer->printClass($this->class);
    }

    private function getType(ReflectionNamedType $type): string
    {
        $typeName = $type->getName();

        if ($type->allowsNull()) {
            $typeName = Type::nullable($typeName);
        }

        return $typeName;
    }

    private function setBody(Method $method, ReflectionMethod $reflectionMethod): Method
    {
        $methodName = $reflectionMethod->getName();
        $propertyName = lcfirst(substr($methodName, 3));

        return match (true) {
            str_starts_with($methodName, 'get') => $this->addGetterBody($method, $propertyName),
            str_starts_with($methodName, 'set') => $this->addSetterBody(
                $method,
                $propertyName,
                $reflectionMethod->getParameters()[0]->getName()
            ),
            default => throw new \Exception("Unknown method {$methodName}"),
        };
    }

    private function addGetterBody(Method $method, string $propertyName): Method
    {
        return $method->setBody(
            "return Pozys\SpaceBattle\Container::resolve('$this->baseDependencyName:$propertyName.get', \$this->object);",
        );
    }

    private function addSetterBody(Method $method, string $propertyName, string $value): Method
    {
        return $method->setBody(
            "Pozys\SpaceBattle\Container::resolve('$this->baseDependencyName:$propertyName.set', \$this->object, $$value)->execute();",
        );
    }
}
