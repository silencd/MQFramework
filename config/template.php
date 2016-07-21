<?php
return [
    'engine' => 'MQFramework\Template\TwigEngine', //default template engine
    'debug' => false,
    'path' => 'views/', //default templates storage path
    'cache' => false, //cache is disable in development mode
    'cache_path' => 'storages/views/',
    'tpl_suffix' => ['.tpl.php', '.php'], //default template file suffix is [.tpl.php]
];
