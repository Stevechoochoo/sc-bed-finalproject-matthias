<?php
namespace com\icemalta\kahuna\client;

use com\icemalta\kahuna\client\controller\RouteController;
use com\icemalta\kahuna\client\helper\ApiHelper;

require 'controller/Controller.php';
require 'controller/UserController.php';
require 'controller/AuthController.php';
require 'controller/ProductController.php';
require 'controller/RouteController.php';
require 'helper/ApiHelper.php';

session_start();

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$requestData = ApiHelper::getRequestData();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/action/register') {
	RouteController::actionRegister([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/action/login') {
	RouteController::actionLogin([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/action/product') {
	RouteController::actionProduct([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/action/product-details') {
	RouteController::actionProductDetails([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $path === '/login') {
	RouteController::viewLogin([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $path === '/logout') {
	RouteController::actionLogout([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $path === '/product') {
	RouteController::viewProduct([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $path === '/products') {
	RouteController::viewProducts([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $path === '/product-details') {
	RouteController::viewProductDetails([], $requestData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && ($path === '/' || $path === '/register')) {
	RouteController::viewRegister([], $requestData);
} else {
	header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

