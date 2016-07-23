<?php
namespace MQFramework;

class Application extends Container
{

	public $basePath;
	protected $app;
	public $request = null;

	protected $bootstraps = [
		'exception' => 'MQFramework\Exception\Bootstrap',
	];

	public function __construct()
	{
		$this->setBasePath();
	}

	public function setBasePath()
	{
		$this->basePath = dirname(__DIR__);
	}

	public function getBasePath()
	{
		return $this->basePath;
	}

	public function bootstrap()
	{
		array_walk($this->bootstraps, function($concrete, $alias) {
			$this->bindings[$alias] = $this->make($concrete);
			$this->bindings[$alias]->boot();
		});
	}
}