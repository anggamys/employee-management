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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
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
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="Cari nama pegawai...">
                </div>
                <select id="statusFilter" style="padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: #fff; outline: none;">
                    <option value="all">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
                <button class="btn-primary" onclick="openAddModal()">+ Tambah Pegawai</button>
            </div>
        </div>
        
        <div class="card">
            <div class="table-responsive">
                <table id="employeeTable">
                    <thead>
                        <tr>
                            <th style="cursor: pointer;" onclick="sortTable('id')">ID ⇕</th>
                            <th style="cursor: pointer;" onclick="sortTable('name')">Pegawai ⇕</th>
                            <th>Email</th>
                            <th style="cursor: pointer;" onclick="sortTable('department')">Divisi ⇕</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data dimuat via JS -->
                    </tbody>
                </table>
                <div id="emptyState" class="empty-state hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                    </svg>
                    <h3 style="color: #0f172a; margin-bottom: 5px;">Data Tidak Ditemukan</h3>
                    <p>Tidak ada data pegawai yang cocok dengan kriteria saat ini.</p>
                </div>
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
                <!-- Kita hapus id="employeeForm" dari tag form, JS akan catch onSubmit form ini -->
                <form id="employeeForm" novalidate enctype="multipart/form-data">
                    <input type="hidden" id="emp_id" name="id">
                    
                    <div class="form-group" style="text-align: center; margin-bottom: 25px;">
                        <img id="photoPreview" src="../assets/default-avatar.png" alt="Preview" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 3px solid #e2e8f0; display: none;">
                        <div id="photoPlaceholder" style="width: 100px; height: 100px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; border: 3px dashed #cbd5e1; color: #94a3b8;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <input type="file" id="emp_photo" name="photo" accept="image/jpeg, image/png, image/jpg" style="font-size: 13px;" onchange="previewImage(this)">
                        <div style="font-size: 12px; color: #64748b; margin-top: 5px;">Format JPG/PNG. Maksimal 2MB.</div>
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" id="emp_name" name="name" required minlength="3" pattern="^[a-zA-Z\s]+$" placeholder="Masukkan nama lengkap">
                        <div class="invalid-feedback">Nama wajib diisi dan hanya boleh berisi huruf (min. 3 karakter).</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="emp_email" name="email" required placeholder="email@perusahaan.com">
                        <div class="invalid-feedback">Masukkan format email yang valid.</div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Divisi</label>
                            <input type="text" id="emp_department" name="department" required minlength="2" placeholder="Contoh: IT, HR">
                            <div class="invalid-feedback">Divisi wajib diisi (min. 2 karakter).</div>
                        </div>
                        
                        <div class="form-group">
                            <label>Jabatan</label>
                            <input type="text" id="emp_position" name="position" required minlength="2" placeholder="Contoh: Staff">
                            <div class="invalid-feedback">Jabatan wajib diisi (min. 2 karakter).</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select id="emp_status" name="status" required>
                            <option value="" disabled selected>Pilih Status...</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                        <div class="invalid-feedback">Silakan pilih status pegawai.</div>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;" id="btnSubmit">Simpan Data Pegawai</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/employee.js"></script>
</body>
</html>
