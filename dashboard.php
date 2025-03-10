<?php
session_start(); // Start the session

require 'middleware.php'; // Include the middleware to check authentication

// Check if the user is authenticated by checking the session
$userData = checkAuth(); // This will verify JWT from session and return user data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paaru Royal Resort Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="flex min-h-screen bg-white">
    <section class="z-10">
        <?php include "sidebar.php"; ?>
    </section>

    <!-- Main Content -->
    <main class="flex-1 p-6 ml-64">
        <div class="flex justify-between items-center bg-white p-4 rounded shadow mb-5">
            <h1 class="text-xl font-semibold">Welcome, <?php echo htmlspecialchars($userData['email']); ?></h1>
            <p>Your role is: <?php echo htmlspecialchars($userData['role']); ?></p>
            <button class="btn btn-primary" onclick="window.location.href='logout.php'">Logout</button>
        </div>

        <!-- Dashboard Content -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-5 rounded shadow">
                <h3 class="text-lg font-semibold">Total Bookings</h3>
                <p class="text-2xl font-bold">120</p>
            </div>
            <div class="bg-white p-5 rounded shadow">
                <h3 class="text-lg font-semibold">Upcoming</h3>
                <p class="text-2xl font-bold text-green-500">5</p>
            </div>
            <div class="bg-white p-5 rounded shadow">
                <h3 class="text-lg font-semibold">Pending Payments</h3>
                <p class="text-2xl font-bold text-red-500">3</p>
            </div>
        </div>

        <!-- Recent Bookings or Stats -->
            <div class="bg-white p-5 rounded shadow">
                <h2 class="text-lg font-semibold mb-3">Recent Bookings</h2>
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Fetch and display booking data here -->
                        <tr>
                            <td>12345</td>
                            <td>John Doe</td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td><span class="badge badge-success">Paid</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-white p-5 rounded shadow">
                <h2 class="text-lg font-semibold mb-3">Your Bookings</h2>
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Status</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Fetch and display user's bookings -->
                        <tr>
                            <td>Room 101</td>
                            <td><span class="badge badge-warning">Pending</span></td>
                            <td>March 5, 2025</td>
                            <td>March 10, 2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </main>
</body>
</html>
