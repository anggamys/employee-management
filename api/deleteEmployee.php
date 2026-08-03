<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Proteksi API: Hanya superadmin yang boleh menghapus
if ($_SESSION['admin_role'] !== 'superadmin') {
    http_response_code(403); // Forbidden
    echo json_encode(["success" => false, "message" => "Akses Ditolak: Hanya Super Admin yang dapat menghapus data."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $id = (int)$data['id'];

    // Ambil info foto sebelum dihapus
    $checkSql = "SELECT photo FROM employees WHERE id = $id";
    $res = mysqli_query($conn, $checkSql);
    $photoToDelete = null;
    if ($row = mysqli_fetch_assoc($res)) {
        $photoToDelete = $row['photo'];
    }

    $sql = "DELETE FROM employees WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        // Hapus file foto jika ada
        if ($photoToDelete && file_exists('../assets/uploads/' . $photoToDelete)) {
            unlink('../assets/uploads/' . $photoToDelete);
        }
        
        echo json_encode(["success" => true, "message" => "Data berhasil dihapus"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
}
