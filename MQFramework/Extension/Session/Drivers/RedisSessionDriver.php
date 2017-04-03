<?php
namespace MQFramework\Extension\Session\Drivers;

use Redis;
use SessionHandlerInterface;
use MQFramework\Helper\Config;
use MQFramework\Extension\Session\SessionTrait;

class RedisSessionDriver implements SessionHandlerInterface
{
    public $handler = NULL;

    private $host;

    private $port = 6379;

    use SessionTrait;

    public function __construct()
    {
        $this->handler = ($this->handler) ? $this->handler : (new Redis);
        $this->connect();
    }
    public function connect()
    {
        $config = Config::get('config.app');
        $this->host = $config['redis']['host'];
        $this->port = ($config['redis']['port']) ?: $this->port;
     
        $auth = $config['redis']['auth'];
        
        if (empty($this->host)) {
            throw new Exception("Can't find Redis Configure, Please configure!");
        }
        $this->handler->connect($this->host, $this->port);
        
        if ($auth) {
            $this->handler->auth($auth);
        }
        
        $this->handler->setOption(Redis::OPT_PREFIX, $config['session_prefix']);
    }
    public function set($key,$value,$expire)
    {
        return $this->handler->set($key, $value, $expire);
    }
    public function delete($key)
    {
        return $this->handler->delete($key);
    }
    public function get($key)
    {
        return $this->handler->get($key);
    }
}