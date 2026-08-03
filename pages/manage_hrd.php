<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect HRD kembali ke dashboard jika mencoba mengakses URL ini secara manual
if ($_SESSION['admin_role'] !== 'superadmin') {
    header("Location: dashboard.php");
    exit();
}

$role_display = 'Super Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun HRD - Employee Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Employee Management</h1>
            <nav style="display: flex; align-items: center;">
                <a href="dashboard.php">Dashboard</a>
                <a href="employees.php">Data Pegawai</a>
                <a href="manage_hrd.php" class="active">Kelola HRD</a>
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
                <h2>Kelola Akun Staff HRD</h2>
                <p>Tambah atau hapus akses login untuk Staff HRD.</p>
            </div>
            <button class="btn-primary" onclick="openAddHrdModal()">+ Tambah Akun HRD</button>
        </div>
        
        <div class="card">
            <div class="table-responsive">
                <table id="hrdTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="hrdTableBody">
                        <!-- Data dimuat via JS -->
                    </tbody>
                </table>
                <div id="emptyHrdState" class="empty-state hidden">
                    <h3 style="color: #0f172a; margin-bottom: 5px;">Belum Ada Akun HRD</h3>
                    <p>Silakan klik tombol Tambah Akun HRD di atas.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah HRD -->
    <div id="hrdModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Tambah Akun HRD</h3>
                <span class="close-modal" onclick="closeHrdModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="hrdForm" novalidate>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="hrd_username" required minlength="3" placeholder="Masukkan username unik">
                        <div class="invalid-feedback">Username wajib diisi (min. 3 karakter).</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="hrd_password" required minlength="5" placeholder="Minimal 5 karakter">
                        <div class="invalid-feedback">Password wajib diisi (min. 5 karakter).</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" id="hrd_password_confirm" required>
                        <div class="invalid-feedback" id="passwordMatchFeedback">Password harus cocok.</div>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;" id="btnSubmitHrd">Simpan Akun</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/manage_hrd.js"></script>
</body>
</html>
