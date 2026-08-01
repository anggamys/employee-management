<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Employee Management</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Employee Management</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="employees.php">Data Pegawai</a>
                <a href="../api/logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
        <p>Silakan navigasi ke menu Data Pegawai untuk mengelola data.</p>
        <br>
        <a href="employees.php" class="btn-primary" style="text-decoration:none; display:inline-block;">Kelola Data Pegawai</a>
    </div>
</body>
</html>
