<?php

$namespace = require '../vendor/autoload.php';

$namespace->addPsr4('', dirname(__DIR__));

$app = new MQFramework\Application;

$app->singleton(MQFramework\Http::class);  //绑定至容器

return $app;
