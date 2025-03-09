<?php
// middleware.php
session_start();
include('../db_conn.php');
include('../config.php');

function isSuperAdminLoggedIn() {
    // Check if super admin is logged in using session
    if (!isset($_SESSION['super_admin_id'])) {
        // Redirect to login page if not logged in
        header('Location: login.php');
        exit();
    }

    // Fetch Super Admin Info to use if needed
    global $conn;
    $superAdminId = $_SESSION['super_admin_id'];
    $stmt = $conn->prepare("SELECT name, email FROM super_admins WHERE id = ?");
    $stmt->bind_param("i", $superAdminId);
    $stmt->execute();
    $stmt->bind_result($superAdminName, $superAdminEmail);
    $stmt->fetch();
    $stmt->close();

    // If no super admin found, redirect
    if (!$superAdminName || !$superAdminEmail) {
        header('Location: login.php');
        exit();
    }

    // Return the super admin info for later use
    return ['name' => $superAdminName, 'email' => $superAdminEmail];
}
?>
