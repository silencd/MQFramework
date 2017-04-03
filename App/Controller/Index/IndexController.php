<?php
namespace App\Controller\Index;

use MQFramework\Controller\Controller;
use App\Model\User;
use MQFramework\Database\Db;
use MQFramework\Helper\Config;
use App\Exception\Handler as LogicException;

use App\Service\SessionService;

class IndexController extends Controller
{
	public function getLogin() {
                            $session = new \App\Service\SessionService;
                            $_SESSION['name'] = 'xxxx';
     
                            // var_dump(error_get_last());
		// $userModel = new User;
		// $data = $userModel->getUserList();
		// $db = new Db;
                            // $condition = ['name', '=', 'u_1232'];
  //                           $c = 'username="u_1232"';
  //                           $hash = md5($c);

  //                           $a = microtime(true);
  //                           for($i=0; $i<15000; $i++) {
  //                               $ret[] = $db->table('users')->where($c)->get();
  //                           }
		// $b = microtime(true);
  //                           echo 'MySQL:    '.($b-$a).PHP_EOL;

                            // try{
                            //     $a = microtime(true);
                            //     for($i=0; $i<100000; $i++) {
                            //         $data['username'] = 'u_'.$i;
                            //         $data['passwd'] = md5(time());
                            //          $db->table('users')->save($data);
                            //     }
                            //     $b = microtime(true);
                            //     echo 'MySQL:    '.($b-$a).PHP_EOL;
                            // } catch (Exception $e) {
                            //     var_dump($e->getMessage());die;
                            // }
                           
                            // try {
                            //     throw new \Exception('WebAPINotifyTest');
                            // } catch (\Exception $e) {
                            //     $logic = new LogicException;
                            //     $logic->report($e);
                            // }
                            
                            // $memcache = new \App\Service\MemcacheService;

                            // $ret = $memcache->getServerStatus(); var_dump($ret);
                            // $c = microtime(true);
                            // for($i=0; $i<15000; $i++) {
                            //     // $k = 'm_'.$i;
                            //     // $v = md5(time());
                            //     // $memcache->set($k, $v, 100);
                            //     $memcache->get($hash);
                            // }
                            // $d = microtime(true);
                            // echo 'Memcache: '.($d - $c); //time:3.1044709682465
                    

		// $this->assign(['data' => $data]);
		// $this->assign(['name' => 'xxoo']);
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
                            $session = new SessionService;
                            // $session->add('user', 'admin');
                            // $session->get('user');
                            // $session->delete('user');
                            
		return "<center><div style='margin-top:20px;'>Welcome to MQFramework：）</div></center>";
	}
}
