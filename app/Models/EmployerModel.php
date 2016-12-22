<?php

namespace FIW\Models;

use \Slim\Slim;
use ORM;
use upload;

class EmployerModel
{
	protected $app;

	public function __construct()
	{
		$this->app = Slim::getInstance();
	}

	public function showAll()
	{
		$employers_list = ORM::for_table('employers')->order_by_asc('id')->find_array();

		foreach ($employers_list as $key=>$employer) {
			$f_path = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'employers', $employer['id'], $employer['photo']]);

			if (is_file($f_path))
				$employers_list[$key]['photo'] = '/uploads/employers/'.$employer['id'].'/'.$employer['photo'];
			else
				$employers_list[$key]['photo'] = '';
		}

		return $employers_list;
	}

	public function save($post_data)
	{
		if (!is_array($post_data))
			return [];

		$remove_old_pic = false;
		if (isset($post_data['remove_pic'])) {
			$remove_old_pic = true;
			unset($post_data['remove_pic']);
		}

		if (array_diff(['id', 'position', 'name', 'about'], array_keys($post_data)))
			return [];

		$id = intval($post_data['id']);
		$employer = ORM::for_table('employers')->find_one($id);

		unset($post_data['id']);

		if (!$employer) {
			$employer = ORM::for_table('employers')->create();
			$employer->photo = 'avatar.png';
		}

		$employer->set($post_data);
		$employer->save();

		$f_path = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'employers', $employer->id]);

		if (!is_dir($f_path)) {
			@mkdir($f_path);
			@chmod($f_path, 0755);
		}

		$file = implode(DIRECTORY_SEPARATOR, [$f_path, 'avatar.png']);

		if ($remove_old_pic == true) {
			if (is_file($file)) {
				@unlink($file);
			}
		}

		return $employer->asArray();
	}

	public function updatePhoto($employer_id)
	{
		$employer_id = intval($employer_id);
		if ($employer_id < 1)
			return false;

		$employer = ORM::for_table('employers')->find_one($employer_id);
		if (!$employer)
			return false;

		$f_path = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'employers', $employer->id]);

		$img = new upload($_FILES['image']);
		if ($img->uploaded) {
			$f_name = 'avatar';
			$img->file_new_name_body = $f_name;

			$f_name .= '.png';
			$img->image_convert = 'png';
			$img->process($f_path);
			if ($img->processed) {
				$img->clean();
				return true;
			}
		}
		return false;
	}

}
