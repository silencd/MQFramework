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
    private $twig = null;
    /*template assign data*/
    private $viewData = [];

    public function __construct()
    {
        $this->setAbsolutePath();
        $this->twig = $this->getInstance();
    }

    private function getInstance()
    {
        if ( is_null($this->twig) ) {
            $this->twig = new \Twig_Loader_Filesystem($this->tplPath);
        }
        return $this->twig;
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
        $twigInstance = new \Twig_Environment($this->twig, [
            // 'debug' => true,
            // 'cache' => $this->tplCachePath,
        ]);
        $tpl = $this->parseTpl($tpl);
        if ( $tpl['tpl'] ) {
            return $twigInstance->render($tpl['name'], $this->viewData);
        } else {
            //TODO
        }
    }

    protected function parseTpl($tplName)
    {
        if ( strpos($tplName, '.') >0) {
            $tplName = str_replace('.', '/', $tplName);
        }
        //template type is [.tpl.php]
        $tpl = $this->tplPath.$tplName;
        if (file_exists($tpl.$this->tplType[0])) {
            return ['name' => $tplName.$this->tplType[0], 'tpl' => true];
        }
        //template type is [.php , not use template engine]
        if (file_exists($tpl.$this->tplType[1])) {
            return ['name' => $tplName.$this->tplType[1], 'tpl' => false];
        }

        throw new \Exception("Template file[$tpl] not exists !");
    }
}
