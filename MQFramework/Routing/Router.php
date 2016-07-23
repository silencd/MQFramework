<?php
namespace MQFramework\Routing;

use MQFramework\Application;
use MQFramework\Http\Exceptions\HttpException;

class Router
{
	protected $app;
	protected $basePath;
	private $currentRequest;

	private $controllerPrefix = 'App\Controller\\'; //控制器存放位置

	private $routeConfig = '/config/routers.php'; //路由配置文件

	public function loadRouterConfig()
	{
		$this->app = $this->app ?: new Application;

		$routeConfig = $this->app->getBasePath().$this->routeConfig;

		if ( ! file_exists($routeConfig) ) {
			throw new \Exception("路由配置文件不存在!");
		}

		$routerMap = require $routeConfig;

		return $routerMap;
	}

	public function dispatch($request)
	{
		$this->currentRequest = $request;

		$response = $this->dispatchToRoute($request);

	    return $this->prepareResponse($request, $response);
	}

	public function dispatchToRoute($request)
	{
		//查找路由表
		//$route = $this->findRoute($request);

		return $this->dispatchToController($request);
	}
	//转发控制器处理
	public function dispatchToController()
	{
		$this->app = $this->app ?: new Application;

		return $this->runController();
	}

	public function runController()
	{
		$controller = $this->changeControllerToClass($this->currentRequest['controller']);

		$method = $this->currentRequest['method'];

		$parameters = $this->currentRequest['parameters'];

		$instance = $this->app->make($controller);

		return $this->callAction($instance, $method, $parameters);
	}

	protected function callAction($instance, $method, $parameters)
	{
		return call_user_func_array([$instance, $method], $parameters);
	}
	//预处理返回结果
	public function prepareResponse($request, $response)
	{
		return $response;
	}

	protected function changeControllerToClass($controller)
	{
		$className = str_replace('/', '\\', $controller);
		$httpMethod = $this->currentRequest;
		return $this->controllerPrefix.$className;
	}
}
