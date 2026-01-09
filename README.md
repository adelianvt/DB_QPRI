# Corporate Project Submission System (DB_QPRI)

Sistem Web Aplikasi untuk manajemen pengajuan, peninjauan, dan persetujuan proyek perusahaan (Corporate Project Submission). Aplikasi ini dirancang untuk mempermudah alur kerja antara **Maker** (Pengaju) dan **Admin** (Manajemen) dengan sistem kontrol akses berbasis peran (Role-Based Access Control).

## 🚀 Fitur Utama

### 1. Multi-Role Authentication
Sistem membedakan akses antara dua peran utama:
*   **Maker (Pengaju)**: Karyawan yang membuat dan mengajukan proposal proyek.
*   **Admin (Manajemen)**: Group Head atau Manajer yang meninjau dan memberikan persetujuan.

### 2. Alur Kerja Pengajuan (Submission Workflow)
*   **Draft**: Maker dapat menyimpan progres pengajuan sebagai draft sebelum disubmit.
*   **Submission**: Maker mengirimkan pengajuan untuk ditinjau.
*   **Review**: Admin dapat melihat detail pengajuan yang masuk.
*   **Approval/Rejection**: Admin memberikan keputusan (Approve/Reject) terhadap pengajuan.

### 3. Dashboard Interaktif
*   **Maker Dashboard**: Menampilkan daftar proyek milik sendiri beserta status terkini.
*   **Management Dashboard**: Pusat kontrol bagi Admin untuk melihat antrean persetujuan (Waiting Approval).

### 4. Keamanan & Validasi
*   Proteksi rute (Route Protection) untuk mencegah akses tidak sah (misal: Maker tidak bisa akses menu Admin).
*   Validasi data input yang ketat untk memastikan kelengkapan dokumen proyek.

---

## 🛠️ Teknologi yang Digunakan

*   **Framework Backend**: [Laravel 12](https://laravel.com)
*   **Bahasa Pemrograman**: PHP > 8.2
*   **Frontend**: Blade Templates
*   **Database**: MySQL
*   **Styling**: Vanilla CSS / Custom Design System (Premium UI)
*   **Build Tool**: Vite

---

## ⚙️ Instalasi & Pengaturan

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal:

### Prasyarat
*   PHP >= 8.2
*   Composer
*   Node.js & NPM
*   MySQL Database

### Langkah Instalasi

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/username/db-qpri.git](https://github.com/username/db-qpri.git)
    cd db-qpri
    ```

2.  **Instal Dependensi PHP (Composer)**
    ```bash
    composer install
    ```

3.  **Instal Dependensi Frontend (NPM)**
    ```bash
    npm install
    ```

4.  **Konfigurasi Environment**
    Salin file `.env.example` menjadi `.env`:
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan sesuaikan koneksi database Anda:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_anda
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

6.  **Migrasi Database & Seeding**
    Jalankan perintah ini untuk membuat tabel dan data awal (Role, Status, User default):
    ```bash
    php artisan migrate --seed
    ```

7.  **Jalankan Aplikasi**
    Buka dua terminal terpisah untuk menjalankan server Laravel dan Vite:
    ```bash
    # Terminal 1
    php artisan serve

    # Terminal 2
    npm run dev
    ```

    Akses aplikasi di: `http://localhost:8000`

---

## 📖 Panduan Penggunaan

### Akun Demo (Jika Menggunakan Seeder)
*   **Login Admin**:
    *   Email: `admin@example.com`
    *   Password: `password`
*   **Login Maker**:
    *   Email: `maker@example.com`
    *   Password: `password`

### Alur Singkat
1.  Login sebagai **Maker**.
2.  Klik tombol **"Buat Pengajuan"**.
3.  Isi formulir detail proyek (Judul, Tipe, Detail Anggaran, dll).
4.  Klik **Simpan Draft** atau langsung **Submit**.
5.  Login sebagai **Admin**.
6.  Masuk ke menu **Management**.
7.  Lihat daftar pengajuan dengan status `Submitted`.
8.  Buka detail dan pilih **Approve** atau **Reject**.

---

## 📝 Lisensi

Proyek ini bersifat open-source dan dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).
