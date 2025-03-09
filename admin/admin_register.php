<?php
include('../db_conn.php'); 
include('../config.php');
require_once '../vendor/autoload.php'; 
use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $companyName = trim($_POST['company_name']);
    $uniqueInfo = trim($_POST['unique_info']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Handling Logo Upload for Company
    $companyLogo = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/company_logos/";
        $companyLogo = $targetDir . basename($_FILES["logo"]["name"]);
        move_uploaded_file($_FILES["logo"]["tmp_name"], $companyLogo);
    }

    if (empty($companyName) || empty($uniqueInfo) || empty($_POST['password'])) {
        echo json_encode(["error" => "All fields are required"]);
        exit();
    }

    // Check if admin exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($adminId);
    $stmt->fetch();
    $stmt->close();

    if (!$adminId) {
        echo json_encode(["error" => "Admin account not found"]);
        exit();
    }

    // Insert company details
    $stmt = $conn->prepare("INSERT INTO company (admin_id, company_name, unique_info, password, logo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $adminId, $companyName, $uniqueInfo, $password, $companyLogo);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Registration completed successfully"]);
    } else {
        echo json_encode(["error" => "Failed to complete registration"]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center mb-4">Admin Registration</h2>
        <form id="registerForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="email" name="email" value="<?php echo $_GET['email'] ?? ''; ?>" required>

            <div class="mb-4">
                <label for="company_name" class="block text-gray-700">Company Name</label>
                <input type="text" id="company_name" name="company_name" class="input input-bordered w-full" required>
            </div>
            <div class="mb-4">
                <label for="unique_info" class="block text-gray-700">Unique Information</label>
                <input type="text" id="unique_info" name="unique_info" class="input input-bordered w-full" required>
            </div>
            <div class="mb-4">
                <label for="logo" class="block text-gray-700">Company Logo</label>
                <input type="file" id="logo" name="logo" class="input input-bordered w-full">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="input input-bordered w-full" required>
            </div>
            <div class="mb-4">
                <button type="submit" class="btn btn-primary w-full">Complete Registration</button>
            </div>
        </form>
        <div id="error-message" class="text-red-500 text-center hidden">Error processing request</div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('admin_register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    window.location.href = 'login.php';
                } else {
                    document.getElementById('error-message').classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>
