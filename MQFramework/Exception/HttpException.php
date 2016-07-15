<?php
namespace MQFramework;

class HttpException extends Exception 
{
	public function httpNotFound() {
		echo 404;
	}

	public function httpIsNotSupport() {}
}