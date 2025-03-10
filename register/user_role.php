<?php
include '../db_conn.php'; // Ensure this file correctly initializes $conn

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_role = trim($_POST['user_role']);

    try {
        $sql = "INSERT INTO user_role (user_role) VALUES (:user_role)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_role' => $user_role]);

        $message = "New user role added successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch all user roles from the database
$user_role = [];
try{
    $sql = "SELECT * FROM user_role";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $user_role = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e){
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Role Section</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <!-- User Roles Table -->
    <section class="w-full max-w-4xl p-6 bg-white shadow-lg rounded-lg mb-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">User Roles</h2>
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border px-4 py-2">SL</th>
                    <th class="border px-4 py-2">Role</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($user_role)): ?>
                    <tr><td class="text-center py-4" colspan="2">No Users Found</td></tr>
                <?php else: ?>
                    <?php $SL = 1; foreach($user_role as $role): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $SL++; ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($role['user_role']); ?></td>
                        </tr>
                    <?php endforeach;?>
                <?php endif;?>
            </tbody>
        </table>
    </section>
    
    <!-- Add User Role Form -->
    <section class="w-full max-w-md p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800">Add User Role</h2>
        <!-- Success/Error Message -->
        <?php if (isset($message)): ?>
            <div class="alert alert-success mt-4"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form action="" method="POST" class="mt-6">
            <div class="form-control flex gap-2">
                <label for="user_role" class="label w-max">
                    <span class="label-text text-gray-700 whitespace-nowrap">User Role:</span>
                </label>
                <input type="text" id="user_role" name="user_role" required
                    class="input w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-full">Add Role</button>
            </div>
        </form>
    </section>
</body>
</html>
