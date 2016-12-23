<?php

namespace FIW\Middleware;

use Slim\Middleware;
use FIW\Models\UserModel;

class Auth extends Middleware
{

	public function call()
	{
		$current_route = $this->app->request()->getResourceUri();
		$login_route = $this->app->urlFor('admin_login_page');

		if (strpos($current_route, '/admin') === 0) {
			if ((new UserModel)->isAuthenticated() == false && $current_route != $login_route)
				$this->app->redirect($login_route);
		}

		$this->next->call();
	}

}
