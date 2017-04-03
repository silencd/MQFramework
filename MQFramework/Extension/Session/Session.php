<?php 
namespace MQFramework\Extension\Session;

use MQFramework\Helper\Config;
use MQFramework\Extension\Session\Drivers\RedisSessionDriver as RedisProvider;
use MQFramework\Extension\Session\Drivers\MemcacheSessionDriver as McProvider;

class Session
{
    /**
     * [Session Driver]
     * @var [object]
     */
    private $provider;
    /**
     * [Session Expire]
     * [Default time set 3600 seconds]
     * @var integer
     */
    private $expire = 3600;

    public function __construct()
    {
        $this->setSessionProvider();
        $this->registerSessionProvider();
    }
    public function setSessionProvider($provider = null)
    {
        if (! $provider) {
            $config = Config::get('config.app');
            if ($config['session_driver'] == 'file' || empty($config['session_driver'])) {
                    $this->provider = null;
                    return;
            } else {
                $provider = $config['session_driver'];
            }
            $this->setExpire($config['session_expire']);
        }
        $provider = strtoupper($provider);
        if ($provider == 'REDIS') {
            $this->provider = new RedisProvider;
        }
        if ($provider == 'MEMCACHE') {
            $this->provider = new McProvider;
        }
    }
    public function registerSessionProvider()
    {
        if (empty($this->provider)) {
            session_start();
            return;
        }
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            session_set_save_handler($this->provider, true);
        } else {
              session_set_save_handler(
                [$this, 'open'], 
                [$this, 'close'], 
                [$this, 'read'], 
                [$this, 'write'], 
                [$this, 'destroy'], 
                [$this, 'gc']
            );
        }
         session_start();
    }
    public function setExpire($expire = null)
    {
        if ($expire) {
            $this->expire = $expire;
        }
    }
    public function read($key)
    {
        $value = $this->provider->get($key);

        return empty($value) ? false : $value;
    }
    public function write($key, $value)
    {
        return $this->provider->write($key, $value, $this->expire);
    }
    public function destroy($key)
    {
       return $this->provider->destroy($key);
    }
    public function delete($key)
    {
        return $this->provider->delete($key);
    }
    public function gc($maxLifeTime)
    {
        return true;
    }
    public function open($path, $name)
    {
        return true;
    }
    public function close()
    {
        return true;
    }
    public function __destruct()
    {
       if (version_compare(PHP_VERSION, '5.4', '<')) {
            session_write_close();
        }
    }
}