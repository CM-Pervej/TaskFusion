<?php
session_start(); // Start the session

require '../middleware.php'; // Include the middleware to check authentication

// Check if the user is authenticated by checking the session
$userData = checkAuth(); // This will verify JWT from session and return user data
include '../db_conn.php';

try {
    // Fetch room types
    $roomTypeSql = "SELECT * FROM room_types";
    $stmt = $conn->prepare($roomTypeSql);
    $stmt->execute();
    $roomTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert room types into an associative array (id => room type)
    $roomTypeMap = [];
    foreach ($roomTypes as $type) {
        $roomTypeMap[$type['id']] = $type['room_category'] . ' - ' . $type['room_type'];
    }

    // Fetch rooms
    $sql = "SELECT * FROM rooms";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms || Paaru Royal Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="flex min-h-screen bg-white">
    <section class="z-10">
        <?php include "../sidebar.php"; ?>
    </section>

    <main class="p-6 ml-64 w-full">
        <table class="table border-collapse">
            <thead>
                <tr>
                    <th>SL </th>
                    <th>Floor</th>
                    <th>Room</th>
                    <th>Capacity</th>
                    <th>Price per night</th>
                    <th>Room Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($rooms)): ?>
                    <tr><td colspan="4">No Rooms Found</td></tr>
                <?php else:?>
                    <?php $SL = 1; foreach($rooms as $room):?>
                        <tr class="text-lg">
                            <td><?php echo $SL++;?></td>
                            <td><?php echo $room['floor'];?></td>
                            <td><?php echo $room['room_number'];?></td>
                            <td><?php echo $room['capacity'];?></td>
                            <td><?php echo $room['price_per_night'];?></td>
                            <td><?php echo isset($roomTypeMap[$room['room_type_id']]) ? $roomTypeMap[$room['room_type_id']] : 'Unknown'; ?></td>
                            <td><?php echo $room['status'] == 1? 'Yes' : 'No';?></td>
                        </tr>
                    <?php endforeach;?>
                <?php endif;?>
            </tbody>
        </table>
    </main>
</body>
</html>