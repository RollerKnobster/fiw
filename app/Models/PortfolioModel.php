<?php

namespace FIW\Models;

use Slim\Slim;
use ORM;

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
			return [0];

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

}
