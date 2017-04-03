<?php
namespace App\Service;

use MQFramework\Helper\Config;

class RedisService
{
    private $host;
    private $port;
    public $handler;
    /**
     * @see https://github.com/phpredis/phpredis
     */
    public function __construct()
    {
        $this->getInstance();
        $this->conn();
    }

    private function getInstance()
    {
        $this->handler = $this->handler?: new \Redis;
    }
    
    protected function conn()
    {
        $config = Config::get('config.database');
        $host = isset($config['REDIS_HOST']) ? $config['REDIS_HOST'] : '127.0.0.1';
        $port = isset($config['REDIS_PORT']) ? $config['REDIS_PORT'] : '6379';
        $timeout = isset($config['REDIS_TIMEOUT']) ? $config['REDIS_TIMEOUT'] : '2';
        $this->handler->connect($host, $port, $timeout);
    }

    // public function setOptions(array $options = [])
    // {
    //     $this->handler->setOption($options);
    // }
}
