<?php

class AdminController {
	protected $app;
	protected $lang;

	public function __construct(){
		$this->app = i18nSlim::getInstance();
		$this->lang = $this->app->container->settings['lang'];
	}

	/**
	 *
	 * Контроллер сторінки адмінки "Редагування тексту про нас"
	 *
	 */
	public function indexAction(){
		$text = ORM::for_table('texts')
			->select('text')
			->where_equal('lang', $this->lang)
			->where_equal('name', 'about_company')
			->find_one();
		return $this->app->render('admin.main.html.twig', ['text' => $text->text]);
	}

	/**
	 *
	 * Контроллер сторінки адмінки "Авторизація"
	 *
	 */
	public function loginAction(){
		return $this->app->render('admin.login.html.twig');
	}

	/**
	 *
	 * Контроллер авторизації
	 *
	 */
	public function authAction(){
		if ($this->app->request->post('auth_name')){
			$user = ORM::for_table('users')
				->where_any_is([['login' => $this->app->request->post('auth_name')], ['email' => $this->app->request->post('auth_name')]])
				->find_one();
			if ($user && $user->password == sha1(md5($this->app->request->post('auth_password')))){
				$_SESSION['user'] = $user;
				$_SESSION['token'] = sha1(time());
				return $this->app->redirect('/admin');
			}
			$this->app->flashNow('login.error', 'Невірний логін або пароль!');
		}
		return $this->app->render('admin.login.html.twig');
	}

	/**
	 *
	 * Контроллер авторизації
	 *
	 */
	public function logoutAction(){
		$_SESSION['user'] = null;
		unset($_SESSION['token']);
		return $this->app->redirect($this->app->urlFor('admin_login'));
	}

	/**
	 *
	 * Контроллер збереження тексту "Про нас"
	 *
	 */
	public function saveTextAction(){
		$this->app->response->headers->set('Content-Type', 'application/json');

		if ($this->app->request->post('text') === null)
			return $this->app->response->write(json_encode(['error'=>'Заповніть текст']));

		$text = ORM::for_table('Texts')
			->where_equal('lang', $this->lang)
			->where_equal('name', 'about_company')
			->find_one();

		if (!$text){
			$text = ORM::for_table('Texts')->create();
			$text->lang = $this->lang;
		}

		$text->text = $this->app->request->post('text');
		$text->save();

		return $this->app->response->write(json_encode(['error'=>'Дані оновлено.']));
	}

	/**
	 *
	 * Контроллер сторінки адмінки "Каталог портфоліо"
	 *
	 */
	public function listPortfolioAction(){
		/* Get category data */
		$cats = ORM::for_table('category')
			->select('cd.*')
			->select('css_class')
			->join('category_data', ['cd.category_id', '=', 'category.id'], 'cd')
			->where_equal('cd.lang', $this->lang)
			->find_array();

		return $this->app->render('admin.portfolio.html.twig',[
			'cats' => $cats,
			'portfolio_list' => (new PortfolioModel)->displayAll()
			]);
	}

	/**
	 *
	 * Контроллер сторінки адмінки "Вивід одного портфоліо"
	 *
	 */
	public function showPortfolioAction($token){
		$token = preg_replace('/[^a-z0-9]/', '', $token);

		$portfolio = (new PortfolioModel)->displayOne($token);

		if (!$portfolio)
			$this->app->redirect($this->app->urlFor('admin_portfolio'));

		$cats = ORM::for_table('category')
			->select('cd.*')
			->select('css_class')
			->join('category_data', ['cd.category_id', '=', 'category.id'], 'cd')
			->where_equal('cd.lang', $this->lang)
			->find_array();

		$used_categories = array_filter(array_map('current', ORM::for_table('category_portfolio')
			->select('category_id')
			->where_equal('category_portfolio.portfolio_id', $portfolio->portfolio_id)
			->find_array()));

		return $this->app->render('admin.portfolio.html.twig',[
			'cats' => $cats,
			'portfolio' => $portfolio,
			'has_categories' => $used_categories,
			'portfolio_list' => (new PortfolioModel)->displayAll()
			]);
	}

	/**
	 *
	 * Контроллер сторінки адмінки "Каталог працівників"
	 *
	 */
	public function listEmployersAction(){
		$this->app->render('admin.employers.html.twig',[
			'employer_list' => (new EmployerModel)->displayAll()
			]);
	}

	/**
	 *
	 * Контроллер сторінки адмінки "Вивід одного працівника"
	 *
	 */
	public function showEmployerAction($token){
		$token = preg_replace('/[^a-z0-9]/', '', $token);

		$employer = (new EmployerModel)->displayOne($token);

		if (!$employer)
			$this->app->redirect($this->app->urlFor('admin_employers'));

		return $this->app->render('admin.employers.html.twig',[
			'employer' => $employer,
			'employer_list' => (new EmployerModel)->displayAll()
			]);
	}


}