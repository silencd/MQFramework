<?php
namespace App\Controller\Index;

use MQFramework\Controller\Controller;
use App\Model\User;
use MQFramework\Database\Db;
use MQFramework\Helper\Config;

class IndexController extends Controller
{
	public function getLogin() {
		$userModel = new User;
		$data = $userModel->getUserList();

		// $db = new Db;
		// $ret = $db->table('users')->where(['name', '=', 'root'])->get();

		$this->assign(['data' => $data]);
		$this->assign(['name' => 'xxoo']);

		return $this->display('home.index');
	}
	public function postLogin($param) {
		var_dump($param);
		if (isset($param['username']) && isset($param['passwd'])) {
			echo "<p>用户名: ".$param['username'];
			echo "<p>密码：".$param['passwd'];
		}
	}

	public function getIndex() {
		$db = Config::get('config.database.mysql'); var_dump($db);
		return "<center>Welcome to MQFramework：）</center>";
	}
}
