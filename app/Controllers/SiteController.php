<?php

namespace FIW\Controllers;

use Exception;
use Slim\Slim;
use FIW\Models\PortfolioModel;
use FIW\Models\EmployerModel;
use FIW\Models\TextModel;
use FIW\Models\ServicesModel;
use FIW\Models\SliderModel;

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

		return $this->app->render('main.html.twig', [
			'main_slider' => (new SliderModel)->listSlides(),
			'portfolio_list' => (new PortfolioModel)->showAll(),
			'employers_list' => (new EmployerModel)->showAll(),
			'about_us' => (new TextModel)->getText('about-us'),
			'services' => (new ServicesModel)->getAll(),
			'contacts' => (new TextModel)->getContacts(),
			'social' => (new TextModel)->getSocial()
		]);

	}

}
