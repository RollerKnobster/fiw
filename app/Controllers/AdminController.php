<?php

namespace FIW\Controllers;

use Exception;
use Slim\Slim;
use FIW\Models\TextModel;
use FIW\Models\SliderModel;
use FIW\Models\PortfolioModel;


class AdminController
{
	protected $app;

	public function __construct()
	{
		$this->app = Slim::getInstance();
	}

	/**
	 *
	 * Контроллер сторінки адмінки "Головна"
	 *
	 */
	public function indexAction()
	{
		return $this->app->render('admin.main.html.twig',[
			'social' => (new TextModel)->getSocial(),
			'slides' => (new SliderModel)->listSlides(),
			'active_page' => 'main'
		]);
	}

	/**
	 *
	 * Контроллер видалення слайду з головного слайдера
	 *
	 */
	public function removeMainSlideAction()
	{
		$this->app->response->headers->set('Content-Type', 'application/json');
		if (!$this->app->request->isAjax())
			return $this->app->response->write('{}');
		$filename = (string)$this->app->request->post('filename');
		$filename = str_replace(['/', '\\'], '', $filename);
		$slide = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'image', 'header', 'slider', $filename]);
		if (!is_file($slide))
			return $this->app->response->write('{}');
		@unlink($slide);
		return $this->app->response->write('{"success": true}');
	}

	/**
	 *
	 * Контроллер завантаження фото в головний слайдер
	 *
	 */
	public function uploadMainSlideAction()
	{
		$this->app->response->headers->set('Content-Type', 'application/json');
		if (!$this->app->request->isAjax())
			return $this->app->response->write('{}');
		if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0)
			return $this->app->response->write('{}');
		$img_file = (new SliderModel)->saveSlide($_FILES['image']['tmp_name']);
		if ($img_file)
			return $this->app->response->write('{"success": true, "img": "'.$img_file.'"}');
		return $this->app->response->write('{}');
	}

	/**
	 *
	 * Контроллер збереження соціальних мереж
	 *
	 */
	public function socialSaveAction()
	{
		$this->app->response->headers->set('Content-Type', 'application/json');
		if (!$this->app->request->isAjax())
			return $this->app->response->write('{}');
		$data = (new TextModel)->setSocial($this->app->request->post('social'));
		if (count(array_diff($data, $this->app->request->post('social'))) == 0);
			return $this->app->response->write('{"success": true}');
		return $this->app->response->write('{}');
	}

	/**
	 *
	 * Контроллер сторінки адмінки "Портфоліо"
	 *
	 */
	public function portfolioAction()
	{
		return $this->app->render('admin.portfolio.html.twig',[
			'projects' => (new PortfolioModel)->showAll(true),
			'active_page' => 'portfolio'
		]);
	}

	/**
	 *
	 * Контроллер збереження портфоліо
	 *
	 */
	public function portfolioSaveAction()
	{
		$this->app->response->headers->set('Content-Type', 'application/json');
		if (!$this->app->request->isAjax())
			return $this->app->response->write('{}');

		$portfolio = new PortfolioModel;
		$data = $portfolio->save($this->app->request->post());

		if (!count($data))
			return $this->app->response->write('{}');

		return $this->app->response->write(json_encode(['success'=>true, 'data'=>$data]));
	}

	/**
	 *
	 * Контроллер збереження фоток портфоліо
	 *
	 */
	public function portfolioPhotoAddAction()
	{
		$this->app->response->headers->set('Content-Type', 'application/json');
		if (!$this->app->request->isAjax())
			return $this->app->response->write('{}');

		$portfolio = new PortfolioModel;
		$data = $portfolio->uploadPhoto($this->app->request->post('portfolio_id'));

		return $this->app->response->write(json_encode(['success'=>true, 'data'=>$data]));
	}

	/**
	 *
	 * Контроллер видалення фоток портфоліо
	 *
	 */
	public function portfolioPhotoRemoveAction()
	{
		$this->app->response->headers->set('Content-Type', 'application/json');
		if (!$this->app->request->isAjax())
			return $this->app->response->write('{}');

		$portfolio = new PortfolioModel;
		$data = $portfolio->removePhoto($this->app->request->post('portfolio_id'), $this->app->request->post('filename'));

		return $this->app->response->write(json_encode(['success'=>$data]));
	}

	/**
	 *
	 * Контроллер отримання даних про одну роботу за токеном
	 *
	 */
	public function portfolioGetOneAction()
	{
		$this->app->response->headers->set('Content-Type', 'application/json');
		if (!$this->app->request->isAjax())
			return $this->app->response->write('{}');

		$portfolio = new PortfolioModel;
		$data = $portfolio->findByToken($this->app->request->post('token'));

		if (!count($data))
			return $this->app->response->write('{}');

		return $this->app->response->write(json_encode(['success'=>true, 'data'=>$data]));
	}

	/**
	 *
	 * Контроллер видалення портфоліо
	 *
	 */
	public function portfolioRemoveAction()
	{
		$this->app->response->headers->set('Content-Type', 'application/json');
		if (!$this->app->request->isAjax())
			return $this->app->response->write('{}');

		$portfolio = new PortfolioModel;
		$data = $portfolio->remove($this->app->request->post('portfolio_id'));

		return $this->app->response->write(json_encode(['success'=>$data]));
	}


}