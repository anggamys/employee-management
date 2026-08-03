document.addEventListener("DOMContentLoaded", () => {
    loadEmployees();

    const form = document.getElementById('employeeForm');
    
    // Logika validasi form kustom
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Hapus class validasi aktif terlebih dahulu
        form.classList.remove('was-validated');
        
        // Periksa apakah form valid menggunakan HTML5 validation API
        if (!form.checkValidity()) {
            e.stopPropagation();
            // Tambahkan class yang memicu state CSS invalid kita
            form.classList.add('was-validated');
            
            // Fokus pada input invalid pertama
            const firstInvalid = form.querySelector(':invalid');
            if(firstInvalid) firstInvalid.focus();
            
            return;
        }
        
        saveEmployee();
    });

    // Umpan balik validasi real-time saat pengguna mengetik
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            if (form.classList.contains('was-validated')) {
                // Jika sudah pernah disubmit sekali, tampilkan validasi langsung saat mengetik
                input.checkValidity();
            }
        });
    });
});

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
        
        // Simpan data di global window object untuk mempermudah edit tanpa fetch ulang
        window.employeesData = data; 

        let rows = '';
        data.forEach(emp => {
            const statusClass = emp.status === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif';
            
            rows += `<tr>
                <td>${emp.id}</td>
                <td style="font-weight: 500;">${emp.name}</td>
                <td>${emp.email}</td>
                <td>${emp.department}</td>
                <td>${emp.position}</td>
                <td><span class="badge ${statusClass}">${emp.status}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-secondary btn-sm" onclick="openEditModal(${emp.id})">Edit</button>
                        <button class="btn-danger btn-sm" onclick="deleteEmployee(${emp.id})">Delete</button>
                    </div>
                </td>
            </tr>`;
        });
        document.querySelector('#employeeTable tbody').innerHTML = rows;
    } catch (error) {
        console.error("Error memuat pegawai:", error);
        Swal.fire('Error', 'Gagal memuat data pegawai', 'error');
    }
}

function openAddModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Pegawai';
    const form = document.getElementById('employeeForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('emp_id').value = '';
    
    // Reset state pilihan
    document.getElementById('emp_status').value = "";
    
    document.getElementById('employeeModal').style.display = 'block';
}

function openEditModal(id) {
    document.getElementById('modalTitle').innerText = 'Edit Pegawai';
    const form = document.getElementById('employeeForm');
    form.classList.remove('was-validated');
    
    // Ambil data langsung dari variabel tanpa fetch ke server lagi
    const emp = window.employeesData.find(e => e.id == id);
    if (emp) {
        document.getElementById('emp_id').value = emp.id;
        document.getElementById('emp_name').value = emp.name;
        document.getElementById('emp_email').value = emp.email;
        document.getElementById('emp_department').value = emp.department;
        document.getElementById('emp_position').value = emp.position;
        document.getElementById('emp_status').value = emp.status;
        
        document.getElementById('employeeModal').style.display = 'block';
    }
}

function closeModal() {
    document.getElementById('employeeModal').style.display = 'none';
}

async function saveEmployee() {
    const id = document.getElementById('emp_id').value;
    const isEdit = id !== '';
    
    const payload = {
        name: document.getElementById('emp_name').value,
        email: document.getElementById('emp_email').value,
        department: document.getElementById('emp_department').value,
        position: document.getElementById('emp_position').value,
        status: document.getElementById('emp_status').value
    };
    
    let url = '../api/addEmployee.php';
    if (isEdit) {
        payload.id = id;
        url = '../api/updateEmployee.php';
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
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
            loadEmployees();
        } else {
            Swal.fire('Error', data.message || 'Gagal menyimpan data', 'error');
        }
    } catch (error) {
        console.error("Error menyimpan pegawai:", error);
        Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
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
