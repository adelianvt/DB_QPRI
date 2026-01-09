# Q-PRI (Q - Project Registration & Information)

Q-PRI adalah aplikasi berbasis **Laravel** untuk mengelola proses **Pengajuan Project** dengan workflow approval bertahap berdasarkan role:

✅ CRV → ✅ GH CRV → ✅ IAG → ✅ GH IAG  
Dengan fitur:

- Pengajuan Project
- Approval & Reject per tahap
- Form tambahan khusus IAG
- Manajemen data per role (visibility rules)
- Download PDF per pengajuan
- Download Selected (ZIP berisi PDF) dari halaman Management

---

## 🚀 Tech Stack

- **Laravel**
- **PostgreSQL**
- **Tailwind CSS**
- **Vite**
- **DomPDF (barryvdh/laravel-dompdf)**
- **ZipArchive**

---

## ✅ Features

### 🔐 Role & Workflow
| Role | role_id | Akses Utama |
|------|---------|------------|
| CRV | 1 | Buat pengajuan, edit/delete hanya saat rejected |
| GH CRV | 2 | Approve / Reject tahap 1 |
| IAG | 3 | Isi form tambahan saat status `pending_iag` |
| GH IAG | 14 | Approve / Reject tahap 2 (final) |

### 📄 PDF Download
- Download PDF per pengajuan via tombol Download (Detail page)
- Download Selected (ZIP PDF) via halaman Management

---

## 📌 Status Flow

| Status Code | Tahap |
|------------|-------|
| `pending_approver1` | Waiting by GH CRV |
| `pending_iag` | Waiting by IAG |
| `pending_approver2` | Waiting by GH IAG |
| `approved` | Approved |
| `rejected` | Rejected |

---

## 📂 Folder Structure (Important Files)

### ✅ Controller
- `app/Http/Controllers/PengajuanController.php`

### ✅ Views
- Management list:  
  `resources/views/pengajuan/index.blade.php`
- Detail view:  
  `resources/views/pengajuan/show.blade.php`
- PDF template:  
  `resources/views/pengajuan/pdf.blade.php`
- Form IAG tambahan:  
  `resources/views/pengajuan/iag_edit.blade.php`
- Layout sidebar + app:  
  `resources/views/layouts/app.blade.php`

---

## ⚙️ Installation

1. Clone repository:
```bash
git clone https://github.com/username/q-pri.git
cd q-pri
Install dependencies:

bash
Copy code
composer install
npm install
Copy .env file:

bash
Copy code
cp .env.example .env
Generate key:

bash
Copy code
php artisan key:generate
Configure database in .env
Example PostgreSQL:

env
Copy code
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=qpri
DB_USERNAME=postgres
DB_PASSWORD=yourpassword
Run migrate:

bash
Copy code
php artisan migrate
Run Vite:

bash
Copy code
npm run dev
Run app:

bash
Copy code
php artisan serve
✅ Login
Aplikasi menggunakan login berdasarkan NIP (sesuai UI).

Pastikan akun user memiliki field:

nip

password

role_id

📌 Routes
File route utama:
routes/web.php

Routes penting:

php
Copy code
Route::get('/pengajuans', [PengajuanController::class, 'index'])->name('pengajuans.index');
Route::get('/pengajuans/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuans.show');

Route::get('/pengajuans/{pengajuan}/download', [PengajuanController::class, 'download'])->name('pengajuans.download');
Route::post('/pengajuans/download-selected', [PengajuanController::class, 'downloadSelected'])->name('pengajuans.downloadSelected');

Route::post('/pengajuans/{pengajuan}/approve', [PengajuanController::class, 'approve'])->name('pengajuans.approve');
Route::post('/pengajuans/{pengajuan}/reject', [PengajuanController::class, 'reject'])->name('pengajuans.reject');

Route::get('/pengajuans/{pengajuan}/iag', [PengajuanController::class, 'iagEdit'])->name('pengajuans.iag.edit');
Route::put('/pengajuans/{pengajuan}/iag', [PengajuanController::class, 'iagUpdate'])->name('pengajuans.iag.update');
🧾 PDF Output
PDF di-generate menggunakan:

Blade template: pengajuan/pdf.blade.php

DomPDF

Jika ingin menambahkan data form IAG ke PDF:
gunakan path meta:

meta['iag']

✅ Notes
Pastikan server memiliki extension:
- zip
- mbstring
- dom
- gd

Cek extension:
php -m

📌 Authors
Developed by: Adelia Novita Sari
Project: Q-PRI Management System











ChatGPT can make mistakes. OpenAI doesn't use Chatqpt workspace data to train its models.
