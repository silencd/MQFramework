<?php
namespace MQFramework\Logging;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as PsrLogInterface;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use MQFramework\Application;
use MQFramework\Helper\Config;

class Logger implements PsrLogInterface
{
    protected $levels = [
        'debug' => MonologLogger::DEBUG,
        'info' => MonologLogger::INFO,
        'notice' => MonologLogger::NOTICE,
        'warning' => MonologLogger::WARNING,
        'error' => MonologLogger::ERROR,
        'critical' => MonologLogger::CRITICAL,
        'alert' => MonologLogger::ALERT,
        'emergency' => MonologLogger::EMERGENCY,
    ];

    private $logger = null;
    protected $path = null;
    protected $channel = 'MQFramework';

    public function __construct()
    {
        if ( is_null($this->logger) ) {
            $this->logger = new MonologLogger($this->channel);
        }
        $this->setPath();
    }
    public function setPath()
    {
        if ( is_null($this->path) ) {
            $app = new Application;
            $env = Config::get('config.app');
            if (isset($env['log_path']) && !empty($env['log_path'])) {
                $file = $app->getBasePath().'/'.$env['log_path'];
                $this->setLogName($file);
            }
        }
    }
    public function error($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }
    public function emergency($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        $this->writeLog($level, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    protected function writeLog($level, $msg, $context)
    {
        if (! is_null($this->path) ) {
            $this->logger->pushHandler($handler = new StreamHandler($this->path, $level));
            $handler->setFormatter($this->setLogFormatter());
            $this->logger->{$level}($msg, $context);
        } else {
            $this->logger->{$level}($msg, $context);
        }
    }
    protected function setLogName($file)
    {
        if (! is_dir($file) ) {
            mkdir($file, 0755);
        }
        $name = substr(date('Y-m-d', time()), 2);
        $this->path = $file.$name.'.log';
    }
    protected function parseLevel($level)
    {
        if ( isset($this->levels[$level]) ) {
            return $this->levels[$level];
        }
        throw new InvalidArgumentException('Invalid log level');
    }
    //set monolog log format
    protected function setLogFormatter()
    {
        return new LineFormatter(null, null, true, true);
    }
}
