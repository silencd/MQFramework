<?php
namespace MQFramework\Database;

use MQFramework\Application;

class Connector
{
    private $configFile = '/config/database.php';

    public function __construct()
    {
        $this->config = $this->getConfig();
    }

    public function getConfig()
    {
        $app = new Application;

        $configFile =  $app->getBasePath().$this->configFile;
        
        if ( file_exists($configFile) ) {
            $dbConfig = require $configFile;
        } else {
            throw new \Exception("数据库配置文件不存在");
        }

        if ( ! is_array($dbConfig) ) {
            throw new \Exception("加载数据库配置文件失败 !");
        }

        return [
            'dsn' => $dbConfig['DB_TYPE'].':host='.$dbConfig['DB_HOST'].';dbname='.$dbConfig['DB_NAME'].';charset=UTF8',
            'username' => $dbConfig['DB_USERNAME'],
            'password' => $dbConfig['DB_PASSWD'],
            'table_prefix' => $dbConfig['DB_TABLE_PREFIX'],
        ];
    }
}
