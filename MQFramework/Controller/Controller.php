<?php
namespace MQFramework\Controller;

use MQFramework\Application;

abstract class Controller
{
	private $dependencies = [
		'view' => 'MQFramework\Template\View',
	];
	private $instances = [];

	public function __construct()
	{
		$this->resolveDependencies();
	}
	private function resolveDependencies()
	{
		$app = new Application;
		foreach ($this->dependencies as $alias => $class) {
			$this->instances[$alias] = $app->make($class);
		}
	}
	protected function assign($data = [])
	{
		$this->instances['view']->render($data);
	}
	protected function display($tpl)
	{
		return $this->instances['view']->display($tpl);
	}
	public function __call($method, $parameters) {
        $class = get_called_class();
		throw new \Exception("method $method not exists in `$class`");
	}
}
