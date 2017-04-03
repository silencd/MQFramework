<?php 
namespace MQFramework\Extension\Session\Drivers;

use SessionHandlerInterface;
use MQFramework\Extension\Session\SessionTrait;

class MemcacheSessionDriver implements SessionHandlerInterface
{
    public $handler = NULL;
    use SessionTrait;

    public function set($key,$value,$expire=null)
    {

    }
    public function delete($key)
    {
        
    }
    public function get($key)
    {

    }
}