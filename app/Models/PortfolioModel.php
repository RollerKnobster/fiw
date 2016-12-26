<?php

namespace FIW\Models;

use Slim\Slim;
use ORM;
use upload;

class PortfolioModel
{
	protected $app;

	public function __construct()
	{
		$this->app = Slim::getInstance();
	}

	public function showAll($admin = false)
	{
		$portfolio_list = ORM::for_table('portfolio')
			->order_by_desc('id')
			->find_array();

		foreach ($portfolio_list as $key => $portfolio) {
			$portfolio_list[$key]['images'] = array_filter(
				array_map(
					function($i) use ($portfolio){
						$f_path = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'portfolio', $portfolio['token'], $i['filename']]);
						if (is_file($f_path))
							return '/uploads/portfolio/'.$portfolio['token'].'/'.$i['filename'];
						else
							return '';
					},
					ORM::for_table('portfolio_images')->where_equal('portfolio_id', $portfolio['id'])->find_array()
					)
				);
			if (!$admin && !count($portfolio_list[$key]['images']))
				unset($portfolio_list[$key]);
		}
		return $portfolio_list;
	}

	public function save($post_data)
	{
		if (!is_array($post_data))
			return [];

		if (array_diff(['id', 'name', 'address', 'price', 'director'], array_keys($post_data)))
			return [];

		$id = intval($post_data['id']);
		$portfolio = ORM::for_table('portfolio')->find_one($id);

		unset($post_data['id']);

		if (!$portfolio) {
			$portfolio = ORM::for_table('portfolio')->create();
			$portfolio->token = sha1($post_data['name'].time());
			$portfolio->created = time();
		}

		$portfolio->set($post_data);
		$portfolio->save();

		return $portfolio->asArray();
	}

	public function findByToken($token)
	{
		$token = preg_replace('/[^a-z0-9]/i', '', $token);
		$portfolio = ORM::for_table('portfolio')->where_equal('token', $token)->find_one()->asArray();
		$portfolio['images'] = array_filter(
			array_map(
				function($i) use ($portfolio){
					$f_path = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'portfolio', $portfolio['token'], $i['filename']]);
					if (is_file($f_path))
						return '/uploads/portfolio/'.$portfolio['token'].'/'.$i['filename'];
					else
						return '';
				},
				ORM::for_table('portfolio_images')->where_equal('portfolio_id', $portfolio['id'])->find_array()
				)
			);
		sort($portfolio['images']);
		return $portfolio;
	}

	public function uploadPhoto($portfolio_id)
	{
		$portfolio_id = intval($portfolio_id);
		if ($portfolio_id < 1)
			return false;

		$portfolio = ORM::for_table('portfolio')->find_one($portfolio_id);
		if (!$portfolio)
			return false;

		$f_path = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'portfolio', $portfolio->token]);

		$img = new upload($_FILES['image']);
		$img->file_overwrite = true;
		if ($img->uploaded) {
			$images = array_map('current', ORM::for_table('portfolio_images')
				->select('filename')
				->where_equal('portfolio_id', $portfolio_id)
				->find_array());
			natsort($images);

			$num = intval(preg_replace('/[^0-9]/', '', end($images)))+1;
			$f_name = 'photo'.$num;
			$img->file_new_name_body = $f_name;

			$f_name .= '.jpg';
			$img->image_convert = 'jpg';
			$img->process($f_path);
			if ($img->processed) {
				$img->clean();
				$i_sql = ORM::for_table('portfolio_images')->create();
				$i_sql->portfolio_id = $portfolio_id;
				$i_sql->filename = $f_name;
				$i_sql->save();
				return '/uploads/portfolio/'.$portfolio->token.'/'.$f_name;
			}
		}
		return false;
	}

	public function removePhoto($portfolio_id, $image)
	{
		$file = ORM::for_table('portfolio_images')
			->select(['portfolio_images.id', 'token'])
			->join('portfolio', ['portfolio.id', '=', 'portfolio_images.portfolio_id'])
			->where_equal('portfolio_id', $portfolio_id)
			->where_equal('filename', $image)
			->find_one();

		if (!$file)
			return false;

		$f_path = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'portfolio', $file->token, $image]);

		if (is_file($f_path))
			@unlink($f_path);

		$file->delete();
		return true;
	}

	public function remove($portfolio_id)
	{
		$portfolio_id = intval($portfolio_id);
		if ($portfolio_id < 1)
			return false;

		$portfolio = ORM::for_table('portfolio')->find_one($portfolio_id);
		if (!$portfolio)
			return false;

		$token = $portfolio->token;
		$portfolio->delete();

		ORM::for_table('portfolio_images')->where_equal('portfolio_id', $portfolio_id)->delete_many();

		$dir = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'uploads', 'portfolio', $token]);

		if (is_dir($dir)) {
			foreach(scandir($dir) as $file) {
				$file = implode(DIRECTORY_SEPARATOR, [$dir, $file]);
				if (is_file($file)) {
					@unlink($file);
				}
			}

			@rmdir($dir);
		}

		return true;
	}

}
