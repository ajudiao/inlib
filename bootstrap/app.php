<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Anderdev\Inlib\core\Router;
use Anderdev\Inlib\controllers\HomeController;
use Anderdev\Inlib\controllers\admin\AdminController;

$router = new Router();

$router->get('/', HomeController::class . '@index');
$router->get('/sobre', HomeController::class . '@sobre');
$router->get('/contato', HomeController::class . '@contato');
$router->get('/livros', HomeController::class . '@livros');

$router->get('/admin', AdminController::class . '@dashboard');
$router->get('/admin/livros', AdminController::class . '@livros');

return $router;
