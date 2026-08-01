<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM employees ORDER BY id DESC");

// Data disimpan dalam array sebelum ditampilkan
$employees = [];
while ($row = mysqli_fetch_assoc($result)) {
    $employees[] = $row;
}

header('Content-Type: application/json');
echo json_encode($employees);
