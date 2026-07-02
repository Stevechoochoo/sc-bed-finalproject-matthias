<?php
namespace com\icemalta\kahuna\client;

use com\icemalta\kahuna\client\controller\RouteController;
use com\icemalta\kahuna\client\helper\ApiHelper;

require 'controller/Controller.php';
require 'controller/UserController.php';
require 'controller/AuthController.php';
require 'controller/RouteController.php';
require 'helper/ApiHelper.php';

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$requestData = ApiHelper::getRequestData();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/action/register') {
	RouteController::actionRegister([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/action/login') {
	RouteController::actionLogin([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $path === '/login') {
	RouteController::viewLogin([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && ($path === '/' || $path === '/register')) {
	RouteController::viewRegister([], $requestData);
} else {
	header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

