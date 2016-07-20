<?php
namespace MQFramework\Helper;

use MQFramework\Application;

class Config
{
    protected static $defaultPath = 'config';

    protected $item;

    private static $basePath = null;

    // public function __construct($item)
    // {
    //     $this->item = $item;
    // }

    public static function get($key)
    {
        $config = self::getConfigFile($key);
        if ( is_array($config) ) {
            return $config;
        }
        return false;
    }

    public static function set($data = [])
    {
        //TODO
    }

    public static function pop($key)
    {
        //TODO
    }

    private static function getConfigFile($file)
    {
        if ( is_string($file) && strpos($file, '.') > 0) {
            $arr = explode('.', $file);
            if ($arr[0] !== self::$defaultPath) {
                /*If you not use default "config." prefix , there will append*/
                if ( count($arr) == 2 && !empty($arr[1]) ) {
                    $fileName = self::setBasePath().'/'.self::$defaultPath.'/'.$arr[0].'/'.$arr[1].'.php';
                } else {
                    throw new \Exception("Config File[$file] Format error !");
                }
            } else {
                $file = str_replace('.', '/', $file);
                $fileName = self::setBasePath().'/'.$file.'.php';
            }

            if ( file_exists($fileName) ) {
                $config = require $fileName;
                return $config;
            }
            throw new \Exception("Config File[$fileName] not exists !");
        }
    }

    private static function setBasePath()
    {
        if ( is_null(self::$basePath) ) {
            $app = new Application;
            return self::$basePath = $app->getBasePath();
        }
        return self::$basePath;
    }
}
