<?php

namespace FIW\Controllers;

use Exception;
use Slim\Slim;
use FIW\Models\PortfolioModel;
use FIW\Models\EmployerModel;
use FIW\Models\TextModel;
use FIW\Models\ServicesModel;

class SiteController {
	protected $app;

	public function __construct(){
		$this->app = Slim::getInstance();
	}

	/**
	 *
	 * Контроллер головної сторінки
	 *
	 */
	public function indexAction(){

		/* Load main big slider pics */
		$main_slider = [];
		$slider_dir = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'image', 'header', 'slider']);
		foreach (scandir($slider_dir) as $img) {
			try{
				getimagesize(implode(DIRECTORY_SEPARATOR, [$slider_dir, $img]));
				$main_slider[] = $img;
			} catch (Exception $e){
				continue;
			}
		}

		/* Render main page */
		return $this->app->render('main.html.twig', [
			'main_slider' => $main_slider,
			'portfolio_list' => (new PortfolioModel)->showAll(),
			'employers_list' => (new EmployerModel)->showAll(),
			'about_us' => (new TextModel)->getText('about-us'),
			'services' => (new ServicesModel)->getAll(),
			'contacts' => (new TextModel)->getContacts(),
			'social' => (new TextModel)->getSocial()
		]);
	}

}
