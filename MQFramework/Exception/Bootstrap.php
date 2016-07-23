<?php
namespace MQFramework\Exception;

use Exception;
use ErrorException;
use Monolog\Logger as Monologger;
use MQFramework\Application;
use MQFramework\Helper\Config;

class Bootstrap
{
    public function boot()
    {
        $this->app = new Application;

        error_reporting(-1);

        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handleException']);

        register_shutdown_function([$this, 'handleShutdown']);

        $app = Config::get('config.app');
        if (! $app['debug'] ) {
            ini_set('display_errors', 'Off');
        }
    }

    public function handleShutdown()
    {
        if (! is_null($error = error_get_last()) && $this->isFatal($error['type']) ) {
            $this->handleException($this->fatalExceptionFromError($error, 0));
        }
    }

    public function handleError($level, $msg, $file = null, $line = 0)
    {
        if (error_reporting() & $level) {
            throw new ErrorException($msg, 0, $level, $file, $line);
        }
    }

    public function handleException($exception)
    {
        if (! $exception instanceof Exception) {
            echo "[not exception, Notice!!!]:".var_dump($exception);
            //$exception = new FatalThrowableError($exception);
        }

        //Logger exceptions
        $this->getExceptionHandler()->report($exception);
        //render exceptions in response
        $this->renderHttpResponse($exception);
    }

    //错误转化成异常
    public function fatalExceptionFromError($error = [], $offset = null)
    {
        return new ErrorException(
            $error['message'], 0, $error['type'], $error['file'], $error['line']
        );
    }

    public function renderHttpResponse(Exception $exception)
    {
        $this->getExceptionHandler()->render($this->app->request, $exception)->send();
    }

    public function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    public function getExceptionHandler()
    {
        return $this->app->make('MQFramework\Exception\Handler');
    }
}
