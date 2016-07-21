<?php
namespace MQFramework\Template;

interface EngineContract
{
    public function setEnv();

    public function render($data = []);

    public function display($tpl);
}
