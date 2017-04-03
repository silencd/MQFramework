<?php
namespace MQFramework;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;

class Container
{
	protected $instances;

	protected $bindings = [];

	public function bind($abstract, $concrete = null) {
		if ( is_null($concrete) ) {
			$concrete = $abstract;
		}

		if (! $concrete instanceof Closure) {
			$concrete = $this->getClosure($abstract, $concrete);

			$this->bindings[$abstract] = $concrete;
		} else {
			$this->instances[$abstract] = $concrete;
		}
	}

	public function make($abstract, $parameters = []) {
		if ( isset($this->instances[$abstract]) ) {
			return $this->instances[$abstract];
		}
		//Closure
		$concrete = $this->getConcrete($abstract);

		$isBuildable = $this->isBuildable($concrete, $abstract);

		if ($isBuildable) {
			$object = $this->build($concrete, $parameters);
		} else {
			$object = $this->make($concrete, $parameters);
		}

		$this->instances[$abstract] = $object;

		return $object;
	}
	//通过反射类解析 创建实例
	protected function build($concrete, $parameters = []) {
		if ($concrete instanceof Closure) {
			return $concrete($this, $parameters);
		}

		$reflector = new ReflectionClass($concrete); 

		if ( ! $reflector->isInstantiable() ) {
			throw new Exception("$concrete can't init");
		}
		//parse params
		$constructor = $reflector->getConstructor();
		if ( is_null($constructor) ) {
			return new $concrete;
		}

		$parameters = $constructor->getParameters();

		$instances = $this->getResolveClass($parameters); 

		return $reflector->newInstanceArgs($instances);
	}

	public function singleton($abstract, $concrete = null) {
		$this->bind($abstract, $concrete);
	}

	protected function getClosure($abstract, $concrete) {
		return  function ($c, $parameters = []) use ($abstract, $concrete) {
			$method = ($abstract == $concrete) ? 'build' : 'make';
			return $c->$method($abstract, $parameters);
		};
	}

	protected function getConcrete($abstract) {
		if ( ! isset($this->bindings[$abstract]) ) {
			return $abstract;
		}

		return $this->bindings[$abstract];
	}
	protected function isBuildable($concrete, $abstract) {
		return $concrete === $abstract || $concrete instanceof Closure;
	}

	protected function getResolveClass($parameters) {
		$dependencies = [];
		foreach ($parameters as $parameter) {
			$className = $parameter->getClass();
			if (! is_null($className) ) {
				$dependencies[] = $this->resolveClass($parameter);
				// $dependencies[] = $className;
			}
		}
		return $dependencies;
	}
	protected function resolveClass(ReflectionParameter $parameter) {
		try {
			return $this->make($parameter->getClass()->name);
		} catch (Exception $e) {
			echo "Resolve error：".$e->getMessage();
		}
	}
}
