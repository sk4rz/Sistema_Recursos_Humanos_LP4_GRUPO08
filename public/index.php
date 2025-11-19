<?php
/**
 * Punto de Entrada Principal
 */

session_start();

require_once dirname(__DIR__) . '/config/config.php';

require_once APP_PATH . '/core/Autoloader.php';
Autoloader::register();

require_once APP_PATH . '/core/Session.php';
Session::start();

require_once APP_PATH . '/core/Router.php';

$router = new Router();
require_once APP_PATH . '/routes/web.php';

$router->dispatch();

