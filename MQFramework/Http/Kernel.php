<?php
namespace MQFramework\Http;

use MQFramework\Application;
use MQFramework\Routing\Router;
use MQFramework\Http\Exceptions\HttpException;

class Kernel
{
	protected $router;
	protected $app;
	protected $request;
	protected $response;

	public function __construct(Application $app, Router $router)
	{
		$this->app = $app;
		$this->router = $router;
		//oad router configure
		$this->app->routerMap = $this->router->loadRouterConfig();
		//load environment bootstrap
		$this->app->bootstrap();
	}

	public function handle($request)
	{
		$this->app->request = $request;
		$this->response = $this->forwardRequestThroughRouter($request);
		return $this;
	}
	//处理原始Http请求
	public function request() {
		$httpRequest = [
			'uri' => $_SERVER['REQUEST_URI'],
			'method' => $_SERVER['REQUEST_METHOD'],
			'ip' => $_SERVER['REMOTE_ADDR'],
			'port' =>$_SERVER['SERVER_PORT'],
			'cookie' => $_COOKIE,
		];

		if ($httpRequest['method'] == 'POST') {
			$parameters = [ 'parameters' => $_POST];
			$httpRequest = array_merge($httpRequest, $parameters);
		}

		return $this->parseRequest($this->request = $httpRequest);
	}
	//解析请求
	protected function parseRequest($request) {
		if (! $this->isPathInfo($request) ) {
			throw new HttpException("Http请求方式不支持！");
		}

		$requestUri = explode('/', $request['uri']);
		//设置模块
		$queryModule = isset($requestUri[1]) && !empty($requestUri[1])? $requestUri[1] : 'index';
		//设置控制器
		$queryController = isset($requestUri[2]) && !empty($requestUri[2]) ? $requestUri[2] : 'index';
		//设置参数
		$parameters = $this->parseParameter();

		return $this->matchRoute($queryModule, $queryController, $parameters);
	}
	//解析参数
	protected function parseParameter() {
		$parameters = [];
		$requestUri = explode('/', $this->request['uri']);

		if ( count($requestUri) > 2 && !empty($requestUri[2]) ) {
			if ($this->request['method'] == 'POST') {
				//处理post请求
				$parameters['post'] = $this->request['parameters'];
			}
			if ($this->request['method'] == 'GET') {
				//URL地址中带有?形式参数, 仅支持path_info模式
				$controllerPos = strpos($this->request['uri'], $requestUri[2]) + 1 + strlen($requestUri[2]);

				$uri = substr($this->request['uri'], $controllerPos);

				$arr = explode('/', $uri);

				$p1 = $p2 = $p3 = [];

				if ( isset($arr[0]) && !empty($arr[0]) ) {
					$v1 = isset($arr[1]) && !empty($arr[1]) ? $arr[1] : '';
					$p1 = [$arr[0] => $v1];
				}
				if ( isset($arr[2]) && !empty($arr[2]) ) {
					$v2 = isset($arr[3]) && !empty($arr[3]) ? $arr[3] : '';
					$p2 = [$arr[2] => $v2];
				}
				if ( isset($arr[4]) && !empty($arr[4]) ) {
					$v3 = isset($arr[5]) && !empty($arr[5]) ? $arr[5] : '';
					$p3 = [$arr[4] => $v3];
				}
				$parameters = array_merge($p1, $p2, $p3);
			}
		}
		return $parameters;
	}
	//匹配路由表 404
	protected function matchRoute($queryModule, $queryController, $parameters) {
		$flag = 0;
		foreach ($this->app->routerMap as $controllerAlias =>$controllerName) {
			if ($controllerAlias == $queryModule.'/'.$queryController) {
				$controller = $controllerName;
				$method = $this->addHttpMethod($queryController); //类中方法名
				$flag++;
			}
		}

		if ($flag == 0) {
			throw new HttpException("请求地址不存在：[`$queryModule/$queryController`]");
		}
		return ['controller' => $controller, 'method' => $method, 'parameters' => $parameters];
	}
	//判断URI是否pathinfo模式
	protected function isPathInfo($request) {
		if (strpos($request['uri'], '?') === false) {
			return true;
		}
	}
	protected function filter($parameter) {
		return filter_var($parameter, FILTER_SANITIZE_MAGIC_QUOTES);
	}
	//转发到路由
	public function forwardRequestThroughRouter($request) {
		return $this->router->dispatch($request);
	}
	//修改方法名称 http请求类型+方法名
	private function addHttpMethod($method)
	{
		return strtolower($this->request['method']).$method;
	}
	public function setErrorInfo($message)
	{
		$this->response = $message;
	}
	//回显
	public function send() {
		echo $this->response;
	}
}
