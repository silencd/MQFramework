<?php
namespace App\Service;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger as Monologger;
use MQFramework\Logging\Logger;

class WebHandler extends AbstractProcessingHandler
{
    //Bearychat Web Api Notify Service
    private $api = 'https://hook.bearychat.com/';

    public function __construct($level = Monologger::NOTICE, $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    protected function write(array $record = [])
    {
        $title = 'MQFramework出现异常,';
        $title.= '错误级别:'.$record['level_name'];
        $title.= '时间:'.$record['datetime']->format('Y-m-d H:i:s');
        $data = [
            'text' => $title.$record['message'],
            'notification' => $title,
            'markdown' => false,
        ];
        $data = 'payload='.json_encode($data);
        $this->send($data);
    }

    private function send($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type : application/json'
        ]);

        $response = curl_exec($ch);
        $result = json_decode($response, true);
        if ($result['code'] != 0) {
            $log = new Logger;
            $log->warning($response);
        }
    }
}
