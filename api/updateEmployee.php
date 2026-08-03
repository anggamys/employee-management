<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$department = mysqli_real_escape_string($conn, $_POST['department'] ?? '');
$position = mysqli_real_escape_string($conn, $_POST['position'] ?? '');
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

if ($id === 0 || empty($name) || empty($email) || empty($department) || empty($position) || empty($status)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit();
}

// Proses Upload Foto (jika ada file baru)
$photoUpdateSql = "";
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $fileTmpPath = $_FILES['photo']['tmp_name'];
    $fileName = $_FILES['photo']['name'];
    $fileSize = $_FILES['photo']['size'];
    $fileType = mime_content_type($fileTmpPath); 
    
    if (in_array($fileType, $allowedTypes) && $fileSize < 2000000) { 
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('emp_') . '.' . $extension;
        $uploadDir = '../assets/uploads/';
        $destPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Hapus foto lama jika ada
            $checkSql = "SELECT photo FROM employees WHERE id = $id";
            $res = mysqli_query($conn, $checkSql);
            if ($row = mysqli_fetch_assoc($res)) {
                if ($row['photo'] && file_exists($uploadDir . $row['photo'])) {
                    unlink($uploadDir . $row['photo']);
                }
            }
            $photoUpdateSql = ", photo='$newFileName'";
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Format foto tidak valid atau ukuran terlalu besar (Max 2MB)"]);
        exit();
    }
}

$sql = "UPDATE employees SET name='$name', email='$email', department='$department', position='$position', status='$status' $photoUpdateSql WHERE id=$id";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["success" => true, "message" => "Data berhasil diupdate"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
}
