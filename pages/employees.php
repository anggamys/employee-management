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
    <title>Data Pegawai - Employee Management</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- SweetAlert2 (Opsional, tapi direkomendasikan) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <h2>Data Pegawai</h2>
        <button class="btn-primary" onclick="openAddModal()" style="margin-top: 15px;">+ Tambah Pegawai</button>
        
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

    <!-- Modal Form Pegawai -->
    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle">Tambah Pegawai</h3>
            <form id="employeeForm">
                <input type="hidden" id="emp_id">
                
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" id="emp_name" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="emp_email" required>
                </div>
                
                <div class="form-group">
                    <label>Divisi</label>
                    <input type="text" id="emp_department" required>
                </div>
                
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" id="emp_position" required>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select id="emp_status" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%">Simpan</button>
            </form>
        </div>
    </div>

    <script src="../js/employee.js"></script>
</body>
</html>
