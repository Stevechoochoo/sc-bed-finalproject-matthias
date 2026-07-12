<?php
namespace com\icemalta\kahuna\client\controller;

class AuthController extends Controller 
{
    public static function login($params, $data): object
    {
        $payload = [
            'email' => $data['email'],
            'password' => $data['password']
        ];
        return json_decode(self::req('POST', '/login', $payload));
    }

    public static function logout($params, $data): object
    {
        $payload = [
            'api_user' => $_SESSION['api_user'],
            'api_token' => $_SESSION['api_token']
        ];
        return json_decode(self::req('POST', '/logout', $payload));
    }
}