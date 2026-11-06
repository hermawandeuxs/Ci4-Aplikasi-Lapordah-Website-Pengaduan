# 🚀 QUICK START GUIDE - LaporDAH

Panduan cepat untuk memahami alur kerja aplikasi LaporDAH.

## Daftar File Penting Untuk Dipelajari

### Urutan Belajar (dari yang paling penting):

```
1. /app/Config/Routes.php
   └─ Pahami URL mapping & role-based routes

2. /app/Controllers/Auth.php
   └─ Pahami alur login & session management

3. /app/Controllers/BaseController.php
   └─ Pahami base class & shared methods

4. /app/Controllers/Home.php
   └─ Pahami landing page & public stats

5. /app/Models/UserModel.php
   └─ Pahami struktur user & query methods

6. /app/Models/ReportModel.php
   └─ Pahami struktur laporan & status tracking

7. /app/Controllers/LaporanAnon.php
   └─ Pahami alur submit laporan anonim

8. /app/Controllers/Admin.php
   └─ Pahami admin dashboard & management

9. /app/Controllers/AdminBidang.php
   └─ Pahami role-based access untuk admin bidang

10. /app/Views/
    └─ Pahami struktur view & template
```

---

## 📊 Alur Utama Aplikasi

### Alur 1: User Anonimous Submit Laporan

```
┌─ User Buka /laporan-anon/submit
│
├─ Isi Form Laporan
│  ├─ Kategori (required)
│  ├─ Lokasi (required, must contain "pekalongan")
│  ├─ Kronologi (required)
│  ├─ Data Pelapor (opsional)
│  └─ Upload Bukti (max 3 files, max 2MB)
│
├─ Submit AJAX Request
│  └─ POST /laporan-anon/submit
│
├─ LaporanAnon::submit() Process
│  ├─ Validasi input
│  ├─ Validasi file (size, type, count)
│  ├─ Upload file ke /public/uploads/reports/
│  ├─ Generate tracking_code (LAPORDAN-xxxxx)
│  └─ Insert ke tabel 'reports' dengan status='pending'
│
├─ Response JSON
│  ├─ success: true
│  ├─ message: "Laporan berhasil diajukan"
│  └─ tracking_code: "LAPORDAN-xxxxx"
│
└─ User bisa check status dengan tracking_code
   └─ GET /laporan-anon/check-status?code=LAPORDAN-xxxxx
```

### Alur 2: Admin/Admin Bidang Verifikasi Laporan

```
┌─ Admin/Admin Bidang Login
│
├─ POST /auth/attemptLogin
│  ├─ Query user by username/email
│  ├─ Verify password (password_verify())
│  ├─ Set session dengan role
│  └─ Redirect ke dashboard
│
├─ Lihat Laporan Pending
│  ├─ Admin: GET /admin/report (all laporan)
│  └─ Admin Bidang: GET /admin-bidang/laporan (filtered by kebidangan)
│
├─ Click Detail Laporan
│  ├─ Admin: GET /admin/report/:id
│  └─ Admin Bidang: GET /admin-bidang/laporan/:id
│
├─ View Laporan Detail
│  ├─ ReportData (kategori, lokasi, kronologi)
│  ├─ Evidence Files (gambar bukti)
│  ├─ Status History (log perubahan status)
│  └─ Action Buttons
│     ├─ Verify (ubah status ke "verified")
│     ├─ Disposition (ubah status ke "disposition" + note)
│     ├─ Progress (ubah status ke "progress" + update)
│     ├─ Completed (ubah status ke "completed")
│     └─ Reject (ubah status ke "rejected")
│
├─ Submit Action (e.g., Verify)
│  └─ POST /admin-bidang/verify/{action}/{id}
│
├─ AdminBidang::verify() Process
│  ├─ Update reports.status = "verified"
│  ├─ Update reports.note = "catatan"
│  ├─ Update reports.verified_by = current_user_id
│  ├─ Insert ke report_status_logs (history)
│  └─ Response success
│
└─ Laporan status berubah di dashboard
```

