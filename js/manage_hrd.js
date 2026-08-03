document.addEventListener("DOMContentLoaded", () => {
    loadHrds();

    const form = document.getElementById('hrdForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        form.classList.remove('was-validated');
        
        // Pengecekan konfirmasi password secara manual
        const pwd = document.getElementById('hrd_password').value;
        const pwdConfirm = document.getElementById('hrd_password_confirm');
        const pwdFeedback = document.getElementById('passwordMatchFeedback');
        
        if (pwd !== pwdConfirm.value) {
            e.stopPropagation();
            pwdConfirm.setCustomValidity("Password tidak cocok");
            pwdFeedback.style.display = 'block';
            form.classList.add('was-validated');
            return;
        } else {
            pwdConfirm.setCustomValidity("");
            pwdFeedback.style.display = 'none';
        }
        
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        
        saveHrd();
    });

    // Hapus pesan error konfirmasi password saat mengetik ulang
    document.getElementById('hrd_password_confirm').addEventListener('input', function() {
        this.setCustomValidity("");
        document.getElementById('passwordMatchFeedback').style.display = 'none';
    });
});

async function loadHrds() {
    try {
        const res = await fetch('../api/manageAdmin.php');
        if (!res.ok) {
            throw new Error('Gagal memuat data admin');
        }
        const data = await res.json();
        
        const tbody = document.getElementById('hrdTableBody');
        const emptyState = document.getElementById('emptyHrdState');
        const table = document.getElementById('hrdTable');

        if (data.length === 0) {
            tbody.innerHTML = '';
            table.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        table.style.display = 'table';
        emptyState.style.display = 'none';

        let rows = '';
        data.forEach(admin => {
            rows += `<tr>
                <td>${admin.id}</td>
                <td style="font-weight: 500;">${admin.username}</td>
                <td><span class="badge" style="background-color: #f1f5f9; color: #475569;">Staff HRD</span></td>
                <td>
                    <button class="btn-danger btn-sm" onclick="deleteHrd(${admin.id}, '${admin.username}')">Delete Akses</button>
                </td>
            </tr>`;
        });
        tbody.innerHTML = rows;
    } catch (error) {
        console.error("Error:", error);
        Swal.fire('Error', 'Gagal memuat data akun', 'error');
    }
}

function openAddHrdModal() {
    const form = document.getElementById('hrdForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('passwordMatchFeedback').style.display = 'none';
    document.getElementById('hrdModal').style.display = 'block';
}

function closeHrdModal() {
    document.getElementById('hrdModal').style.display = 'none';
}

async function saveHrd() {
    const btn = document.getElementById('btnSubmitHrd');
    const originalText = btn.innerText;
    btn.innerText = 'Menyimpan...';
    btn.disabled = true;

    const payload = {
        action: 'add',
        username: document.getElementById('hrd_username').value,
        password: document.getElementById('hrd_password').value
    };

    try {
        const res = await fetch('../api/manageAdmin.php', {
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
            closeHrdModal();
            loadHrds();
        } else {
            Swal.fire('Error', data.message || 'Gagal membuat akun', 'error');
        }
    } catch (error) {
        console.error("Error:", error);
        Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
}

function deleteHrd(id, username) {
    Swal.fire({
        title: `Hapus Akses HRD?`,
        text: `Apakah Anda yakin ingin menghapus akun '${username}'? Mereka tidak akan bisa login lagi.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, cabut akses!',
        cancelButtonText: 'Batal'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch('../api/manageAdmin.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', id: id })
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
                    loadHrds();
                } else {
                    Swal.fire('Error', data.message || 'Gagal menghapus data', 'error');
                }
            } catch (error) {
                console.error("Error:", error);
                Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
            }
        }
    });
}
