<?php 
include "../db_conn_PDO.php";

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Fetch user image path before deleting
    try {
        $stmt = $conn->prepare("SELECT image FROM users WHERE id = :id");
        $stmt->execute([':id' => $delete_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the image file if exists
        if (!empty($user['image']) && file_exists("../uploads/" . basename($user['image']))) {
            unlink("../uploads/" . basename($user['image']));
        }

        // Delete the user from the database
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $delete_id]);

        // Redirect to refresh the page
        header("Location: delete.php");
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch users
try {
    $sql = "SELECT * FROM users ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
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
        .btn { padding: 5px 10px; text-decoration: none; margin: 2px; }
        .update { background-color: yellow; color: black; }
        .delete { background-color: red; color: white; }
    </style>
</head>
<body>
    <h1>List of Users</h1>
    <table>
        <tr>
            <th>SL</th>
            <th>Name</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Birth</th>
            <th>Address</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php if (empty($users)): ?>
            <tr><td colspan="9" style="text-align: center;">No Users Found</td></tr>
        <?php else: ?>
            <?php $SL = 1; foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $SL++; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['gender']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo htmlspecialchars($user['birth']); ?></td>
                    <td><?php echo htmlspecialchars($user['address']); ?></td>
                    <td>
                        <?php 
                            $imagePath = !empty($user['image']) ? '../uploads/' . basename($user['image']) : '../uploads/default.png';
                            if (!empty($user['image']) && file_exists($imagePath)): 
                        ?>
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($user['name']); ?>">
                        <?php else: ?>
                            <p>Image not found</p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="update.php?id=<?php echo $user['id']; ?>" class="btn update">Update</a>
                        <a href="?delete_id=<?php echo $user['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>
</html>
