<?php
namespace MQFramework\Exception;

use Exception;
use MQFramework\Logging\Logger;
use MQFramework\Helper\Config;
use MQFramework\Application;
use MQFramework\Http\Exceptions\HttpException;
use MQFramework\Database\Exceptions\DBException;

class Handler
{
    protected $log;

    public function __construct(Logger $log)
    {
        $this->log = $log;
    }

    public function report(Exception $exception)
    {
        $this->log->error($exception);
    }

    public function render($request, Exception $e)
    {
        $message = '';
        if ($e instanceof HttpException) {
            return $this->toResponse($e->getResponse());
        }

        if ($e instanceof DBException) {
            $message = $e->getMessage();
        }

        $app = Config::get('config.app');
        if (! $app['debug'] ) {
            $message = '';
        }

        //页面样式
        $message = $this->decorate($message);

        return $this->toResponse($message);
    }

    public function toResponse($message)
    {
        $app = new Application;
        $response = $app->make('MQFramework\Http\Kernel');
        $response->setErrorInfo($message);
        return $response;
    }

    protected function decorate($message)
    {
        if ( empty($message) ) { return $message; }
        $style = <<<EOF
        <html>
            <header></header>
            <body>
                <div><table><tr>信息：<td>#msg#</td>
                Trace流：<td>#content#</td></tr></table></div>
            </body>
        </html>
EOF;

        return  str_replace(['#msg#', '#content#'], $message, $style);
    }
}
