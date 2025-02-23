<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;



if(isset($_SERVER['REQUEST_METHOD']) == 'GET'){

    $now = time();
    $key = 'example_key';
    $payload = [
    'iat' => $now,
    'exp' => $now + 3600,
    'data' => '1',
    
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');    
    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        print_r($decoded);
    } catch (Exception $e) {
        echo 'An error occurred: ' . $e->getMessage();
    }
}