### Alur 3: User Biasa Login & Lihat Dashboard

```
┌─ User Buka /login
│
├─ Isi Username/Email & Password
│  └─ POST /auth/attemptLogin
│
├─ Auth::attemptLogin() Process
│  ├─ Query user by username/email
│  ├─ Verify password (password_verify)
│  ├─ Check status=1 (aktif)
│  ├─ Set session (user_id, username, role)
│  └─ Redirect /user/dashboard
│
├─ User Dashboard
│  ├─ Lihat statistik personal
│  ├─ Edit profil & password
│  ├─ Upload foto profil
│  └─ Lihat FAQ & info publik
│
└─ Logout
   └─ GET /user/logout → destroy session → redirect /login
```

---

## 🔐 Role-Based Access Control (RBAC)

### User Roles

| Role             | Akses Routes           | Fitur                                       |
| ---------------- | ---------------------- | ------------------------------------------- |
| **user**         | `/user/*`              | Dashboard, profil, FAQ                      |
| **admin_bidang** | `/admin-bidang/*`      | Verifikasi laporan (by kebidangan)          |
| **admin**        | `/admin/*`             | Full access semua laporan + user management |
| **public**       | `/`, `/laporan-anon/*` | Submit laporan anonim, check status         |

### Security Filters

Setiap route dilindungi dengan filter:

```php
// User only
$routes->group('user', ['filter' => 'role:user'], function($routes) {
    // Hanya user dengan role='user' yg bisa akses
});

// Admin only
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    // Hanya user dengan role='admin' yg bisa akses
});
```

---

## 📝 Status Laporan

Status laporan di database tabel `reports`:

```
pending      ──┐
               ├──> verified / disposition ──> progress ──> completed ──> archived
rejected     ──┘
```

**Penjelasan**:

- **pending**: Laporan baru, belum diproses
- **verified**: Laporan sudah diverifikasi
- **disposition**: Laporan di-disposisi ke unit lain
- **progress**: Laporan dalam proses penyelesaian
- **completed**: Laporan selesai diproses
- **archived**: Laporan diarsipkan
- **rejected**: Laporan ditolak / tidak valid

---

## 🗄️ Database Schema (Penting)

### Tabel `users`

```
id | username | email | password_hash | nama_user | role | status | kebidangan | created_at | updated_at
```

**role**: `user`, `admin`, `admin_bidang`  
**status**: `1` (aktif), `0` (nonaktif)  
**kebidangan**: Bidang/departemen (untuk admin_bidang)

### Tabel `reports` (Utama)

```
id | tracking_code | category | location | latitude | longitude |
chronology | nama_pelapor | email_pelapor | hp_pelapor |
evidence_1 | evidence_2 | evidence_3 |
status | note | verified_by | verified_at |
deleted_at | created_at | updated_at
```

**status**: `pending`, `verified`, `disposition`, `progress`, `completed`, `archived`, `rejected`  
**evidence_X**: Nama file (disimpan di `/public/uploads/reports/`)

### Tabel `report_status_logs`

```
id | report_id | old_status | new_status | changed_by | updated_at
```

Mencatat history perubahan status setiap laporan.

---

## 🔧 Useful Code Snippets

### 1. Get Current User Data

```php
$userId = session()->get('user_id');
$role = session()->get('role');
$username = session()->get('username');
```

### 2. Check User Role in Controller

```php
if (session()->get('role') !== 'admin') {
    return redirect()->to('/');
}
```

### 3. Query Laporan by Status

```php
$reportModel = new \App\Models\ReportModel();
$pendingReports = $reportModel->where('status', 'pending')->findAll();
$completedReports = $reportModel->where('status', 'completed')->findAll();
```

### 4. Generate Tracking Code

```php
$trackingCode = 'LAPORDAN-' . strtoupper(substr(uniqid(), -5));
```

### 5. Verify Password

