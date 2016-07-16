<?php
namespace App\Model;

use MQFramework\Database\Model;

class User extends Model
{
	// protected $table = ''; //ORM

	public function getUserList() {
		return $this->where(['name', '=', 'root'])->get();
	}
}