<?php
session_start();
require '../middleware.php';
$userData = checkAuth();
include '../db_conn.php';

try {
    $stmt = $conn->prepare("SELECT * FROM room_types");
    $stmt->execute();
    $roomTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $roomTypeMap = [];
    foreach ($roomTypes as $type) {
        $roomTypeMap[$type['id']] = $type['room_category'] . ' - ' . $type['room_type'];
    }

    $stmt = $conn->prepare("SELECT * FROM rooms");
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $floor = $_POST['floor'];
    $room_number = $_POST['room_number'];
    $capacity = $_POST['capacity'];
    $price_per_night = $_POST['price_per_night'];
    $room_type_id = $_POST['room_type_id'];
    $status = $_POST['status'];

    try {
        $sql = "UPDATE rooms SET floor = ?, room_number = ?, capacity = ?, price_per_night = ?, room_type_id = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$floor, $room_number, $capacity, $price_per_night, $room_type_id, $status, $id]);

        header("Location: all_rooms.php?success=Room updated successfully");
        exit();
    } catch (PDOException $e) {
        die("Error updating room: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rooms || Paaru Royal Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <h2 class="text-2xl font-semibold mb-4">Rooms Information</h2>

    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-gray-300">
            <tr>
                <th>SL</th>
                <th>Floor</th>
                <th>Room</th>
                <th>Capacity</th>
                <th>Price</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $SL = 1; foreach($rooms as $room): ?>
                <tr class="text-center border-b">
                    <td><?= $SL++; ?></td>
                    <td><?= $room['floor']; ?></td>
                    <td><?= $room['room_number']; ?></td>
                    <td><?= $room['capacity']; ?></td>
                    <td>$<?= $room['price_per_night']; ?></td>
                    <td><?= $roomTypeMap[$room['room_type_id']] ?? 'Unknown'; ?></td>
                    <td class="<?= $room['status'] == 1 ? 'text-green-500' : 'text-red-500'; ?>">
                        <?= $room['status'] == 1 ? 'Available' : 'Occupied'; ?>
                    </td>
                    <td>
                        <button onclick="fillForm(<?= htmlspecialchars(json_encode($room)); ?>)" class="bg-blue-500 px-3 py-1 text-white rounded">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ✅ Update Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-5 rounded shadow-lg">
            <h3 class="text-xl font-semibold mb-3">Update Room</h3>
            <form method="post">
                <input type="hidden" name="id" id="editId">
                
                <label>Floor:</label>
                <input type="text" name="floor" id="editFloor" class="w-full border p-2 mb-2">
                
                <label>Room Number:</label>
                <input type="text" name="room_number" id="editRoomNumber" class="w-full border p-2 mb-2">
                
                <label>Capacity:</label>
                <input type="text" name="capacity" id="editCapacity" class="w-full border p-2 mb-2">
                
                <label>Price Per Night:</label>
                <input type="text" name="price_per_night" id="editPrice" class="w-full border p-2 mb-2">
                
                <label>Room Type:</label>
                <select name="room_type_id" id="editRoomTypeId" class="w-full border p-2 mb-2">
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?= $type['id']; ?>"><?= $type['room_category'] . ' - ' . $type['room_type']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Status:</label>
                <select name="status" id="editStatus" class="w-full border p-2 mb-2">
                    <option value="1">Available</option>
                    <option value="0">Occupied</option>
                </select>

                <button type="submit" name="update" class="bg-green-500 px-4 py-2 text-white rounded">Update</button>
                <button type="button" onclick="closeModal()" class="bg-red-500 px-4 py-2 text-white rounded">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function fillForm(room) {
            document.getElementById('editId').value = room.id;
            document.getElementById('editFloor').value = room.floor;
            document.getElementById('editRoomNumber').value = room.room_number;
            document.getElementById('editCapacity').value = room.capacity;
            document.getElementById('editPrice').value = room.price_per_night;
            document.getElementById('editRoomTypeId').value = room.room_type_id;
            document.getElementById('editStatus').value = room.status;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>
