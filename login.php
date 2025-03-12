<?php
require 'db_conn.php';
require 'vendor/autoload.php';
require 'config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read input data
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    header("Content-Type: application/json");

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        echo json_encode(["error" => "Email and Password are required."]);
        exit;
    }

    try {
        // Fetch user from the database by email
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Generate JWT Token
            $payload = [
                "iss" => "yourdomain.com",
                "iat" => time(),
                // "exp" => time() + 3600, // 1 hour expiration
                "exp" => time() + TOKEN_EXPIRATION, // Use the constant from config.php
                "sub" => $user['id'],
                "email" => $user['email'],
                "role" => $user['user_role']
            ];

            $jwt = JWT::encode($payload, SECRET_KEY, ALGORITHM);

            // Store JWT token in session
            $_SESSION['jwt'] = $jwt;

            echo json_encode([
                "message" => "Login successful!",
                "token" => $jwt // Optionally send the token in the response
            ]);
        } else {
            echo json_encode(["error" => "Invalid email or password."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="w-full max-w-md p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800">Login</h2>

        <!-- Message Box -->
        <div id="message" class="hidden p-3 mt-3 text-white rounded-lg"></div>

        <form id="loginForm" class="mt-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="email" id="email" name="email" class="input input-bordered w-full" required>
            </div>

            <div class="form-control mt-3">
                <label class="label">
                    <span class="label-text">Password</span>
                </label>
                <input type="password" id="password" name="password" class="input input-bordered w-full" required>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-full">Login</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("loginForm").addEventListener("submit", async function(event) {
            event.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const messageBox = document.getElementById("message");

            try {
                const response = await fetch("login.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.token) {
                    // Store JWT in session (client-side)
                    localStorage.setItem("jwt", data.token); // Or you can use cookies

                    messageBox.textContent = "Login successful!";
                    messageBox.classList.remove("hidden", "bg-red-500");
                    messageBox.classList.add("bg-green-500");

                    setTimeout(() => {
                        window.location.href = "dashboard.php"; // Redirect to dashboard after successful login
                    }, 1500);
                } else {
                    throw new Error(data.error || "Invalid credentials");
                }
            } catch (error) {
                messageBox.textContent = error.message;
                messageBox.classList.remove("hidden", "bg-green-500");
                messageBox.classList.add("bg-red-500");
            }
        });
    </script>
</body>
</html>
