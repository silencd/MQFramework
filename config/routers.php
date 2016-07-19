<?php
/**
 *  写法：
 *  		模块[控制器]/方法 => 模块/控制器名 自动匹配类中方法
 *  		或者 模块[控制器]/方法/参数1/参数1值/参数2/参数2值
 *  		仅支持四个参数的pathinfo模式，不支持传统的 [?action=value&action=value] 形式
 *  URI：
 *  		/user/login => User/LoginController => App/Controller/User/LoginController.php
 *
 *  	Controller写法
 *  		[http请求操作类型+方法名] 如 getLogin()
 */
return [
	'index/index' => 'Index/IndexController',
	'index/login' => 'Index/IndexController',
];
