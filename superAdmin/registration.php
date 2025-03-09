<?php
// Include database connection
include('../db_conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $contact = trim($_POST['contact']);

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($contact)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }

    // Handle Image Upload
    $image_path = NULL; // Default value if no image is uploaded
    if (!isset($error) && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $image_tmp = $image['tmp_name'];
        $image_size = $image['size'];
        $image_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($image_ext, $allowed_exts)) {
            $error = "Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF, WEBP.";
        } elseif ($image_size > 5000000) { // 5MB limit
            $error = "Image size exceeds 5MB.";
        } else {
            // Rename the file to avoid conflicts (e.g., profile_1708429123.jpg)
            $newFileName = "profile_" . time() . "_" . uniqid() . "." . $image_ext;
            $upload_dir = "../uploads/";
            $image_path = $upload_dir . $newFileName;

            // Move uploaded file
            if (!move_uploaded_file($image_tmp, $image_path)) {
                $error = "Failed to upload image.";
            } else {
                $image_path = $newFileName; // Store only the file name in the database
            }
        }
    }

    // Hash password before inserting into the database
    if (!isset($error)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into the database
        $stmt = $conn->prepare("INSERT INTO super_admins (name, email, password, contact, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $contact, $image_path);

        if ($stmt->execute()) {
            $success = "Super Admin created successfully!";
        } else {
            error_log("MySQL Error: " . $stmt->error); // Log error for debugging
            $error = "Error creating Super Admin.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto mt-10">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-6 text-center">Super Admin Registration</h2>
            
            <!-- Success or Error Message -->
            <?php if (isset($success)) { echo "<div class='bg-green-200 text-green-700 p-4 mb-4'>{$success}</div>"; } ?>
            <?php if (isset($error)) { echo "<div class='bg-red-200 text-red-700 p-4 mb-4'>{$error}</div>"; } ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium">Full Name</label>
                    <input type="text" name="name" id="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <input type="password" name="password" id="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="mb-4">
                    <label for="contact" class="block text-sm font-medium">Contact Number</label>
                    <input type="text" name="contact" id="contact" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium">Profile Image (Optional)</label>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <small class="text-gray-500">Max size: 5MB. High-resolution image supported.</small>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg">Register</button>
            </form>
        </div>
    </div>
</body>
</html>
