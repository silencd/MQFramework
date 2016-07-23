<?php
namespace MQFramework\Template;

use MQFramework\Application;
use MQFramework\Helper\Config;

class TwigEngine implements EngineContract
{
    private $tplPath;
    private $suffix = [];
    private $viewData = [];
    private $tplCachePath;
    private $isDebug = false;
    private $isCache = false;
    private $instance = null;
    private $engine = null;

    public function __construct()
    {
        //set environment
        $this->setEnv();
    }
    public function setEnv()
    {
        $app = new Application;
        $basePath = $app->getBasePath();

        $config = Config::get('config.template');

        $this->tplPath = $basePath.'/'.$config['path'];
        $this->tplCachePath = $basePath.'/'.$config['cache_path'];
        $this->suffix = $config['tpl_suffix'];

        if ($cache = $this->setCache($config) ) {
            $config['cache'] = $cache;
        } else {
            unset($config['cache']);
        }
        $this->engine = $this->getInstance();
        $this->instance = $this->setEngine($config);
    }

    private function getInstance()
    {
        if ( is_null($this->engine) ) {
            $this->engine = new \Twig_Loader_Filesystem($this->tplPath);
        }
        return $this->engine;
    }

    private function setEngine($opts)
    {
        return new \Twig_Environment($this->engine, $opts);
    }

    private function setCache($config)
    {
        if ( $config['cache'] ) {
            if (! is_dir($this->tplCachePath) ) {
                mkdir($this->tplCachePath, 0755);
            }
            return $this->tplCachePath;
        }
    }
    private function parseTpl($tplName)
    {
        if ( strpos($tplName, '.') >0) {
            $tplName = str_replace('.', '/', $tplName);
        }
        //default emplate type is [.tpl.php]
        $tpl = $this->tplPath.$tplName;

        if (file_exists($tpl.$this->suffix[0])) {
            return ['path' => $tplName.$this->suffix[0], 'useTplEngine' => true];
        }

        //template type is [.php , not use template engine]
        if (file_exists($tpl.$this->suffix[1])) {
            return ['path' => $tplName.$this->suffix[1], 'useTplEngine' => false];
        }

        throw new \Exception("Template file [`$tpl`] not exists !");
    }

    public function render($data = [])
    {
        $this->viewData = array_merge($this->viewData, $data);
    }

    public function display($tpl)
    {
        $template = $this->parseTpl($tpl);
        if ( $template['useTplEngine'] ) {
            return $this->instance->render($template['path'], $this->viewData);
        } else {
            //TODO
        }
    }
}
