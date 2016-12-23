<?php

namespace FIW\Models;

use ORM;

class UserModel
{

	public function isAuthenticated()
	{
		return (count($_SESSION) && isset($_SESSION['user']) && is_object($_SESSION['user']));
	}

	public function auth($login, $pass)
	{
		$login = preg_replace('/[^a-z0-9\.\-\_\@]/i', '', $login);
		if (!$login)
			return false;

		$user = ORM::for_table('users')
			->where('login', $login)
			->find_one();

		if (!$user)
			return false;

		$pass = md5(sha1($pass));
		if ($user->password != $pass)
			return false;

		$user->last_in = time();
		$user->token = sha1($user->login.time());
		$user->save();

		$_SESSION['user'] = $user;
		$_SESSION['token'] = $user->token;

		return true;
	}

	public function logout()
	{
		unset($_SESSION['user']);
		unset($_SESSION['token']);
	}
}
