<?php
namespace com\icemalta\jobapp\client\controller;

class RouteController extends Controller
{
    public static function showView(string $view, ?array $params = []): void {
        include __DIR__ . "/../templates/$view.twig";
    }

    public static function viewRegister(array $params, array $data): void
    {
        self::showView('register', $params);
    }

    public static function actionRegister(array $params, array $data): void
    {
        $result = UserController::register($params, $data);
        if (isset($result->data)) {
            self::showView('register', ['success' => 'Registration succeeded!']);
        } else {
            self::showView('register', ['error' => 'Registration failed. Please try again.']);
        }
    }
}