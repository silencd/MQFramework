<?php
namespace MQFramework\Template;

use MQFramework\Application;
use MQFramework\Helper\Config;

class View
{
    private $engine = null;

    public function __construct()
    {
        $this->engine = $this->getEngine();
    }
    
    private function getEngine()
    {
        $config = Config::get('config.template');
        $app = new Application;
        return $app->make($config['engine']);
    }

    public function render($data)
    {
        $this->engine->render($data);
    }

    public function display($tpl)
    {
        return $this->engine->display($tpl);
    }
}
