<?php
//处理任务相关 根据调度的指令执行
namespace Task;

use Task\Event;
use Task\Conductor;

class Task
{
    protected $taskId;
    private $taskParams = null;
    private $table = 'task';

    /**
     * [addTask description]
     * @param array $params [description]
     */
    public function addTask($params = [])
    {
        if ( $this->parseTask($params) ) {
            $taskId = $this->taskToDB('add', $this->taskParams);
            $this->taskParams['task_id'] = $taskId;
            return $taskId;
        } else {
            return false;
        }
    }
    /**
     * [removeTask description]
     * @return [type] [description]
     */
    public function removeTask($taskId)
    {
        if ( $this->isRun($taskId) ) {
            //移除正在执行的任务
        }
        return $this->taskToDB('remove', ['id' => $taskId]);
    }

    public function isRun($taskId = '')
    {

    }
    public function isFinish($taskId = '')
    {
        $taskInfo = $this->taskToDB('get', ['id' => $taskId]);
        if ($taskInfo['task_status'] == 1) {
            return true;
        }
    }
    public function run()
    {
        $conductor = new Conductor();
        // $event = new Event();
        $conductor->dispatch(new Event(), $this->taskParams);
    }
    /**
     * [解析任务格式 统一数据]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function parseTask($params = [])
    {
        if (empty($params['name']) || empty($params['module'])
                || empty($params['user_id'])
                || empty($params['action']))
        {
            return false;
        }
        // if ( ! isset($params['description']) ) {
        //     $params['description'] = '';
        // }
        if ( ! isset($params['params']) ) {
            $params['params'] = '';
        } else {
            $params['params'] = base64_encode($params['params']);
        }
        if ( isset($params['schedule']) ) {
            $params['type'] = 'queue';
            $params['schedule'] = 1;
            $params['schedule_time'] = 10; //10s后执行
        } else {
            $params['type'] = 'task';
            $params['schedule'] = 0;
        }
        if (! isset($params['policy_id']) ) {
            $params['policy_id'] = '';
        }
        $params['create_time'] = date('Y-m-d H:i:s', time());

        return $this->taskParams = $params;
    }
    private function taskToDB($operator, $params = [])
    {
        $db = new \Base\Database\Db();
        if ($operator == 'add') {
            return  $db->table($this->table)->save($params);
        }
        if ($operator == 'remove') {
            return $db->table($this->table)->where($params)->data(['status' => 1])->update();
        }
        if ($operator == 'get') {
            return $db->table($this->table)->where($params)->get();
        }
    }
}
