<?php
require 'config.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function checkAuth() {
    // Check if the JWT token is stored in session
    if (isset($_SESSION['jwt'])) {
        $jwt = $_SESSION['jwt']; // Get token from session
    } else {
        header("Location: /taskfusion/login.php");
        exit();
    }

    try {
        // Decode the JWT token
        $decoded = JWT::decode($jwt, new Key(SECRET_KEY, ALGORITHM));
        return (array) $decoded; // Return decoded payload as an array for easy access
    } catch (Exception $e) {
        // Invalid token or expired token, redirect to login page
        header("Location: /taskfusion/login.php");
        exit();
    }
}
?>
