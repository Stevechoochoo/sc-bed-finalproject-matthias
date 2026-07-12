<?php
require 'com/icemalta/kahuna/model/DBConnect.php';
require 'com/icemalta/kahuna/model/User.php';
require 'com/icemalta/kahuna/model/Product.php';
use com\icemalta\kahuna\model\User;
use com\icemalta\kahuna\model\Product;

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
    case 'GET':
        $requestData = $_GET;
        break;
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

if (isset($_SERVER["HTTP_X_API_USER"])) {
    $requestData["api_user"] = $_SERVER["HTTP_X_API_USER"];
}
if (isset($_SERVER["HTTP_X_API_KEY"])) {
    $requestData["api_token"] = $_SERVER["HTTP_X_API_KEY"];
}

/* EndPoint Handlers */
$endpoints["user"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'GET') {
        if (isset($requestData['api_user'], $requestData['api_token']) && User::verifyToken($requestData['api_user'], $requestData['api_token'])) {
            $user = new User(id: $requestData['api_user']);
            $userInfo = User::getInfo($user);
            sendResponse(data: $userInfo);
        } else {
            sendResponse(code: 403, error: 'Missing, invalid or expired token.');
        }
        return;
    }

    $name = $requestData['name'];
    $surname = $requestData['surname'];
    $email = $requestData['email'];
    $password = $requestData['password'];
    $user = new User(name: $name, surname: $surname, email: $email, password: $password);
    $user = User::save($user);
    sendResponse(data: $user, code: 201);
};

$endpoints["login"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod !== 'POST') {
        sendResponse(null, 405, 'Method not allowed.');
        return;
    }

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

$endpoints["logout"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod !== 'POST') {
        sendResponse(null, 405, 'Method not allowed.');
        return;
    }

    if (isset($requestData['api_user'], $requestData['api_token']) && User::verifyToken($requestData['api_user'], $requestData['api_token'])) {
        $user = new User(id: $requestData['api_user']);
        User::deleteToken($user);
        sendResponse(data: ['message' => 'Logout succeeded.']);
    } else {
        sendResponse(code: 403, error: 'Missing, invalid or expired token.');
    }
};

$endpoints["product"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod === 'GET') {
        if (isset($requestData['api_user'], $requestData['api_token']) && User::verifyToken($requestData['api_user'], $requestData['api_token'])) {
            $products = Product::getAll();
            sendResponse(data: $products);
        } else {
            sendResponse(code: 403, error: 'Missing, invalid or expired token.');
        }
        return;
    }

    if ($requestMethod !== 'POST') {
        sendResponse(null, 405, 'Method not allowed.');
        return;
    }

    if (isset($requestData['api_user'], $requestData['api_token']) && User::verifyToken($requestData['api_user'], $requestData['api_token'])) {
        $serialNumber = $requestData['serial_number'];
        $purchaseDate = $requestData['purchase_date'];
        $product = new Product(serialNumber: $serialNumber);
        $product = Product::getBySerialNumber($product);

        if ($product) {
            $user = new User(id: $requestData['api_user']);
            $product->setPurchaseDate($purchaseDate);
            $product = Product::register($user, $product);
            sendResponse(data: $product, code: 201);
        } else {
            sendResponse(code: 404, error: 'Product serial number not found.');
        }
    } else {
        sendResponse(code: 403, error: 'Missing, invalid or expired token.');
    }
};

$endpoints["registered-products"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod !== 'GET') {
        sendResponse(null, 405, 'Method not allowed.');
        return;
    }

    if (isset($requestData['api_user'], $requestData['api_token']) && User::verifyToken($requestData['api_user'], $requestData['api_token'])) {
        $user = new User(id: $requestData['api_user']);
        $products = Product::getRegistered($user);
        sendResponse(data: $products);
    } else {
        sendResponse(code: 403, error: 'Missing, invalid or expired token.');
    }
};

$endpoints["registered-product"] = function (string $requestMethod, array $requestData): void {
    if ($requestMethod !== 'GET') {
        sendResponse(null, 405, 'Method not allowed.');
        return;
    }

    if (isset($requestData['api_user'], $requestData['api_token']) && User::verifyToken($requestData['api_user'], $requestData['api_token'])) {
        if (!isset($requestData['serial_number'])) {
            sendResponse(code: 400, error: 'Missing serial number.');
            return;
        }
        $user = new User(id: $requestData['api_user']);
        $product = new Product(serialNumber: $requestData['serial_number']);
        $product = Product::getRegisteredBySerialNumber($user, $product);
        if ($product) {
            sendResponse(data: $product);
        } else {
            sendResponse(code: 404, error: 'Registered product not found.');
        }
    } else {
        sendResponse(code: 403, error: 'Missing, invalid or expired token.');
    }
};

$endpoints["404"] = function (string $requestMethod, array $requestData): void {
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
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}

try {
    if (isset($endpoints[$endPoint])) {
        $endpoints[$endPoint]($requestMethod, $requestData);
    } else {
        $endpoints["404"]($requestMethod, array("endPoint" => $endPoint));
    }
} catch (Exception $e) {
    sendResponse(null, 500, $e->getMessage());
} catch (Error $e) {
    sendResponse(null, 500, $e->getMessage());
}