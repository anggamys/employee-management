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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Employee Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Employee Management</h1>
            <nav>
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="employees.php">Data Pegawai</a>
                <a href="../api/logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h2>Dashboard</h2>
        </div>
        
        <div class="card" style="margin-bottom: 25px;">
            <h3 style="color: #0f172a; margin-bottom: 10px;">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h3>
            <p style="color: #475569;">Sistem Manajemen Pegawai saat ini sedang berjalan dengan baik. Anda memiliki akses penuh untuk menambah, mengubah, dan menghapus data.</p>
        </div>

        <div class="dashboard-summary">
            <div class="summary-card">
                <h3>Total Pegawai Terdaftar</h3>
                <div class="number" id="totalEmployees">--</div>
            </div>
            <div class="summary-card" style="border-top-color: #10b981;">
                <h3>Pegawai Aktif</h3>
                <div class="number" id="activeEmployees">--</div>
            </div>
            <div class="summary-card" style="border-top-color: #ef4444;">
                <h3>Pegawai Nonaktif</h3>
                <div class="number" id="inactiveEmployees">--</div>
            </div>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="employees.php" class="btn-primary" style="text-decoration:none; display:inline-block; padding: 12px 25px;">Kelola Data Pegawai &rarr;</a>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", async () => {
            try {
                const res = await fetch('../api/getEmployees.php');
                if(res.ok) {
                    const data = await res.json();
                    
                    const total = data.length;
                    const active = data.filter(e => e.status === 'Aktif').length;
                    const inactive = data.filter(e => e.status === 'Nonaktif').length;

                    document.getElementById('totalEmployees').innerText = total;
                    document.getElementById('activeEmployees').innerText = active;
                    document.getElementById('inactiveEmployees').innerText = inactive;
                }
            } catch (e) {
                console.error("Gagal mengambil summary data", e);
            }
        });
    </script>
</body>
</html>
