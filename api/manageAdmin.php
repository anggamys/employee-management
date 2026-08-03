<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Proteksi API: Hanya superadmin yang boleh manajemen admin/HRD
if ($_SESSION['admin_role'] !== 'superadmin') {
    http_response_code(403); // Forbidden
    echo json_encode(["success" => false, "message" => "Akses Ditolak: Hanya Super Admin yang dapat mengakses menu ini."]);
    exit();
}

// Menangani GET Request (Ambil daftar admin)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = mysqli_query($conn, "SELECT id, username, role FROM admin WHERE role = 'hrd' ORDER BY id DESC");
    $admins = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($admins);
    exit();
}

// Menangani POST Request (Tambah atau Hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // HAPUS HRD
    if (isset($data['action']) && $data['action'] === 'delete') {
        if (isset($data['id'])) {
            $id = (int)$data['id'];
            // Jangan biarkan superadmin menghapus dirinya sendiri atau superadmin lain dari endpoint ini
            $sql = "DELETE FROM admin WHERE id=$id AND role='hrd'";
            if (mysqli_query($conn, $sql)) {
                echo json_encode(["success" => true, "message" => "Akun HRD berhasil dihapus"]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "ID tidak valid"]);
        }
        exit();
    }
    
    // TAMBAH HRD
    if (isset($data['action']) && $data['action'] === 'add') {
        if (isset($data['username']) && isset($data['password'])) {
            $username = mysqli_real_escape_string($conn, $data['username']);
            $password = $data['password']; // Jangan di-escape, akan di-hash
            
            // Validasi input
            if (strlen($username) < 3 || strlen($password) < 5) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Username minimal 3 karakter dan password minimal 5 karakter"]);
                exit();
            }
            
            // Cek apakah username sudah ada
            $check = mysqli_query($conn, "SELECT id FROM admin WHERE username = '$username'");
            if (mysqli_num_rows($check) > 0) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Username sudah digunakan"]);
                exit();
            }
            
            // HASH PASSWORD
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO admin (username, password, role) VALUES ('$username', '$hashedPassword', 'hrd')";
            if (mysqli_query($conn, $sql)) {
                echo json_encode(["success" => true, "message" => "Akun HRD baru berhasil dibuat"]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
        }
        exit();
    }
}
