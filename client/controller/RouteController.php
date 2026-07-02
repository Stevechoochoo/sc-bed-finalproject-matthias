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
            self::showView('login', ['success' => 'Login succeeded!']);
        } else {
            self::showView('login', ['error' => 'Login Failed. Please try again.']);
        }
    }
}