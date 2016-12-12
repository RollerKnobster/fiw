<?php

namespace FIW\Models;

use \Slim\Slim;
use ORM;

class ServicesModel
{

	public function getAll()
	{
		$data = [];
		$dataFromDB = ORM::for_table('services')->find_array();

		foreach($dataFromDB as $item) {
			$item['image'] = $this->checkPic($item['slug']);
			$data[] = $item;
		}

		return $data;
	}

	private function checkPic($slug){
		$file = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'image', 'services', 'description', $slug.'pic.png']);

		if (is_file($file))
			return 'image/services/description/'.$slug.'pic.png';
		return '';
	}
}
