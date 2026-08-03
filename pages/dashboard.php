<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$role_display = (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin') ? 'Super Admin' : 'Staff HRD';
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
            <nav style="display: flex; align-items: center;">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="employees.php">Data Pegawai</a>
                <?php if($_SESSION['admin_role'] === 'superadmin'): ?>
                    <a href="manage_hrd.php">Kelola HRD</a>
                <?php endif; ?>
                <div style="margin-left: 20px; padding-left: 20px; border-left: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
                    <div style="text-align: right;">
                        <div style="font-weight: 600; font-size: 14px; color: #0f172a;"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo $role_display; ?></div>
                    </div>
                    <a href="../api/logout.php" style="margin-left: 0; padding: 6px 12px; background: #fee2e2; color: #ef4444; border-radius: 6px;">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Dashboard Overview</h2>
                <p>Ringkasan sistem manajemen data pegawai perusahaan.</p>
            </div>
        </div>
        
        <div class="card" style="margin-bottom: 30px;">
            <h3 class="welcome-text">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h3>
            <p style="color: #64748b; font-size: 15px;">Sistem beroperasi dengan normal. Anda login sebagai <strong><?php echo $role_display; ?></strong>.</p>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div class="stat-details">
                    <h3>Total Pegawai</h3>
                    <div class="number" id="totalEmployees">--</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                </div>
                <div class="stat-details">
                    <h3>Status Aktif</h3>
                    <div class="number" id="activeEmployees">--</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon danger">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="stat-details">
                    <h3>Status Nonaktif</h3>
                    <div class="number" id="inactiveEmployees">--</div>
                </div>
            </div>
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

                    // Animasi angka (opsional tapi bagus untuk UI)
                    animateValue("totalEmployees", 0, total, 1000);
                    animateValue("activeEmployees", 0, active, 1000);
                    animateValue("inactiveEmployees", 0, inactive, 1000);
                }
            } catch (e) {
                console.error("Gagal mengambil summary data", e);
            }
        });

        function animateValue(id, start, end, duration) {
            if (start === end) {
                document.getElementById(id).innerHTML = end;
                return;
            }
            let range = end - start;
            let current = start;
            let increment = end > start ? 1 : -1;
            let stepTime = Math.abs(Math.floor(duration / range));
            if (stepTime === 0) stepTime = 50; // default safeguard
            
            let obj = document.getElementById(id);
            let timer = setInterval(function() {
                current += increment;
                obj.innerHTML = current;
                if (current == end) {
                    clearInterval(timer);
                }
            }, stepTime);
        }
    </script>
</body>
</html>
