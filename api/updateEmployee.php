<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id']) && isset($data['name']) && isset($data['email']) && isset($data['department']) && isset($data['position']) && isset($data['status'])) {
    $id = (int)$data['id'];
    $name = mysqli_real_escape_string($conn, $data['name']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $department = mysqli_real_escape_string($conn, $data['department']);
    $position = mysqli_real_escape_string($conn, $data['position']);
    $status = mysqli_real_escape_string($conn, $data['status']);

    $sql = "UPDATE employees SET name='$name', email='$email', department='$department', position='$position', status='$status' WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Data berhasil diupdate"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
}
