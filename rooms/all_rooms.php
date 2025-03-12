<?php
session_start(); // Start the session
session_regenerate_id(true);
require '../middleware.php'; // Include the middleware to check authentication
$userData = checkAuth(); // This will verify JWT from session and return user data
include '../db_conn.php'; // Database connection

// Fetch room types
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

    // Check if 'room_status' parameter is set
    $roomStatus = isset($_GET['room_status']) ? (int)$_GET['room_status'] : null;

    // Fetch rooms based on status (if set)
    if ($roomStatus !== null) {
        $sql = "SELECT * FROM rooms WHERE status = :status";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':status' => $roomStatus]);
    } else {
        // Fetch all rooms if no status parameter is set
        $sql = "SELECT * FROM rooms";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
}

// ✅ Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
    $userData = checkAuth(); // Ensure the user is authenticated

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

// ✅ Handle Delete
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete_id'])) {
    $userData = checkAuth(); // Ensure the user is authenticated

    $delete_id = $_GET['delete_id'];

    if (!$delete_id) {
        die("Invalid room ID");
    }

    try {
        // Check if the room exists before attempting deletion
        $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ? AND status = 0");
        $stmt->execute([$delete_id]);
        if (!$stmt->fetch()) {
            die("Room not found or cannot be deleted (status is not 0).");
        }

        // Delete the room
        $sql = "DELETE FROM rooms WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $delete_id]);

        header("Location: all_rooms.php?success=Room deleted successfully");
        exit();
    } catch (PDOException $e) {
        die("Error deleting room: " . $e->getMessage());
    }
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
<body class="flex min-h-screen bg-white overflow-hidden">
    <section class="z-20">
        <?php include "../sidebar.php"; ?>
    </section>

    <main class="flex flex-col gap-10 p-6 ml-64 my-10 w-full">
        <div class="mb-4 pr-72 pb-2 pl-5 pt-5 w-full flex fixed left-64 top-0 right-0 z-10 bg-white">
            <h2 class="text-2xl font-semibold text-left">Rooms Information</h2>
            <div class="flex-1 px-4">
                <input type="text" id="search" class="p-3 w-full rounded-md border border-gray-300" placeholder="Search for rooms...">
            </div>
            <h2 class="text-2xl font-semibold text-left">Add Room</h2>
        </div>

        <div class="max-h-screen mt-2 pb-32 overflow-x-auto">
            <table class="table table-pin-rows table-pin-cols border-collapse">
                <thead class="sticky top-0 bottom-14 bg-white">
                    <tr class="text-center">
                        <th class=" border border-gray-300">SL</th>
                        <th class=" border border-gray-300">Floor</th>
                        <th class=" border border-gray-300">Room</th>
                        <th class=" border border-gray-300">Capacity</th>
                        <th class="text-right border border-gray-300">Price per night</th>
                        <th class="text-left border border-gray-300">Room Type</th>
                        <th class=" border border-gray-300">Status</th>
                        <th class=" border border-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody id="roomTable">
                    <?php if (empty($rooms)): ?>
                        <tr><td colspan="7" class="text-center py-4">No Rooms Found</td></tr>
                    <?php else: ?>
                        <?php $SL = 1; foreach ($rooms as $room): ?>
                            <tr class="text-lg text-center">
                                <td class="border border-gray-300 font-semibold"><?= $SL++; ?></td>
                                <td class="border border-gray-300 font-semibold"><?= $room['floor']; ?></td>
                                <td class="border border-gray-300 font-semibold"><?= $room['room_number']; ?></td>
                                <td class="border border-gray-300 font-semibold"><?= $room['capacity']; ?></td>
                                <td class="border border-gray-300 font-semibold text-right"><?= $room['price_per_night']; ?></td>
                                <td class="border border-gray-300 font-semibold text-left"><?= isset($roomTypeMap[$room['room_type_id']]) ? $roomTypeMap[$room['room_type_id']] : 'Unknown'; ?></td>
                                <td class="border border-gray-300 font-semibold <?= $room['status'] == 1 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?= $room['status'] == 0 ? 'Available' : 'Booked'; ?>
                                </td>
                                <td class="border border-gray-300 p-0">
                                    <button onclick="fillForm(<?= htmlspecialchars(json_encode($room)); ?>)" class="btn btn-outline btn-primary m-0">Edit</button>
                                    <a href="?delete_id=<?= $room['id']; ?>" class="delete btn btn-outline btn-secondary m-0" onclick="return confirm('Are you sure you want to delete this room?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    
       <!-- Update Modal -->
        <div id="editModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-30">
            <div class="bg-white p-5 rounded shadow-lg w-96">
                <h3 class="text-xl font-semibold mb-3">Update Room</h3>
                <form method="post" class="">
                    <!-- Hidden CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                    
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
                        <option value="1">Booked</option>
                        <option value="0">Available</option>
                    </select>

                    <button type="submit" name="update" class="bg-green-500 px-4 py-2 text-blue-700 rounded">Update</button>
                    <button type="button" onclick="closeModal()" class="bg-red-500 px-4 py-2 text-red-700 rounded">Cancel</button>
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

        <script>
            // Simple search function
            document.getElementById('search').addEventListener('input', function(e) {
                const searchQuery = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#roomTable tr');

                rows.forEach(row => {
                    const columns = row.querySelectorAll('td');
                    let match = false;

                    columns.forEach(column => {
                        if (column.innerText.toLowerCase().includes(searchQuery)) {
                            match = true;
                        }
                    });

                    row.style.display = match ? '' : 'none';
                });
            });
        </script>
</body>
</html>
