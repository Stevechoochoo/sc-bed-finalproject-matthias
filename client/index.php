<?php
namespace com\icemalta\jobapp\client;

use com\icemalta\jobapp\client\controller\RouteController;
use com\icemalta\jobapp\client\helper\ApiHelper;

require 'controller/Controller.php';
require 'controller/UserController.php';
require 'controller/RouteController.php';
require 'helper/ApiHelper.php';

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$requestData = ApiHelper::getRequestData();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/action/register') {
	RouteController::actionRegister([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && ($path === '/' || $path === '/register')) {
	RouteController::viewRegister([], $requestData);
} else {
	header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

