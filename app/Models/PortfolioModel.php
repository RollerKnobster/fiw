<?php

namespace FIW\Models;

use Slim\Slim;
use ORM;

class PortfolioModel {
	protected $app;

	public function __construct(){
		$this->app = Slim::getInstance();
	}

	public function showAll(){
		$portfolio_list = ORM::for_table('portfolio')
			->order_by_desc('id')
			->find_array();

		$portfolio_images = ORM::for_table('portfolio_images')->find_array();

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
		}
		return $portfolio_list;
	}

}
