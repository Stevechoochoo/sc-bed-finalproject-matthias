<?php
namespace com\icemalta\kahuna\client\controller;

class RouteController extends Controller
{
    public static function showView(string $view, ?array $params = []): void {
        include __DIR__ . "/../templates/$view.twig";
    }

    public static function viewRegister(array $params, array $data): void
    {
        self::showView('register', $params);
    }

    public static function viewLogin(array $params, array $data): void
    {
        self::showView('login', $params);
    }

    public static function viewProduct(array $params, array $data): void
    {
        if (isset($_SESSION['api_user'], $_SESSION['api_token'])) {
            $products = ProductController::getAll($params, $data);
            if (isset($products->data)) {
                $params['products'] = $products->data;
            }
            self::showView('product', $params);
        } else {
            self::showView('login', ['error' => 'Please login first.']);
        }
    }

    public static function viewProducts(array $params, array $data): void
    {
        if (isset($_SESSION['api_user'], $_SESSION['api_token'])) {
            $products = ProductController::getRegistered($params, $data);
            if (isset($products->data)) {
                $params['products'] = $products->data;
            }
            self::showView('products', $params);
        } else {
            self::showView('login', ['error' => 'Please login first.']);
        }
    }

    public static function actionRegister(array $params, array $data): void
    {
        $result = UserController::register($params, $data);
        if (isset($result->data)) {
            self::showView('login', ['success' => 'Registration succeeded! You can now login.']);
        } else {
            self::showView('register', ['error' => 'Registration failed. Please try again.']);
        }
    }

    public static function actionLogin(array $params, array $data): void
    {
        $result = AuthController::login($params, $data);
        if (isset($result->data)) {
            $_SESSION['api_user'] = $result->data->user;
            $_SESSION['api_token'] = $result->data->token;
            self::viewProduct(['success' => 'Login succeeded!'], $data);
        } else {
            self::showView('login', ['error' => 'Login Failed. Please try again.']);
        }
    }

    public static function actionProduct(array $params, array $data): void
    {
        if (isset($_SESSION['api_user'], $_SESSION['api_token'])) {
            $result = ProductController::register($params, $data);
            if (isset($result->data)) {
                self::viewProduct(['success' => 'Product registered successfully.'], $data);
            } else {
                self::viewProduct(['error' => 'Product registration failed. Please try again.'], $data);
            }
        } else {
            self::showView('login', ['error' => 'Please login first.']);
        }
    }
}