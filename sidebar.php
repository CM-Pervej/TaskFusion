<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <aside class="w-64 bg-blue-100 text-black py-5 pl-5 min-h-screen fixed">
        <h2 class="text-2xl font-bold mb-5 text-blue-900 font-">Paaru Royal Resort</h2>
        <ul class="text-lg text-right">
            <!-- Dashboard Menu -->
            <li><a href="/taskfusion/dashboard.php" class="block py-2 px-7 hover:bg-blue-700 hover:text-white">Dashboard</a></li>

            <!-- Manage Employee Menu -->
            <li class="relative group">
                <a href="#" class="block py-2 px-7 hover:border-r hover:border-black hover:bg-blue-700 hover:text-white">Manage Employee</a>
                <ul class="absolute left-60 -ml-1 top-0 hidden group-hover:block bg-blue-700 w-max text-left border-l border-blue-700">
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Manage Employee</a></li>
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Add Role</a></li>
                </ul>
            </li>

            <!-- Manage Rooms Menu -->
            <li class="relative group">
                <a href="#" class="block py-2 px-7 hover:border-r hover:border-black hover:bg-blue-700 hover:text-white">Manage Rooms</a>
                <ul class="absolute left-60 -ml-1 top-0 hidden group-hover:block bg-blue-700 w-max text-left border-l border-blue-700">
                    <li><a href="/taskfusion/rooms/all_rooms.php" class="block text-white py-2 px-7 hover:bg-black">Manage Rooms</a></li>
                    <li><a href="/taskfusion/rooms/all_rooms.php?room_status=0" class="block text-white py-2 px-7 hover:bg-black">Available Rooms</a></li>
                    <li><a href="/taskfusion/rooms/all_rooms.php?room_status=1" class="block text-white py-2 px-7 hover:bg-black">Unavailable Rooms</a></li>
                </ul>
            </li>

            <!-- View Bookings Menu -->
            <li class="relative group">
                <a href="#" class="block py-2 px-7 hover:border-r hover:border-black hover:bg-blue-700 hover:text-white">View Bookings</a>
                <ul class="absolute left-60 -ml-1 top-0 hidden group-hover:block bg-blue-700 w-max text-left border-l border-blue-700">
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">View Bookings</a></li>
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Check-in</a></li>
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Check-out</a></li>
                </ul>
            </li>

            <!-- Hotel Services Menu -->
            <li class="relative group">
                <a href="#" class="block py-2 px-7 hover:border-r hover:border-black hover:bg-blue-700 hover:text-white">Hotel Services</a>
                <ul class="absolute left-60 -ml-1 top-0 hidden group-hover:block bg-blue-700 w-max text-left border-l border-blue-700">
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Spa Services</a></li>
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Gym Services</a></li>
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Restaurant</a></li>
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Room Services</a></li>
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Additional Services (Staff, Towel)</a></li>
                    <li><a href="#" class="block text-white py-2 px-7 hover:bg-black">Business and Meeting Services</a></li>
                </ul>
            </li>
        </ul>
    </aside>
</body>
</html>
