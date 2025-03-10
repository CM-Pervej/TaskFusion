<?php
include '../db_conn.php';

$user_roles = [];
try {
    $sql = "SELECT * FROM user_role";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $user_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $gender = trim($_POST['gender']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $birth = trim($_POST['birth']);
    $address = trim($_POST['address']);
    $user_role = trim($_POST['user_role']);

    // Handle Image Upload
    $image = "";
    if (!empty($_FILES["image"]["name"])) {
        $allowed_extensions = ['jpg', 'png', 'jpeg'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file_name = $_FILES["image"]["name"];
        $file_size = $_FILES["image"]["size"];
        $file_tmp_name = $_FILES["image"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            die("Error: Invalid file type. Only jpg, jpeg, and png are allowed.");
        }

        if ($file_size > $max_size) {
            die("Error: Maximum file size is 5MB.");
        }

        $image = "../uploads/" . uniqid("profile_", true) . "." . $file_ext;
        move_uploaded_file($file_tmp_name, $image);
    }

    try {
        $sql = "INSERT INTO users (name, gender, email, password, phone, birth, address, user_role, image)
                VALUES (:name, :gender, :email, :password, :phone, :birth, :address, :user_role, :image)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':gender' => $gender,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':phone' => $phone,
            ':birth' => $birth,
            ':address' => $address,
            ':user_role' => $user_role,
            ':image' => $image
        ]);

        $message = "New employee $name has been created successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-2xl p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800">Add New User</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-success mt-4"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="mt-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="form-control">
                    <label for="name" class="label">
                        <span class="label-text">Name</span>
                    </label>
                    <input type="text" id="name" name="name" required class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label for="gender" class="label">
                        <span class="label-text">Gender</span>
                    </label>
                    <select id="gender" name="gender" required class="select select-bordered w-full">
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="form-control">
                    <label for="phone" class="label">
                        <span class="label-text">Phone</span>
                    </label>
                    <input type="tel" id="phone" name="phone" required class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label for="email" class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="email" name="email" required class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label for="password" class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" id="password" name="password" required class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label for="birth" class="label">
                        <span class="label-text">Birth Date</span>
                    </label>
                    <input type="date" id="birth" name="birth" required class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label for="address" class="label">
                        <span class="label-text">Address</span>
                    </label>
                    <textarea id="address" name="address" rows="2" required class="textarea textarea-bordered w-full"></textarea>
                </div>

                <div class="form-control">
                    <label for="image" class="label">
                        <span class="label-text">Profile Image</span>
                    </label>
                    <input type="file" id="image" name="image" class="file-input file-input-bordered w-full">
                </div>

                <div class="form-control">
                    <label for="user_role" class="label">
                        <span class="label-text">User Role</span>
                    </label>
                    <select name="user_role" id="user_role" required class="select select-bordered w-full">
                        <option value="" disabled selected>Select Role</option>
                        <?php if (empty($user_roles)): ?>
                            <option value="">No User Roles Found</option>
                        <?php else: ?>
                            <?php foreach ($user_roles as $role): ?>
                                <option value="<?= $role['id']; ?>"><?= htmlspecialchars($role['user_role']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn btn-primary w-full">Add User</button>
            </div>
        </form>
    </div>
</body>
</html>
