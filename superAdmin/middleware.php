<?php
require_once '../vendor/autoload.php';
require_once '../config.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

session_start();
include('../db_conn.php');

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    http_response_code(401);
    exit();
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$token = str_replace('Bearer ', '', $authHeader);

try {
    $decoded = JWT::decode($token, new Key(SECRET_KEY, ALGORITHM));
    $_SESSION['user_id'] = $decoded->sub;
    $_SESSION['role_id'] = $decoded->role;
} catch (Exception $e) {
    echo json_encode(["error" => "Invalid token: " . $e->getMessage()]);
    http_response_code(401);
    exit();
}
?>
