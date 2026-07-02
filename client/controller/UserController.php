<?php
namespace com\icemalta\jobapp\client\controller;

class UserController extends Controller
{
    public static function register(array $params, array $data): object
    {
        $payload = [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => $data['password']
        ];
        return json_decode(self::req('POST', '/user', $payload));
    }
}