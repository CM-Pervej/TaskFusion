<?php 
include "../db_conn_PDO.php";

// Fetch users
try {
    $sql = "SELECT * FROM users ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submission (Update User)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Fetch current image
    $stmt = $conn->prepare("SELECT image FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $oldImage = $user['image'];

    // Handle image upload
    if (!empty($_FILES["image"]["name"])) {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_name = $_FILES["image"]["name"];
        $file_tmp = $_FILES["image"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            die("Invalid file type. Only jpg, jpeg, and png allowed.");
        }

        // Delete old image if exists
        if (!empty($oldImage) && file_exists("../uploads/" . basename($oldImage))) {
            unlink("../uploads/" . basename($oldImage));
        }

        // Upload new image
        $newImage = "uploads/" . uniqid("profile_", true) . "." . $file_ext;
        move_uploaded_file($file_tmp, "../" . $newImage);
    } else {
        $newImage = $oldImage;
    }

    // Update user data
    try {
        $sql = "UPDATE users SET name = :name, email = :email, image = :image WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':image' => $newImage,
            ':id' => $id
        ]);

        header("Location: update.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border: 1px solid black; }
        img { width: 100px; height: 100px; object-fit: cover; border-radius: 5px; }
        .btn { padding: 5px 10px; text-decoration: none; margin: 2px; cursor: pointer; }
        .edit { background-color: blue; color: white; }
        .modal { display: none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; padding: 20px; width: 40%; margin: 10% auto; }
    </style>
    <script>
        function showEditModal(id, name, email, image) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('currentImage').src = image;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <h1>List of Users</h1>
    <table>
        <tr>
            <th>SL</th>
            <th>Name</th>
            <th>Email</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php if (empty($users)): ?>
            <tr><td colspan="5" style="text-align: center;">No Users Found</td></tr>
        <?php else: ?>
            <?php $SL = 1; foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $SL++; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php 
                            $imagePath = !empty($user['image']) ? '../uploads/' . basename($user['image']) : '../uploads/default.png';
                        ?>
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($user['name']); ?>">
                    </td>
                    <td>
                        <button class="btn edit" onclick="showEditModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $imagePath; ?>')">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit User</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editUserId">
                <label>Name:</label>
                <input type="text" name="name" id="editName" required><br><br>

                <label>Email:</label>
                <input type="email" name="email" id="editEmail" required><br><br>

                <label>Current Image:</label><br>
                <img id="currentImage" src="" width="100"><br><br>

                <label>New Image (optional):</label>
                <input type="file" name="image"><br><br>

                <button type="submit">Update</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>
</body>
</html>
