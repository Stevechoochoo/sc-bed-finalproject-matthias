<?php
require 'com/icemalta/kahuna/model/DBConnect.php';
require 'com/icemalta/kahuna/model/User.php';
use com\icemalta\kahuna\model\User;

cors();

$requestData = [];
header("Content-Type: application/json; charset=UTF-8");

/* BASE URI */
$BASE_URI = '/kahuna/api/';

function sendResponse(mixed $data = null, int $code = 200, mixed $error = null): void
{
    if (!is_null($data)) {
        $response['data'] = $data;
    }
    if (!is_null($error)) {
        $response['error'] = $error;
    }
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    http_response_code($code);
}

/* Get Request Data */
$requestMethod = $_SERVER['REQUEST_METHOD'];
switch ($requestMethod) {
    case 'POST':
        $requestData = $_POST;
        break;
    default:
        sendResponse(null, 405, 'Method not allowed.');
        exit;
}

/* Extract EndPoint */
$parsedURI = parse_url($_SERVER["REQUEST_URI"]);
$path = explode('/', str_replace($BASE_URI, "", $parsedURI["path"]));
$endPoint = $path[0];
if (empty($endPoint)) {
    $endPoint = "/";
}

/* EndPoint Handlers */
$endpoints["user"] = function (array $requestData): void {
    $name = $requestData['name'];
    $surname = $requestData['surname'];
    $email = $requestData['email'];
    $password = $requestData['password'];
    $user = new User(name: $name, surname: $surname, email: $email, password: $password);
    $user = User::save($user);
    sendResponse(data: $user, code: 201);
};

$endpoints["login"] = function (array $requestData): void {
    $email = $requestData['email'];
    $password = $requestData['password'];
    $user = new User(email: $email, password: $password);
    $user = User::authenticate($user);

    if ($user) {
        // Login ok
        $token = str_replace("=", "", base64_encode(random_bytes(160 / 8)));
        User::saveToken($user, $token);
        sendResponse(data: [
            'user' => $user->getId(),
            'token' => $token
        ]);
    } else {
        // Login failed
        sendResponse(code: 401, error: 'Login failed.');
    }
};

$endpoints["404"] = function (array $requestData): void {
    sendResponse(null, 404, "Endpoint " . $requestData["endPoint"] . " not found.");
};

function cors()
{
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}

try {
    if (isset($endpoints[$endPoint])) {
        $endpoints[$endPoint]($requestData);
    } else {
        $endpoints["404"](array("endPoint" => $endPoint));
    }
} catch (Exception $e) {
    sendResponse(null, 500, $e->getMessage());
} catch (Error $e) {
    sendResponse(null, 500, $e->getMessage());
}