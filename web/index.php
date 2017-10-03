<?php

define('ROOT_DIR', __DIR__);
define('APP_DIR', dirname(__DIR__));

require_once APP_DIR.'/vendor/autoload.php';
require_once APP_DIR.'/app/config.php';

session_start();

use Slim\Slim;
use FIW\Middleware\Auth;

/* config ORM */
/*
ORM::configure('mysql:host=localhost;dbname='.DB_NAME);
ORM::configure('username', DB_USER);
ORM::configure('password', DB_PASSWORD);
*/
ORM::configure('mysql:host=db19.freehost.com.ua;dbname=fiw_main');
ORM::configure('username', 'fiw_main');
ORM::configure('password', '6SBuIys9G');


ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
ORM::configure('caching', true);
ORM::configure('caching_auto_clear', true);
ORM::configure('logging', DEBUG);

$user_device = new Mobile_Detect();
$is_mobile = ($user_device->isMobile() || $user_device->isTablet() ? true : false);

$app = new Slim([
	'debug' => DEBUG,
	'mode' => APP_MODE,
	'templates.path' => implode(DIRECTORY_SEPARATOR, [APP_DIR, 'app', 'Views']),
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
if ($is_mobile)
	$app->view->getInstance()->getLoader()->addPath(implode(DIRECTORY_SEPARATOR, [APP_DIR, 'app', 'Views', 'mobile']), 'mobile');

$app->add(new Auth);

$app->view->setData(['app'=>$app->container->settings]);
$app->get('/', 'FIW\Controllers\SiteController:indexAction')->name('home');

$app->get('/admin', 'FIW\Controllers\AdminController:indexAction')->name('admin_index');
$app->get('/admin/', 'FIW\Controllers\AdminController:indexAction');
$app->get('/admin/portfolio', 'FIW\Controllers\AdminController:portfolioAction')->name('admin_portfolio');
$app->get('/admin/portfolio/', 'FIW\Controllers\AdminController:portfolioAction');
$app->get('/admin/about', 'FIW\Controllers\AdminController:aboutAction')->name('admin_about');
$app->get('/admin/about/', 'FIW\Controllers\AdminController:aboutAction');
$app->get('/admin/services', 'FIW\Controllers\AdminController:servicesAction')->name('admin_services');
$app->get('/admin/services/', 'FIW\Controllers\AdminController:servicesAction');
$app->get('/admin/contacts', 'FIW\Controllers\AdminController:contactsAction')->name('admin_contacts');
$app->get('/admin/contacts/', 'FIW\Controllers\AdminController:contactsAction');
$app->get('/admin/login', 'FIW\Controllers\AdminController:loginAction')->name('admin_login_page');
$app->get('/admin/login/', 'FIW\Controllers\AdminController:loginAction');
$app->get('/admin/logout', 'FIW\Controllers\AdminController:logoutAction')->name('admin_logout');

$app->post('/admin/main-slider/remove', 'FIW\Controllers\AdminController:removeMainSlideAction')->name('admin_remove_main_slide');
$app->post('/admin/main-slider/upload', 'FIW\Controllers\AdminController:uploadMainSlideAction')->name('admin_upload_main_slide');
$app->post('/admin/social-save', 'FIW\Controllers\AdminController:socialSaveAction')->name('admin_social_save');
$app->post('/admin/portfolio/save', 'FIW\Controllers\AdminController:portfolioSaveAction')->name('admin_save_portfolio');
$app->post('/admin/portfolio/get_one', 'FIW\Controllers\AdminController:portfolioGetOneAction')->name('admin_get_one_portfolio');
$app->post('/admin/portfolio/remove', 'FIW\Controllers\AdminController:portfolioRemoveAction')->name('admin_rm_portfolio');
$app->post('/admin/portfolio/add-photo', 'FIW\Controllers\AdminController:portfolioPhotoAddAction')->name('admin_add_portfolio_photo');
$app->post('/admin/portfolio/remove-photo', 'FIW\Controllers\AdminController:portfolioPhotoRemoveAction')->name('admin_rm_portfolio_photo');
$app->post('/admin/about/save', 'FIW\Controllers\AdminController:aboutSaveAction')->name('admin_about_save');
$app->post('/admin/employer/save', 'FIW\Controllers\AdminController:employerSaveAction')->name('admin_employer_save');
$app->post('/admin/services/save', 'FIW\Controllers\AdminController:servicesSaveAction')->name('admin_services_save');
$app->post('/admin/contacts/save', 'FIW\Controllers\AdminController:contactsSaveAction')->name('admin_contacts_save');
$app->post('/admin/login', 'FIW\Controllers\AdminController:authAction')->name('admin_auth');

$app->run();

?>
