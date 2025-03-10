<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "task_fusion";

// Create a new connection with PDO (PHP Data Object)
try{
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Database connection established";
} catch (PDOException $e){
    // echo "Connection failes: " . $e->getMessage();
    die("Connection failed: " . $e->getMessage());
}
?>