```php
if (password_verify($inputPassword, $user['password_hash'])) {
    // Password correct
}
```

### 6. Hash Password

```php
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
```

### 7. Check if File is Image

```php
$validMimes = ['image/jpeg', 'image/jpg', 'image/png'];
if (in_array($file->getClientMimeType(), $validMimes)) {
    // Valid image
}
```

### 8. Get All Reports with Filter

```php
$builder = $reportModel->builder();
$builder->where('status !=', 'deleted')
        ->where('status !=', 'archived')
        ->orderBy('created_at', 'DESC');
$reports = $builder->get()->getResultArray();
```

---

## 📂 Folder Structure Reference

```
Master-LaporDah/
├── app/
│   ├── Config/
│   │   ├── Routes.php           ← URL Routing (PENTING)
│   │   ├── Database.php         ← Database Config
│   │   └── ...
│   ├── Controllers/
│   │   ├── BaseController.php   ← Parent class untuk semua controller
│   │   ├── Home.php             ← Landing page
│   │   ├── Auth.php             ← Login/Register/Forgot password
│   │   ├── Admin.php            ← Admin dashboard & management
│   │   ├── AdminBidang.php      ← Admin Bidang dashboard
│   │   ├── LaporanAnon.php      ← Anonymous reporting
│   │   ├── User.php             ← User dashboard
│   │   └── Reports.php          ← Public reports listing
│   ├── Models/
│   │   ├── UserModel.php        ← User queries
│   │   ├── ReportModel.php      ← Report queries
│   │   ├── ReportStatusLogModel.php ← Status history
│   │   └── ...
│   ├── Views/
│   │   ├── auth/                ← Login, Register, Forgot Password
│   │   ├── admin/               ← Admin views
│   │   ├── admin_bidang/        ← Admin Bidang views
│   │   ├── universal/           ← Shared views (dashboard, etc)
│   │   ├── reports/             ← Public report listing
│   │   ├── layouts/             ← Layout templates
│   │   └── ...
│   ├── Filters/
│   │   ├── AuthFilter.php       ← Check login
│   │   ├── RoleFilter.php       ← Check role
│   │   └── NoAuthFilter.php     ← Inverse check
│   ├── Helpers/
│   │   ├── report_helper.php    ← Report utilities
│   │   └── PaginationHelper.php ← Pagination utilities
│   └── ...
├── public/
│   ├── uploads/
│   │   └── reports/             ← Evidence files disimpan di sini
│   ├── assets/                  ← CSS, JS, images
│   └── index.php
├── DOKUMENTASI_APLIKASI.md      ← Full dokumentasi (BACA INI)
└── README.md
```

---

## 🎯 Checklist Belajar

- [ ] Baca & pahami Routes.php
- [ ] Pahami alur login di Auth.php
- [ ] Pahami Home.php (landing page)
- [ ] Pahami UserModel & ReportModel
- [ ] Pahami LaporanAnon (submit laporan)
- [ ] Pahami Admin dashboard
- [ ] Pahami AdminBidang dashboard
- [ ] Test login sebagai user biasa
- [ ] Test login sebagai admin_bidang
- [ ] Test login sebagai admin
- [ ] Submit laporan anonim & track status
- [ ] Verifikasi laporan sebagai admin_bidang
- [ ] Check archive & history

---

## 🐛 Debugging Tips

### 1. Enable Debug Mode

```php
// app/Config/App.php
public $environment = 'development';
```

### 2. Check Logs

```
/writable/logs/log-YYYY-MM-DD.log
```

### 3. Dump & Die (Debug Output)

```php
dd($variable); // Print & stop execution
d($variable);  // Print only
```

### 4. Log Messages

```php
log_message('debug', 'Your debug message');
log_message('error', 'Your error message');
```

### 5. Check Session Data

```php
dd(session()->getFlashdata());
dd(session()->get());
```

---

## 📞 Kontribusi & Support

Jika ada pertanyaan atau bug, silakan hubungi tim development!

Happy Learning! 🎉
