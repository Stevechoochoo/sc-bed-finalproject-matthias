<?php
namespace com\icemalta\kahuna\client\controller;

class UserController extends Controller
{
    public static function getInfo(array $params, array $data): object
    {
        $userInfo = json_decode(self::req('GET', "/user", $data));
        return $userInfo;
    }

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