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

	public function save($post_data)
	{
		if (!is_array($post_data))
			return [];

		if (array_diff(['id', 'price', 'description'], array_keys($post_data)))
			return [];

		$id = intval($post_data['id']);
		$service = ORM::for_table('services')->find_one($id);

		unset($post_data['id']);

		if (!$service) {
			return [];
		}

		$service->set($post_data);
		$service->save();

		return $service->asArray();
	}

	private function checkPic($slug)
	{
		$file = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'image', 'services', 'description', $slug.'pic.png']);

		if (is_file($file))
			return 'image/services/description/'.$slug.'pic.png';
		return '';
	}
}
