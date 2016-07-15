<?php
namespace Base\Database;

class Connector
{
    public $config = [];

    public function __construct()
    {
        $this->config = $this->getConfig();
    }
    public function getConfig()
    {
        if ( empty( $this->config ) ) {
            $rootPath = dirname(dirname(__DIR__));
            $dbConfig = require $rootPath.'/Config/Database.php';
            if ( ! is_array($dbConfig) ) {
                throw new Exception("Database Configure get error !");
            }
            return [
                'dsn' => $dbConfig['DB_TYPE'].':host='.$dbConfig['DB_HOST'].';dbname='.$dbConfig['DB_NAME'].';charset=UTF8',
                'username' => $dbConfig['DB_USERNAME'],
                'password' => $dbConfig['DB_PASSWD'],
                'table_prefix' => $dbConfig['DB_TABLE_PREFIX'],
            ];
        }
        return $this->config;
    }
}
