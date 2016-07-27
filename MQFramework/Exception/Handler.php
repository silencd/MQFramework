<?php
namespace MQFramework\Exception;

use Exception;
use MQFramework\Application;
use MQFramework\Helper\Config;
use MQFramework\Logging\Logger;
use MQFramework\Http\Exceptions\HttpException;
use MQFramework\Database\Exceptions\DBException;

class Handler
{
    protected $log = null;
    protected $app;

    public function __construct()
    {
        $this->log = is_null($this->log) ? new Logger : $this->log;
    }

    public function report(Exception $exception)
    {
        $this->log->error($exception);
    }

    public function render(Exception $e)
    {
        $message = '';
        if ($e instanceof HttpException) {
            return $this->toResponse($e->getResponse());
        }

        $message = $this->decorate($e);

        if ($e instanceof DBException) {
            $message = $e->getResponse();
        }

        $app = Config::get('config.app');
        if (! $app['debug'] ) {
            $message = '';
        }
        $message = $this->decorate($message);
        return $this->toResponse($message);
    }

    public function toResponse($message)
    {
        $this->app = is_null($this->app) ? new Application : $this->app;
        $response = $this->app->make('MQFramework\Http\Kernel');
        $response->setErrorInfo($message);
        return $response;
    }

    protected function decorate($m)
    {
        if ( empty($m) ) {
            return $m;
        }
        if (is_object($m)) {
            $msg = "<table border=1 cellspacing=0>";
            $msg.= "<tr><td>Message</td><td>{$m->getMessage()}</td></tr>";
            $msg.= "<tr><td>File</td><td>{$m->getFile()}</td></tr>";
            $msg.= "<tr><td>Line</td><td>{$m->getLine()} Line</td></tr>";
            $msg.= "<tr><td>Error Code</td><td>{$m->getCode()}</td></tr>";
            $msg.= "</table>";
            return $msg;
        }
        $style = <<<EOF
        <html>
            <header></header>
            <body>
                <div>#context#</div>
            </body>
        </html>
EOF;

        return  str_replace('#context#', $m, $style);
    }
}
