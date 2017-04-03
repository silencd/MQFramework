<?php
namespace MQFramework\Extension\Session;

trait SessionTrait
{
    public function write($key, $value){
        $this->handler->set($key, $value);
    }
    public function destroy($key){
        $this->handler->delete($key);
    }
    public function read($key)
    {
        return $this->handler->get($key);
    }
    public function open($path, $name){
        return true;
    }
    public function close(){
        return true;
    }
    public function gc($maxLifeTime)
    {
        return true;
    }

}