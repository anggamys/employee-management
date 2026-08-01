<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $id = (int)$data['id'];

    $sql = "DELETE FROM employees WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Data berhasil dihapus"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
}
