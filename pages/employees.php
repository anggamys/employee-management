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
    <title>Data Pegawai - Employee Management</title>
    <!-- Tambahkan Google Fonts untuk UI yang lebih modern -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Employee Management</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="employees.php" class="active">Data Pegawai</a>
                <a href="../api/logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h2>Data Pegawai</h2>
            <button class="btn-primary" onclick="openAddModal()">+ Tambah Pegawai</button>
        </div>
        
        <div class="card">
            <div class="table-responsive">
                <table id="employeeTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Divisi</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat via JS / Fetch API -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form Pegawai -->
    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Pegawai</h3>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="employeeForm" novalidate>
                    <input type="hidden" id="emp_id">
                    
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" id="emp_name" required minlength="3" pattern="^[a-zA-Z\s]+$" placeholder="Masukkan nama lengkap">
                        <div class="invalid-feedback">Nama wajib diisi dan hanya boleh berisi huruf dan spasi (min. 3 karakter).</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="emp_email" required placeholder="email@perusahaan.com">
                        <div class="invalid-feedback">Masukkan format email yang valid (contoh: user@domain.com).</div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Divisi</label>
                            <input type="text" id="emp_department" required minlength="2" placeholder="Contoh: IT, HR">
                            <div class="invalid-feedback">Divisi wajib diisi (min. 2 karakter).</div>
                        </div>
                        
                        <div class="form-group">
                            <label>Jabatan</label>
                            <input type="text" id="emp_position" required minlength="2" placeholder="Contoh: Staff">
                            <div class="invalid-feedback">Jabatan wajib diisi (min. 2 karakter).</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select id="emp_status" required>
                            <option value="" disabled selected>Pilih Status...</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                        <div class="invalid-feedback">Silakan pilih status pegawai.</div>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan Data Pegawai</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/employee.js"></script>
</body>
</html>
