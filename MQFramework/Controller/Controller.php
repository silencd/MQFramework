<?php
namespace MQFramework\Controller;

abstract class Controller
{
	public function __construct() {}

	protected function view() 
	{

	} 
	protected function display()
	{
		return ;
	}
	public function __call($method, $parameters) {
		throw new \Exception("方法[$method]不存在");
	}
}