<?php
 
    $app = require '../MQFramework/Bootstrap.php';

    $http = $app->make(MQFramework\Http::class); //获取Http实例

    $response  = $http->handle(
        $request = $http->request()
    );
  
    $response->send();
