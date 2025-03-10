<?php 
include '../db_conn_PDO.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $birth = $_POST['birth'];
    $address = $_POST['address'];


    // normal image insertion 
    // $image = "";
    // if(!empty($_FILES["image"]["name"])){
    //     $directory = "uploads/";
    //     $image = $directory.basename($_FILES["image"]["name"]);
    //     move_uploaded_file($_FILES["image"]["tmp_name"],$image);
    // }


    $image = "";
    if(!empty($_FILES["image"]["name"])){
        $allowed_extensions = ['jpg', 'png', 'jpeg'];
        $max_size = 5 * 1024 * 1024; // 5  MB

        $file_name = $_FILES["image"]["name"];
        $file_size = $_FILES["image"]["size"];
        $file_tmp_name = $_FILES["image"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if(!in_array($file_ext, $allowed_extensions)){
            die("Error: Invalid file type. Only jpg, jpeg and png are allowed");
        }

        if($file_size > $max_size){
            die("Error: Maximum file size is 5MB");
        }

        $image = "../uploads/" . uniqid("profile_", true) . "." . $file_ext;
        move_uploaded_file($file_tmp_name, $image);
    }

    try{
        $sql = "INSERT INTO users (name, gender, email, phone, birth, address, image)
        VALUES (:name, :gender, :email, :phone, :birth, :address, :image)";

        $stmt = $conn->prepare($sql);

        $stmt->execute(
            [':name' => $name, ':gender' => $gender, ':email' => $email, ':phone' => $phone, ':birth' => $birth, ':address' => $address, ':image' => $image]
        );

        $message = "New record created successfully";
    } catch (PDOException $e) {
        $message = "Error: ". $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Human Data</title>
</head>
<body>
    <h2>Insert Human Information</h2>

    <!-- Success/Error Message -->
    <?php if (isset($message)): ?>
        <div class="alert alert-success mt-4"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>First Name:</label>
        <input type="text" name="name" required><br><br>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select><br><br>

        <label>Birth Date:</label>
        <input type="date" name="birth" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Phone:</label>
        <input type="text" name="phone" required><br><br>

        <label>Address:</label>
        <textarea name="address" required></textarea><br><br>

        <label>Profile Picture:</label>
        <input type="file" name="image"><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>