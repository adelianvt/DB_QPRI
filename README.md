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
