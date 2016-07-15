<?php
namespace App\Controller\Index;

use MQFramework\Controller\Controller;

class IndexController extends Controller
{
	public function getLogin() {
		return "登陆：";
	}
	public function postLogin($param) {
		var_dump($param);
		if (isset($param['username']) && isset($param['passwd'])) {
			echo "<p>用户名: ".$param['username'];
			echo "<p>密码：".$param['passwd'];
		}
	}
	public function postMail() { echo "Post Mail"; }

	public function getIndex() {
		return "<p>MQFramework框架：）";
	}
}