<?php
// When connecting to MySQL via TCP/IP (which is required when MySQL is in a Docker container
// and PHP is running on the host), use 127.0.0.1 instead of localhost.
// 'localhost' forces PHP to use Unix sockets which won't work across the Docker boundary.
$host = "127.0.0.1";
$user = "root";
$pass = "e1eb7b675299d78233d63a8d18b8f3728b55b7e11cf529fa6461404abaf4ce5c";
$db = "db_employee_management";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
