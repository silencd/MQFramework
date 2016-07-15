<?php
//接受任务，设定策略，制定执行指令
namespace Task;

use Task\Schedule;
use Task\Event;

class Conductor
{
    protected $taskId;
    protected $maxTaskCnt = 100;
    protected $taskTimeout = 60;
    private $payload = null;
    private $handle = null;

    // public function __construct(Event $event, $payload)
    // {
    //     $this->handle = $event;
    //     $this->payload = $this->parseParam($payload);
    // }
    public function addLock()
    {

    }

    public function removeLock()
    {

    }
    public function schedule()
    {

    }
    public function dispatch(Event $event, $payload)
    {
        $payload = $this->parseParam($payload);
        $event->register($payload);
        $event->trigger();
    }
    private function parseParam($params)
    {
        return $params;
    }
}
