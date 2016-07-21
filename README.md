# MQFramework
A Simple PHP Framework to Learn MVC, Just a Demo or Maybe :)

Author:uShell

##Require
* Twig (PHP Template Engine)

## Document
The Framwork is so simple to use, Look some examples

### Create user controller
```php
<?php
namespace App\Controller;

use MQFramework\Controller\Controller;

class UserController extends Controller
{
	public function getLogin() {
    	$this->assign(['title' => 'userlogin']);
        $this->display('user.login');
    }
}
````
If you notice the function <code>getLogin()</code>name, it's different from other framwork.The function name is <code>GET or POST</code> + <code>function Name</code> when you post some data forward to the function you want
```php
public function postLogin($request) {
	$name = $request['username'];
    $passwd = $request['password'];
    ...
}
//$request variable already contains the form fileds
```
Now the framework just support == get/post == http method

### Create user model
```php
<?php
namespace App\Model;

use MQFramework\Database\Model;

class User extends Model
{
	proteced $table = 'users';
	public funciton getUserList() {
    	$list = $this->where(['id', '>', '30'])->get();
    }
}
//if you not set $table variable, framework can resolve the model class to set database table(Just so simple ORM :)
```
Example:
```php
<?php 
namespace App\Controller;

use MQFramework\Controller\Controller;
use MQFramework\Database\Db;
use App\Model\User;

class User extends Controller
{

	public funciton demo(){
        $db = new Db;
		$db->table('users')->where(['name', '=', 'admin'])->get();
        $db->table('users')->where(['name' => 'admin'])->delete();
        $db->table('users')->where('id = 3')->data(['name' => 'root'])->update();
		$db->table('users')->save(['name' => 'admin', 'passwd' => md5(123)]);
		$db->table('users')->select('name')->where('id>3')->order('id')->limit(10)->get();
    }
}
```
---
### Use Twig Template Engine
* [Twig Document](http://twig.sensiolabs.org/doc/templates.html)
* template configure file in <code>config/template.php</code>
>   'engine' => 'MQFramework\Template\TwigEngine', //default template engine
>    'debug' => false,
>    'path' => 'views/', //default templates storage path
>    'cache' => false, //cache is disable in development mode
>    'cache_path' => 'storages/views/',
>    'tpl_suffix' => ['.tpl.php', '.php'],
* you can use other template engine, use <code>engine =>'MQFramework\Template\BladeEngine'</code> and it must be class name implements <code>EngineContract</code> class

Look this example
* Create User login template file 
   $ vim views/user/login.tpl.php
   
* Write some code
```php	
    {{ name }}
    {% if data | length > 0%}
    	{{data.username}}
    {% endif %}
```
* In the controller
```php
	$this->display('user.login');
```
---
### Use Config Helper

* config files default path config/*.php or config/database/*.php
* must follow the format
```php
	<?php return ['name' => 'xxoo];
```

Example:

```php
<?php
namespace App\Controller;
use MQFramework\Controller\Controller;
use MQFramework\Helper\Config;
class User extends Controller
{
	public function index(){
    	$database = Config::get('config.database'); //config/database.php
        //or Config::get('config.database.mysql'); //config/database/mysql.php
    	if ($database !== false) {
        	$db_host = $database['host'];
            ...
        }
    }
}
```
