<?php

namespace FIW\Models;

use \Slim\Slim;
use ORM;

class EmployerModel {
	protected $app;

	public function __construct(){
		$this->app = Slim::getInstance();
	}

	public function showAll(){
		$employers_list = ORM::for_table('employers')->order_by_asc('id')->find_array();

		foreach ($employers_list as $key=>$employer){
			$f_path = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'employers', $employer['id'], $employer['photo']]);

			if (is_file($f_path))
				$employers_list[$key]['photo'] = '/uploads/employers/'.$employer['id'].'/'.$employer['photo'];
			else
				$employers_list[$key]['photo'] = '';
		}

		return $employers_list;
	}

}
