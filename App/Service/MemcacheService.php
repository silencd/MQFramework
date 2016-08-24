<?php
namespace App\Service;

use Exception;
use MQFramework\Helper\Config;

class MemcacheService
{
    private $host;
    private $port;
    private $handler = null;
    public $pool = [];

    public function __construct()
    {
        $this->handler = isset($this->handler)?: new \Memcached;
        $this->connect();
    }
    /**
     * [设置Ｍemcache Config]
     * @param array $config [description]
     *  host port weight
     *  $config = [['127.0.0.1', '11211', 20], ['192.168.0.1', 11211, 2]];
     */
    public function setConfig(array $config = [])
    {
        $list = [];
        foreach ($config as $m) {
            if (! $this->isOffline($host = $m[0])) {
                $list[] = $m;
            }
        }
        $this->pool = array_merge($this->pool, $list);
    }
    /**
     * [connect description]
     * @return [type] [description]
     */
    public function connect()
    {
        if (empty($this->pool)) {
            $db = Config::get('config.database');
            $this->host = $db['MEMCACHE_HOST'];
            $this->port = $db['MEMCACHE_PORT'];
            $this->pool = [[$this->host, $this->port]];
        }
        $this->handler->addServers($this->pool);
    }
    public function removeServer(string $ip)
    {
        foreach ($this->pool as $k=>$m) {
            if ($m[0] == $ip) unset($this->pool[$k]);
        }
    }
    /**
     * [setConsistentHash 设置一致性hash]
     * @see http://php.net/manual/en/memcached.constants.php
     * 开启一致性hash后, memcache weight才起生效
     */
    public function setConsistentHash()
    {
        $this->handler->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        $this->handler->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
    }
    public function set($k, $v, $expire = null)
    {
        return $this->handler->set($k, $v, $expire);
    }
    public function setMore(array $k)
    {
        return  $this->handler->setMulti($k);
    }
    public function get($k)
    {
        return $this->handler->get($k);
    }
    public function getMore(array $k)
    {
        return $this->handler->getMulti($k);
    }
    public function decrement($k, $offset = 1)
    {
        return $this->handler->decrement($k, $offset);
    }
    public function increment($k, $offset = 1)
    {
        return $this->handler->increment($k, $offset);
    }
    public function delete($k, $delay = 0)
    {
        return $this->handler->delete($k, $delay);
    }
    public function deleteMore(array $k)
    {
        return $this->handler->deleteMulti($k);
    }
    /**
     * [append 追加类型必须和值类型相同]
     * @param  [type] $k [description]
     * @param  [type] $v [description]
     * @return [type]    [description]            
     */
    public function append($k, $v)
    {
        if (gettype($this->handler->get($k)) != gettype($v)) {
            return false;
        }
        return $this->handler->append($k, $v);
    }
    /**
     * [fappend 强制追加，类型自动转换]
     * @param  [type] $k [description]
     * @param  [type] $v [description]
     * @return [type]    [description]
     */
    public function fappend($k, $v)
    {
        $this->handler->setOption(Memcached::OPT_COMPRESSED, false);
        return $this->handler->prepend($k, $v);
    }
    public function flush()
    {
        return  $this->handler->flush();
    }
    public function getServerStatus()
    {
        return $this->handler->getStats();
    }
    public function __destruct()
    {
        $this->handler->quit();
    }
    public function __call($method, $param)
    {
        throw new Exception("MemcacheService method[$method] not exists");
    }
}