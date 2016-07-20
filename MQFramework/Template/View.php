<?php
namespace MQFramework\Template;

use MQFramework\Application;

class View
{
    /*views storage document*/
    private $tplPath = '/views/';

    /*views cache storage document*/
    private $tplCachePath = '/storages/views';

    /*template file type support*/
    protected $tplType = ['.tpl.php', '.php'];

    /*twig instance*/
    private $twigLoader = null;

    /*template assign data*/
    private $viewData = [];

    private $isCache = false;

    private $debug = false;

    public function __construct()
    {
        $this->setAbsolutePath();
        $this->twigLoader = $this->getInstance();
    }

    private function getInstance()
    {
        if ( is_null($this->twigLoader) ) {
            $this->twigLoader = new \Twig_Loader_Filesystem($this->tplPath);
        }
        return $this->twigLoader;
    }

    protected function setAbsolutePath()
    {
        $app = new Application;
        $basePath = $app->getBasePath();
        $this->tplPath = $basePath.$this->tplPath;
        $this->tplCachePath = $basePath.$this->tplCachePath;
    }

    public function render($data = [])
    {
        $this->viewData = array_merge($this->viewData, $data);
    }

    public function display($tpl)
    {
        $twigInstance = $this->getTplEngine();
        $template = $this->parseTpl($tpl);
        if ( $template['useTplEngine'] ) {
            return $twigInstance->render($template['path'], $this->viewData);
        } else {
            //TODO
        }
    }
    protected function getTplEngine()
    {
        $opts = [];
        if ($this->isCache) {
            if (! is_dir($this->tplCachePath) ) {
                mkdir($this->tplCachePath, 0755);
            }
            $opts['cache'] = $this->tplCachePath;
        }
        if ($this->isDebug) {
            $opts['debug'] = true;
        }
        return new \Twig_Environment($this->twigLoader, $opts);
    }
    protected function parseTpl($tplName)
    {
        if ( strpos($tplName, '.') >0) {
            $tplName = str_replace('.', '/', $tplName);
        }
        //template type is [.tpl.php]
        $tpl = $this->tplPath.$tplName;
        if (file_exists($tpl.$this->tplType[0])) {
            return ['path' => $tplName.$this->tplType[0], 'useTplEngine' => true];
        }
        //template type is [.php , not use template engine]
        if (file_exists($tpl.$this->tplType[1])) {
            return ['path' => $tplName.$this->tplType[1], 'useTplEngine' => false];
        }

        throw new \Exception("Template file[$tpl] not exists !");
    }

    public function setCache()
    {
        $this->isCache = true;
    }
    public function isDebug()
    {
        $this->debug = true;
    }
}
