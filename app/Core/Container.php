<?php
namespace App\Core;

use Closure;
use ReflectionClass;
use ReflectionException;
use Exception;
use ReflectionUnionType;

class Container
{
    protected array $bindings = [];
    protected array $instances = [];

    public function bind($abstract, $concrete = null, $singleton = false): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        if (!$concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'singleton');
    }

    public function singleton($abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * @throws Exception
     */
    public function make($abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract]['concrete'] ?? $abstract;

        if ($concrete instanceof Closure) {
            $object = $concrete($this);
        } else {
            $object = $this->autoWire($abstract);
        }

        if (isset($this->bindings[$abstract]['singleton']) && $this->bindings[$abstract]['singleton']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    protected function getClosure($abstract, $concrete): Closure
    {
        return function ($container) use ($abstract, $concrete) {
            $method = ($abstract == $concrete) ? 'autoWire' : 'make';
            return $container->$method($concrete);
        };
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    protected function autoWire($abstract)
    {
        try {
            $reflector = new ReflectionClass($abstract);
        } catch (ReflectionException $e) {
            throw new Exception("Class $abstract does not exist. " . $e->getMessage());
        }

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class $abstract is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $abstract;
        }

        $parameters = $constructor->getParameters();
        $dependencies = array_map(function ($parameter) use ($abstract) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if (!$type) {
                throw new Exception("Cannot resolve the dependency $name of $abstract because it has no type hinting");
            }

            if ($type instanceof ReflectionUnionType) {
                throw new Exception("Container does not support union types in $abstract");
            }

            if ($type->isBuiltin()) {
                throw new Exception("Cannot resolve the built-in type $name of $abstract");
            }

            return $this->make($type->getName());
        }, $parameters);

        return $reflector->newInstanceArgs($dependencies);
    }
}