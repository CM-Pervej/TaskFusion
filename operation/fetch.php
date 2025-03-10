<?php 
include "../db_conn_PDO.php";

try{
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e){
    echo "Error: ". $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>List of users</h1>
    <table border="1">
        <tr>
            <th>SL </th>
            <th>Name </th>
            <th>Gender </th>
            <th>Email </th>
            <th>Phone </th>
            <th>Birth </th>
            <th>Address </th>
            <th>image</th>
        </tr>
        <?php if(empty($users)): ?>
            <tr><td colspan="8">No Users Found</td></tr>
        <?php else: ?>
            <?php $SL=1; foreach($users as $user): ?>
                <tr>
                    <td> <?php echo $SL++; ?></td>
                    <td> <?php echo htmlspecialchars($user['name']); ?></td>
                    <td> <?php echo htmlspecialchars($user['gender']); ?></td>
                    <td> <?php echo htmlspecialchars($user['email']); ?></td>
                    <td> <?php echo htmlspecialchars($user['phone']); ?></td>
                    <td> <?php echo htmlspecialchars($user['birth']); ?></td>
                    <td> <?php echo htmlspecialchars($user['address']); ?></td>
                    <td>
                        <?php 
                            $directory = !empty($user['image'])? '../uploads/' . basename($user['image']) : '../uploads/default.png';
                            if (!empty($user['image']) && file_exists($directory)):
                        ?>
                         <img src="<?php echo $directory;?>" alt="<?php echo htmlspecialchars($user['name']);?>" width="100">
                        <?php else: ?>
                            <p>Image not found</p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif;?>
    </table>
</body>
</html>