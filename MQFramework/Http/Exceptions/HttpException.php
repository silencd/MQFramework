<?php
namespace MQFramework\Http\Exceptions;

use Exception;

class HttpException extends Exception
{
	protected $httpError;

	public function __construct($message)
	{
		$this->httpError = $message;
	}

	public function getResponse()
	{
		return $this->httpError;
	}
}
