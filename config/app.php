<?php
return [
    'debug' => true,
    'log_path' => 'storages/logs/',
    'session_driver' => 'redis',
    'session_prefix' => '',
    'session_expire' => '3600',
    'redis' => [
        'host' => '127.0.0.1',
        'port' => '6379',
        'auth' => ''
    ]
];
