<?php
require_once 'middleware.php';

if ($_SESSION['role_id'] != 1) {
    echo json_encode(["error" => "Access denied"]);
    http_response_code(403);
    exit();
}
?>
