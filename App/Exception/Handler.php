<?php
namespace App\Exception;

use App\Service\WebHandler;
use Monolog\Logger as Monologger;
use MQFramework\Logging\Logger;
use MQFramework\Exception\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function report(\Exception $e)
    {
        //自定义消息通知的handler
        $channel = 'Robot';
        $logger = new Monologger($channel);
        $logger->pushHandler($handler = new WebHandler);
        $logger->error($e);
        return parent::report($e);
    }
}
