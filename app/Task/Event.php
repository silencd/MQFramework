<?php
namespace Task;

use Executor\Init;

class Event
{
    private $payload = null;

    public function listen()
    {

    }
    public function register($params = [])
    {
        $this->payload = $params;
    }
    public function trigger()
    {
        $executorInit = new Init($this->payload);
        $executorInit->run();
    }
}
