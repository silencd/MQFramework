<?php 
namespace App\Service;

use MQFramework\Extension\Session\Session;

class SessionService
{
    public $session;

    public function __construct()
    {
        $this->session = new Session;
    }
    public function add($key, $value, $expire = null)
    {
        $this->session->write($key, $value, $expire);
    }
    public function delete($key)
    {
        $this->session->delete($key);
    }
    public function get($key)
    {
        return $this->session->read($key);
    }
}