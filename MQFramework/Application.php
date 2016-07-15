<?php 
namespace MQFramework;

class Application extends Container
{
	
	public $routerMap = [];
	public $basePath;
	protected $app;
	protected $singleton;

	public function __construct() {
		$this->setBasePath();	
	}
	

	public function setBasePath() {
		$this->basePath = dirname(__DIR__);
	}

	public function getBasePath() {
		return $this->basePath;
	}
}