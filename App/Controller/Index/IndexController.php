<?php
namespace App\Controller\Index;

use MQFramework\Controller\Controller;
use App\Model\User;
use MQFramework\Database\Db;

class IndexController extends Controller
{
	public function getLogin() {
		$userModel = new User;
		$ret = $userModel->getUserList();
		
		// $db = new Db;
		// $ret = $db->table('users')->where(['name', '=', 'root'])->get();
		return "登陆：".var_dump($ret);
	}
	public function postLogin($param) {
		var_dump($param);
		if (isset($param['username']) && isset($param['passwd'])) {
			echo "<p>用户名: ".$param['username'];
			echo "<p>密码：".$param['passwd'];
		}
	}

	public function getIndex() {
		return "<p>MQFramework框架：）";
	}
}