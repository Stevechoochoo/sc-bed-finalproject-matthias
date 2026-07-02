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
}