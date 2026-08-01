# Employee Management System

Sistem manajemen pegawai berbasis web (PHP Native) yang dibuat untuk memenuhi Tugas Praktikum Pemrograman Web. Aplikasi ini dirancang agar dapat mendemonstrasikan operasi CRUD (Create, Read, Update, Delete) secara asinkron (tanpa refresh halaman) menggunakan Fetch API.

## Fitur Utama (Sesuai Kriteria Tugas Khusus)

1.  **Login Admin:** Menggunakan session PHP murni.
2.  **Daftar Pegawai:** Dirender menggunakan JavaScript dari JSON response.
3.  **Tambah Data (Async):** Melalui form modal, submit via `fetch` POST, tanpa reload.
4.  **Edit Data (Async):** Melalui form modal di halaman yang sama, submit via `fetch` POST, tanpa reload.
5.  **Hapus Data (Async):** Konfirmasi via SweetAlert2, eksekusi penghapusan via `fetch` POST, render ulang data otomatis.
6.  **Backend Native:** PHP Native dengan koneksi ke database MySQL menggunakan ekstensi `mysqli`.
7.  **Data Flow Array PHP:** Menampung hasil query ke dalam variabel array PHP eksplisit sebelum ditampilkan via `json_encode()` ke frontend.
8.  **Pemisahan Aset:** File HTML/PHP, CSS, dan JavaScript disimpan dalam file dan folder yang terpisah.

---

## Prasyarat Lingkungan (Environment)

*   PHP (versi 7.4 atau lebih baru disarankan)
*   MySQL (atau MariaDB)
*   Web Server (Apache/Nginx) -> Bisa menggunakan XAMPP, WAMP, MAMP, atau LAMP stack.
*   Browser modern dengan dukungan Fetch API (Chrome, Firefox, Edge, Safari).

---

## Panduan Instalasi dan Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di laptop Anda saat demo:

### 1. Persiapan Database

1.  Nyalakan service MySQL/MariaDB pada server lokal Anda (misal: jalankan module MySQL di XAMPP).
2.  Buka alat manajemen database favorit Anda (seperti phpMyAdmin, DBeaver, TablePlus, atau CLI MySQL).
3.  Buat database baru dengan nama `employee_management`:
    ```sql
    CREATE DATABASE employee_management;
    ```
4.  Import struktur dan data awal tabel dari file `database.sql` yang tersedia di dalam folder utama project ini.
    *   Jika melalui phpMyAdmin: Pilih database `employee_management` > Klik tab "Import" > Pilih file `database.sql` > Klik "Go/Kirim".

### 2. Konfigurasi Koneksi Database

Jika Anda menggunakan kredensial MySQL yang tidak standar (bukan `root` tanpa password), Anda perlu menyesuaikannya:

1.  Buka file `config/database.php` menggunakan teks editor (VS Code, Sublime, dll).
2.  Sesuaikan variabel koneksi jika diperlukan:
    ```php
    $host = "localhost";
    $user = "root"; // Ubah jika username MySQL Anda bukan root
    $pass = "";     // Ubah jika root MySQL Anda menggunakan password
    $db = "employee_management";
    ```

### 3. Menjalankan Aplikasi

Anda memiliki dua pilihan utama untuk menjalankan aplikasi PHP ini.

#### Opsi A: Menggunakan Web Server Lengkap (XAMPP/LAMP/Apache) - *Sangat Disarankan*

1.  Pindahkan/copy seluruh folder project ini (`employee-management`) ke dalam direktori *document root* web server Anda.
    *   XAMPP (Windows): `C:\xampp\htdocs\`
    *   LAMP (Linux): `/var/www/html/`
    *   MAMP (Mac): `/Applications/MAMP/htdocs/`
2.  Pastikan service Apache sudah berjalan.
3.  Buka browser web Anda.
4.  Akses URL berikut: `http://localhost/employee-management/`

#### Opsi B: Menggunakan PHP Built-in Server (Ringan, Tanpa Apache)

Jika Anda sudah menginstal PHP di system secara global dan tidak ingin menyalakan Apache:

1.  Buka Terminal atau Command Prompt.
2.  Arahkan direktori (cd) ke dalam folder project `employee-management`.
3.  Jalankan perintah berikut:
    ```bash
    php -S localhost:8000
    ```
4.  Buka browser web Anda.
5.  Akses URL berikut: `http://localhost:8000/`

*(Catatan: Anda tetap harus memastikan service MySQL berjalan terpisah jika menggunakan cara ini).*

### 4. Menggunakan Aplikasi (Data Login Demo)

Setelah aplikasi terbuka di browser, Anda akan diarahkan ke halaman Login. Gunakan kredensial dummy berikut yang telah disiapkan:

*   **Username:** `admin`
*   **Password:** `admin123`

Setelah berhasil login, Anda dapat langsung menguji fitur Tambah, Edit, dan Hapus pegawai dari halaman Data Pegawai. Semua proses harus berjalan asinkron tanpa reload pada browser (Anda bisa memeriksanya dengan melihat tidak ada icon *refresh* browser yang berputar saat Anda menyimpan/menghapus data).

---

## Catatan Penting Saat Demo

*   **Pemrosesan Data Array:** Jika asisten atau dosen meminta bukti bahwa data disimpan dalam array sebelum dirender (Kriteria wajib no 6), tunjukkan file `api/getEmployees.php` pada baris 15-18 (`$employees[] = $row;`).
*   **Tidak Ada Refresh:** Jika ditanya bukti halaman tidak berpindah, tekan tombol `F12` (DevTools browser), masuk ke tab **Network**, pilih filter **Fetch/XHR**, lalu lakukan aksi (Tambah/Edit/Hapus). Tunjukkan bahwa yang terjadi hanya pemanggilan file API (`.php`), bukan pemuatan ulang file HTML.
*   **Offline Mode:** Library SweetAlert2 saat ini di-load menggunakan koneksi internet (CDN). Jika koneksi internet dirasa kurang stabil pada saat demo, sangat disarankan untuk mengunduh library tersebut dan menaruhnya di folder `assets/` secara lokal.
