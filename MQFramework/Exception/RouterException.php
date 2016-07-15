<?php 
namespace MQFramework;

class RouterException extends Exception 
{
	public function __construct($error) {
		echo $error;
	}
}