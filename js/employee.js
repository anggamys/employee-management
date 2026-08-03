let userRole = 'hrd'; // Default fallback

document.addEventListener("DOMContentLoaded", async () => {
    // Ambil role user terlebih dahulu
    try {
        const roleRes = await fetch('../api/getRole.php');
        if (roleRes.ok) {
            const roleData = await roleRes.json();
            userRole = roleData.role;
        }
    } catch (e) {
        console.error("Gagal memuat role", e);
    }

    loadEmployees();

    const form = document.getElementById('employeeForm');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        form.classList.remove('was-validated');

        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');

            const firstInvalid = form.querySelector(':invalid');
            if(firstInvalid) firstInvalid.focus();

            return;
        }

        saveEmployee();
    });

    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            if (form.classList.contains('was-validated')) {
                input.checkValidity();
            }
        });
    });

    // Fitur Live Search
    document.getElementById('searchInput').addEventListener('input', renderTable);

    // Fitur Filter Status
    document.getElementById('statusFilter').addEventListener('change', renderTable);
});

let currentSortColumn = 'id';
let isAscending = true;

async function loadEmployees() {
    try {
        const res = await fetch('../api/getEmployees.php');
        if (!res.ok) {
            if (res.status === 401) {
                window.location.href = 'login.php';
                return;
            }
            throw new Error('Gagal memuat data');
        }
        const data = await res.json();
        window.employeesData = data;

        renderTable();
    } catch (error) {
        console.error("Error memuat pegawai:", error);
        Swal.fire('Error', 'Gagal memuat data pegawai', 'error');
    }
}

function renderTable() {
    if (!window.employeesData) return;

    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;

    // Filter data berdasarkan search dan status
    let filteredData = window.employeesData.filter(emp => {
        const matchSearch = emp.name.toLowerCase().includes(searchTerm) ||
                            emp.department.toLowerCase().includes(searchTerm) ||
                            emp.email.toLowerCase().includes(searchTerm);
        const matchStatus = statusFilter === 'all' || emp.status === statusFilter;
        return matchSearch && matchStatus;
    });

    // Urutkan (Sorting) data
    filteredData.sort((a, b) => {
        let valA = a[currentSortColumn];
        let valB = b[currentSortColumn];

        // Ubah ID menjadi angka agar pengurutan benar
        if (currentSortColumn === 'id') {
            valA = parseInt(valA);
            valB = parseInt(valB);
        } else {
            valA = valA.toLowerCase();
            valB = valB.toLowerCase();
        }

        if (valA < valB) return isAscending ? -1 : 1;
        if (valA > valB) return isAscending ? 1 : -1;
        return 0;
    });

    const tbody = document.getElementById('tableBody');
    const emptyState = document.getElementById('emptyState');
    const table = document.getElementById('employeeTable');

    if (filteredData.length === 0) {
        tbody.innerHTML = '';
        table.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }

    table.style.display = 'table';
    emptyState.style.display = 'none';

    let rows = '';
    filteredData.forEach(emp => {
        const statusClass = emp.status === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif';
        // Tampilkan foto profil default jika null
        const photoUrl = emp.photo ? `../assets/uploads/${emp.photo}` : 'https://ui-avatars.com/api/?background=e2e8f0&color=475569&name=' + encodeURIComponent(emp.name);

        // Sembunyikan tombol delete jika role bukan superadmin
        const deleteButtonHtml = userRole === 'superadmin'
            ? `<button class="btn-danger btn-sm" onclick="deleteEmployee(${emp.id})">Delete</button>`
            : '';

        rows += `<tr>
            <td>${emp.id}</td>
            <td>
                <div class="user-info">
                    <img src="${photoUrl}" alt="Photo" class="user-avatar">
                    <span style="font-weight: 500;">${emp.name}</span>
                </div>
            </td>
            <td>${emp.email}</td>
            <td>${emp.department}</td>
            <td>${emp.position}</td>
            <td><span class="badge ${statusClass}">${emp.status}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="btn-secondary btn-sm" onclick="openEditModal(${emp.id})">Edit</button>
                    ${deleteButtonHtml}
                </div>
            </td>
        </tr>`;
    });
    tbody.innerHTML = rows;
}

function sortTable(column) {
    if (currentSortColumn === column) {
        isAscending = !isAscending; // Balikkan arah urutan
    } else {
        currentSortColumn = column;
        isAscending = true;
    }
    renderTable();
}

function previewImage(input) {
    const preview = document.getElementById('photoPreview');
    const placeholder = document.getElementById('photoPlaceholder');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
        placeholder.style.display = 'flex';
    }
}

function openAddModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Pegawai';
    const form = document.getElementById('employeeForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('emp_id').value = '';
    document.getElementById('emp_status').value = "";

    // Reset pratinjau gambar
    document.getElementById('photoPreview').style.display = 'none';
    document.getElementById('photoPlaceholder').style.display = 'flex';
    document.getElementById('emp_photo').value = "";

    document.getElementById('employeeModal').style.display = 'block';
}

function openEditModal(id) {
    document.getElementById('modalTitle').innerText = 'Edit Pegawai';
    const form = document.getElementById('employeeForm');
    form.classList.remove('was-validated');

    const emp = window.employeesData.find(e => e.id == id);
    if (emp) {
        document.getElementById('emp_id').value = emp.id;
        document.getElementById('emp_name').value = emp.name;
        document.getElementById('emp_email').value = emp.email;
        document.getElementById('emp_department').value = emp.department;
        document.getElementById('emp_position').value = emp.position;
        document.getElementById('emp_status').value = emp.status;

        // Setup pratinjau gambar untuk edit
        const preview = document.getElementById('photoPreview');
        const placeholder = document.getElementById('photoPlaceholder');
        if (emp.photo) {
            preview.src = `../assets/uploads/${emp.photo}`;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        } else {
            preview.src = '';
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
        }
        document.getElementById('emp_photo').value = "";

        document.getElementById('employeeModal').style.display = 'block';
    }
}

function closeModal() {
    document.getElementById('employeeModal').style.display = 'none';
}

async function saveEmployee() {
    const btn = document.getElementById('btnSubmit');
    const originalText = btn.innerText;
    btn.innerText = 'Menyimpan...';
    btn.disabled = true;

    const id = document.getElementById('emp_id').value;
    const isEdit = id !== '';
    const form = document.getElementById('employeeForm');

    // Menggunakan FormData untuk mendukung File Upload
    const formData = new FormData(form);

    let url = '../api/addEmployee.php';
    if (isEdit) {
        url = '../api/updateEmployee.php';
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            Swal.fire({
                title: 'Sukses',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            closeModal();
            loadEmployees(); // Ambil ulang data terbaru
        } else {
            Swal.fire('Error', data.message || 'Gagal menyimpan data', 'error');
        }
    } catch (error) {
        console.error("Error menyimpan pegawai:", error);
        Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
}

function deleteEmployee(id) {
    Swal.fire({
        title: 'Yakin hapus data ini?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                // Untuk hapus tetap pakai JSON (karena tidak ada file upload)
                const res = await fetch('../api/deleteEmployee.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadEmployees();
                } else {
                    Swal.fire('Error', data.message || 'Gagal menghapus data', 'error');
                }
            } catch (error) {
                console.error("Error menghapus pegawai:", error);
                Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
            }
        }
    });
}
