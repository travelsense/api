<?php
require_once __DIR__ . '/vendor/autoload.php';
$api = new \Api\Test\ApiClient('http://localhost:8000');

try {
    $api->registerUser([
        'firstName' => 'John',
        'lastName' => 'Smith',
        'email' => 'john@example.com',
        'password' => '123',
    ]);
} catch (\Api\Test\ApiClientException $e) {
    if ($e->getCode() === \Api\Exception\ApiException::USER_EXISTS) {
        echo "User already registered\n";
    } else {
        throw $e;
    }
}

//$token = $api->getTokenByEmail('john@example.com', '123');
$token = 'c029e9641bc3a4410ca766b796023482e3e2893c1';
echo "Auth token: $token\n";

$api->setAuthToken($token);

var_dump($api->getCurrentUser());
