<?php
session_start();
include('../db_conn.php');
include('../config.php');
require_once '../vendor/autoload.php';  // Include Firebase JWT library
use \Firebase\JWT\JWT;

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Read raw POST data (for JSON body)
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email']) || !isset($data['password'])) {
        echo json_encode(["error" => "Email and Password are required"]);
        http_response_code(400);
        exit();
    }

    $email = trim($data['email']);
    $password = trim($data['password']);

    if (empty($email) || empty($password)) {
        echo json_encode(["error" => "Email and Password cannot be empty"]);
        http_response_code(400);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, email, password FROM super_admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $email, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['super_admin_id'] = $id;  // Set the session variable to the super admin's ID
            // Generate JWT Token
            $token = generateJWT($id, $email);

            echo json_encode([
                "message" => "Login successful",
                "token" => $token
            ]);
        } else {
            echo json_encode(["error" => "Invalid credentials"]);
            http_response_code(401);
        }
    } else {
        echo json_encode(["error" => "No account found"]);
        http_response_code(404);
    }
    $stmt->close();
}

// Function to generate JWT token
function generateJWT($userId, $email) {
    $issuedAt = time();
    $expirationTime = $issuedAt + TOKEN_EXPIRATION;  // Token expires in 1 hour
    $payload = [
        'iss' => 'your_domain.com',  // Issuer
        'iat' => $issuedAt,          // Issued at: time when the token was generated
        'exp' => $expirationTime,    // Expiration time
        'sub' => $userId,            // Subject (user ID)
        'email' => $email            // User email
    ];

    // Encode the payload with the secret key and algorithm
    $jwt = JWT::encode($payload, SECRET_KEY, ALGORITHM);

    return $jwt;
}
?>
