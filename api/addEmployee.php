<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Karena mengirim FormData (karena ada file upload), kita menggunakan $_POST alih-alih json_decode php://input
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$department = mysqli_real_escape_string($conn, $_POST['department'] ?? '');
$position = mysqli_real_escape_string($conn, $_POST['position'] ?? '');
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

if (empty($name) || empty($email) || empty($department) || empty($position) || empty($status)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit();
}

// Proses Upload Foto
$photoName = NULL;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $fileTmpPath = $_FILES['photo']['tmp_name'];
    $fileName = $_FILES['photo']['name'];
    $fileSize = $_FILES['photo']['size'];
    $fileType = mime_content_type($fileTmpPath); // Lebih aman daripada $_FILES['type']
    
    // Cek ekstensi & ukuran (Maks 2MB)
    if (in_array($fileType, $allowedTypes) && $fileSize < 2000000) { 
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('emp_') . '.' . $extension;
        $uploadDir = '../assets/uploads/';
        $destPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $photoName = $newFileName;
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Format foto tidak valid atau ukuran terlalu besar (Max 2MB)"]);
        exit();
    }
}

$photoSql = $photoName ? "'$photoName'" : "NULL";
$sql = "INSERT INTO employees (name, email, department, position, status, photo) VALUES ('$name', '$email', '$department', '$position', '$status', $photoSql)";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["success" => true, "message" => "Data berhasil ditambahkan"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
}
