<?php

define('ROOT_DIR', __DIR__);
define('APP_DIR', dirname(__DIR__));

require_once APP_DIR.'/vendor/autoload.php';
require_once APP_DIR.'/app/config.php';

session_start();

use Slim\Slim;

/* config ORM */
ORM::configure('mysql:host=localhost;dbname='.DB_NAME);
ORM::configure('username', DB_USER);
ORM::configure('password', DB_PASSWORD);
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
ORM::configure('caching', true);
ORM::configure('caching_auto_clear', true);
ORM::configure('logging', DEBUG);

$user_device = new Mobile_Detect();
$is_mobile = ($user_device->isMobile() || $user_device->isTablet() ? true : false);

$app = new \Slim\Slim([
	'debug' => DEBUG,
	'mode' => APP_MODE,
	'templates.path' => '../app/views'.($is_mobile ? '/mobile' : ''),
	'site_name' => 'Forma Interior Workshop',
	'is_mobile' => $is_mobile
	]);

$app->view = new \Slim\Views\Twig();
$app->view->setTemplatesDirectory($app->container->settings['templates.path']);
$app->view->parserOptions['debug'] = $app->container->settings['debug'];
$app->view->parserExtensions = array(
	new \Slim\Views\TwigExtension(),
	new \Twig_Extension_Debug(),
);

function check_login() {
	$app = \Slim\Slim::getInstance();
	if (!count($_SESSION) || !$_SESSION['user'] || !$_SESSION['token'])
		$app->redirect('/admin/login');
	$app->config('user', $_SESSION['user']);
};

$app->view->setData(['app'=>$app->container->settings]);

$app->get('/', 'FIW\Controllers\SiteController:indexAction')->name('home');

$app->run();
