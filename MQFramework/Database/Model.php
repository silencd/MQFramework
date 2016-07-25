<?php
namespace MQFramework\Database;

use MQFramework\Database\Db;

class Model extends Db
{
	protected $table = null;

	public function __construct() {
		//设置表名
		$this->table($this->getTable());
		parent::__construct();
	}

	protected function getTable() {
		if ( is_null($this->table) ) {
			return $this->classToTable($this);
		}
		return $this->table;
	}

	public  function setTable($table)
	{
		$this->table = $table;
		return $this;
	}

	public function classToTable($class)
	{
		$className = is_object($class) ? get_class($class) : $class;
		if (strpos($className, '\\') > 0 ) {
			$arr = explode('\\', $className);
			$className = array_pop($arr);
		}
		//表名为复数
		$tableName = strtolower($className).'s';
		return $tableName;
	}

	public function __call($method, $args)
	{
        throw new \Exception("{$method}() function not exists !");
	}
}
