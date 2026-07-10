<?php
namespace com\icemalta\kahuna\client\controller;

class ProductController extends Controller
{
    public static function getAll(array $params, array $data): object
    {
        $payload = [
            'api_user' => $_SESSION['api_user'],
            'api_token' => $_SESSION['api_token']
        ];
        return json_decode(self::req('GET', '/product', $payload));
    }

    public static function register(array $params, array $data): object
    {
        $payload = [
            'serial_number' => $data['serial_number'],
            'purchase_date' => $data['purchase_date'],
            'api_user' => $_SESSION['api_user'],
            'api_token' => $_SESSION['api_token']
        ];
        return json_decode(self::req('POST', '/product', $payload));
    }
}