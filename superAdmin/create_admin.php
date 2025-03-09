<?php
// create_admin.php

// Include the middleware to check for JWT authentication
include('middleware.php');

// Check if super admin is logged in
$superAdminInfo = isSuperAdminLoggedIn(); // This will return the super admin info

// Handle Admin Creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    
    // Handling Image Upload for Admin
    $adminImage = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/admin_images/";
        $adminImage = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $adminImage);
    }

    if (empty($name) || empty($email) || empty($contact)) {
        echo json_encode(["error" => "All fields are required"]);
        exit();
    }

    // Insert new admin
    $stmt = $conn->prepare("INSERT INTO admins (name, email, contact, image, role_id) VALUES (?, ?, ?, ?, 2)");
    $stmt->bind_param("ssss", $name, $email, $contact, $adminImage);
    
    if ($stmt->execute()) {
        $adminId = $stmt->insert_id;
        $stmt->close();

        // Send Email to Admin with Registration Link
        $registerLink = "http://yourdomain.com/admin_register.php?email=" . urlencode($email);
        
        // Send email logic here...

        echo json_encode(["message" => "Admin registered successfully. Email sent."]);
    } else {
        echo json_encode(["error" => "Failed to register admin"]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center mb-4">Create Admin</h2>
        
        <!-- Display Super Admin Info -->
        <div class="text-center mb-4">
            <p class="text-lg font-medium">Welcome, <?php echo htmlspecialchars($superAdminInfo['name']); ?> (<?php echo htmlspecialchars($superAdminInfo['email']); ?>)</p>
        </div>

        <form id="createAdminForm" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Admin Name</label>
                <input type="text" id="name" name="name" class="input input-bordered w-full" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="input input-bordered w-full" required>
            </div>
            <div class="mb-4">
                <label for="contact" class="block text-gray-700">Contact</label>
                <input type="text" id="contact" name="contact" class="input input-bordered w-full" required>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700">Admin Image</label>
                <input type="file" id="image" name="image" class="input input-bordered w-full">
            </div>
            <div class="mb-4">
                <button type="submit" class="btn btn-primary w-full">Create Admin</button>
            </div>
        </form>
        <div id="error-message" class="text-red-500 text-center hidden">Error occurred while processing request</div>
    </div>

    <script>
        document.getElementById('createAdminForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('create_admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    window.location.href = 'dashboard.php';
                } else {
                    document.getElementById('error-message').classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>
