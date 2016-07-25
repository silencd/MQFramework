<?php
namespace MQFramework\Exception;

use Exception;

class ErrorThrowable extends Exception
{
    public function __construct(\Throwable $e)
    {
        
    }
}
