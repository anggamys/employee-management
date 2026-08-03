<?php
// Ketika terhubung ke MySQL via TCP/IP (yang dibutuhkan saat MySQL ada di dalam Docker container
// dan PHP berjalan di host), gunakan 127.0.0.1 alih-alih localhost.
// 'localhost' memaksa PHP untuk menggunakan Unix sockets yang tidak akan bekerja lintas batas Docker.
$host = "127.0.0.1";
$user = "root";
$pass = "e1eb7b675299d78233d63a8d18b8f3728b55b7e11cf529fa6461404abaf4ce5c";
$db = "db_employee_management";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
