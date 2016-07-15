<?php
//统一处理
namespace Executor;

use Base\Executor\ExecutorAbstract as ExecutorAbstract;

class Init extends ExecutorAbstract
{
    protected $jobInfo = null;

    private $table = 'task_result';
    private $taskTable = 'task';

    private $module_path = '../Module/'; //模块位置
    private $executor = 'python';  //默认python脚本
    private $executorList = ['php', 'python', 'python3']; //脚本执行器列表

    public function __construct($payload)
    {
        $this->jobInfo = $this->parseJob($payload);
    }

    public function run()
    {
        //判断模块是否存在
        $module = __DIR__.'/'.$this->module_path.$this->jobInfo['module'];
        //执行脚本
        $script = $module.'/'.$this->jobInfo['action'];
        //脚本参数
        $params = base64_decode($this->jobInfo['params']);

        if ( is_dir($module) && file_exists($script) ) {
            try {
                $command = sprintf("%s %s %s", $this->executor, $script, $params);
                // echo $command;
                exec(escapeshellcmd($command), $response);
            } catch (\Exception $e) {
                $msg = "Execute Error...Command:$command, Message: ".$e->getMessage();
                throw new \Exception($msg);
            }
            // var_dump($response);
            //解析结果
            $result = $this->parseResponse($response);
            //保存结果
            $this->executorToDB('save', $result);
        } else {
            throw new \Exception("{$module} Module not exists  or {$script} Script not exists !");
        }
    }

    //分析执行结果
    public function parseResponse($response)
    {
        //分析结果
        $data['task_id'] = $this->jobInfo['task_id'];
        $data['result'] = 'xxxooo'; //测试
        $data['create_time'] = date('Y-m-d H:i:s', time());
        //根据结果状态，更新任务状态
        if ( rand(0,1) ) {
            $status = 2;
        } else {
            $status = 3;
        }

        $this->taskToDB('update', ['task_status' => $status, 'task_id' => $this->jobInfo['task_id']]);
        return $data;
    }
    //分析任务
    private function parseJob($payload)
    {
        if ( empty($payload['module']) || empty($payload['action']) ) {
            throw new \Exception("Execute Error without Module, Payload info:".json_encode($payload));
        }
        if ( empty($payload['task_id']) ) {
            throw new \Exception("Execute Error without TaskID !");
        }
        //更新任务状态
        $this->taskToDB('update', ['task_status' => 1, 'task_id' => $payload['task_id']]);

        $payload['module'] = strtolower($payload['module']);

        if ( isset($payload['executor']) && in_array(strtolower($payload['executor']), $this->executorList) ) {
            $this->executor = strtolower($payload['executor']);
        }
        return $payload;
    }
    //保存数据
    private function executorToDB($operator, $params = [])
    {
        $db = new \Base\Database\Db();

        if ($operator == 'save') {
            if ( empty($params) ) {
                throw new \Exception("Executor Insert Task_Result DB Error, Empty data !");
            }
            try {
                $db->table($this->table)->save($params);
            } catch (\Exception $e) {
                echo "Executor Insert Task_Result DB Error, ".$e->getMessage();
            }
        }
        if ($operator == 'update') {
            // if ( empty($params['task_id']) ) {
            //     throw new \Exception("Executor Update DB Error, No Task ID");
            // }
            try {
                $condition = ['task_id' => $this->jobInfo['task_id']];
                $db->table($this->table)->where($condition)->data($params)->update();
            } catch (\Exception $e) {
                echo "Executor Update Task_Result DB Error, ".($e->getMessage()).", Param: ".json_encode($params);
            }
        }
    }
    private function taskToDB($operator, $params = [])
    {
        $db = new \Base\Database\Db();

        if ($operator == 'update') {
            if ( empty($params['task_id']) ) {
                throw new \Exception("Executor Update Task DB Error, No Task ID");
            }
            try {
                //更新任务状态 1 start, 2 finish, 3 fail
                $condition = ['id' => $params['task_id']];
                unset($params['task_id']);
                $db->table($this->taskTable)->where($condition)->data($params)->update();
            } catch (\Exception $e) {
                echo "Executor Update Task DB Error, ".($e->getMessage()).", Param: ".json_encode($params);
            }
        }
    }
}
