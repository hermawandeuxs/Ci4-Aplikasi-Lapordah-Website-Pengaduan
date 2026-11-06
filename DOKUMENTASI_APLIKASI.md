# 📋 DOKUMENTASI APLIKASI LaporDAH

## Daftar Isi

1. [Overview Aplikasi](#overview-aplikasi)
2. [Struktur Database](#struktur-database)
3. [Struktur Folder & File](#struktur-folder--file)
4. [User Roles & Access Control](#user-roles--access-control)
5. [Alur Proses Utama](#alur-proses-utama)
6. [File Penting & Fungsinya](#file-penting--fungsinya)
7. [API Routes](#api-routes)

---

## Overview Aplikasi

**LaporDAH** adalah aplikasi sistem pelaporan pengaduan online berbasis web yang memungkinkan masyarakat untuk melaporkan kasus korupsi, gratifikasi, dan pelanggaran lainnya.

### Fitur Utama:

- ✅ Pengajuan laporan anonim (tanpa login)
- ✅ Pengajuan laporan terdaftar (untuk user yang login)
- ✅ Dashboard statistik real-time
- ✅ Tracking status laporan
- ✅ Manajemen user & hak akses
- ✅ Export & print laporan
- ✅ Arsip laporan
- ✅ Email notification untuk reset password

### Technology Stack:

- **Backend**: PHP CodeIgniter 4
- **Database**: MySQL
- **Frontend**: HTML, CSS (Bootstrap), JavaScript
- **PDF Generation**: DomPDF
- **Real-time Stats**: AJAX

---

## Struktur Database

### Tabel Utama:

#### 1. **users**

Menyimpan data user aplikasi

```
id (PK)
username              - Unik, untuk login
email                 - Email, unik
password_hash         - Password yang di-hash
nama_user             - Nama lengkap
photo                 - Path foto profil
role                  - admin | admin_bidang | user
status                - 1=aktif, 0=nonaktif
kebidangan            - Bidang/departemen (untuk admin_bidang)
created_at / updated_at
```

#### 2. **reports** (Tabel Utama Laporan)

Menyimpan semua laporan pengaduan

```
id (PK)
tracking_code         - Kode unik laporan (format: LAPORDAN-xxxxx)
category              - Kategori laporan (korupsi, gratifikasi, dll)
other_category        - Kategori lain (jika custom)
location              - Lokasi kejadian
latitude / longitude  - Koordinat lokasi
chronology            - Deskripsi kejadian
nama_pelapor          - Nama pelapor
alamat_pelapor        - Alamat pelapor
email_pelapor         - Email pelapor
hp_pelapor            - No. HP pelapor
evidence_1/2/3        - Nama file bukti (max 3 file)
status                - pending | verified | disposition | progress | completed | archived | rejected
note                  - Catatan admin/disposisi
verified_by           - ID user yang verifikasi
verified_at           - Waktu verifikasi
deleted_at            - Soft delete timestamp
created_at / updated_at
```

#### 3. **report_status_logs**

Mencatat history perubahan status laporan

```
id (PK)
report_id (FK)
old_status            - Status sebelumnya
new_status            - Status baru
changed_by            - ID user yang mengubah
updated_at
```

#### 4. **password_reset**

Menyimpan token reset password

```
id (PK)
email
token                 - Token unik untuk reset
expires_at            - Waktu kadaluarsa token
created_at
```

#### 5. **visitors**

Tracking pengunjung website

```
id (PK)
ip_address
user_agent
visited_at
```

---

## Struktur Folder & File

### `/app/Controllers/`

Menghandle HTTP request & logic aplikasi

- **Home.php** - Landing page & public stats
- **Auth.php** - Login, register, forgot password
- **User.php** - Dashboard & profil user
- **Admin.php** - Dashboard & manajemen laporan (admin)
- **AdminBidang.php** - Dashboard & verifikasi (admin bidang)
- **LaporanAnon.php** - Submit & manage laporan anonim
- **Reports.php** - Public reports listing & search
- **BaseController.php** - Base class untuk semua controller

### `/app/Models/`

Database models

- **UserModel.php** - Query user data
- **ReportModel.php** - Query report data
- **ReportStatusLogModel.php** - Query status logs
- **AuditLogModel.php** - Query audit logs
- **VisitorModel.php** - Query visitor data

### `/app/Views/`

View templates (HTML)

- `/auth/` - Login, register, forgot password forms
- `/admin/` - Admin dashboard & views
- `/admin_bidang/` - Admin bidang views
- `/universal/` - Shared views
- `/reports/` - Public report listing
- `/layouts/` - Layout templates
- `/errors/` - Error pages

### `/app/Config/`

Konfigurasi aplikasi

- **Routes.php** - Route definitions (URL mapping)
- **Database.php** - Database configuration
- **App.php** - App configuration
- **Services.php** - Service registration
- **Validation.php** - Validation rules

### `/app/Filters/`

Middleware untuk request filtering

- **AuthFilter.php** - Check login status
- **RoleFilter.php** - Check user role
- **NoAuthFilter.php** - Inverse role check

### `/app/Helpers/`

Utility functions

- **report_helper.php** - Report-related functions
- **PaginationHelper.php** - Pagination helpers

---

## User Roles & Access Control

### 1. **User Biasa** (role: `user`)

- Bisa login dengan username/email & password
- Akses: `/user/*`
- Fitur:
  - Lihat dashboard user
  - Lihat profil
  - Edit profil & password
  - Lihat daftar laporan publik (terbatas)
  - **Catatan**: Fitur submit laporan terdaftar DISABLED

### 2. **Admin Bidang** (role: `admin_bidang`)

- Bisa login & harus punya kebidangan
- Akses: `/admin-bidang/*`
- Fitur:
  - Dashboard dengan statistik bidang
  - List laporan untuk bidang mereka
  - Verifikasi laporan
  - Update progress & status laporan
  - Arsip laporan
  - Print & export laporan
  - **Dibatasi**: Hanya lihat laporan untuk bidang mereka

### 3. **Admin Master** (role: `admin`)

- Bisa login dengan privilege penuh
- Akses: `/admin/*`
- Fitur:
  - Dashboard dengan statistik keseluruhan
  - Manage semua laporan (tanpa filter bidang)
  - Manage user (tambah, edit, delete, reset password)
  - View history status laporan
  - Print & export laporan
  - **Unggul**: Full access tanpa pembatasan bidang

### 4. **Public / Tidak Login**

- Akses: `/` (landing page), `/laporan-anon/*`, `/reports/*`
- Fitur:
  - Lihat landing page dengan statistik
  - Submit laporan anonim
  - Check status laporan (via tracking code)
  - Lihat daftar laporan publik
  - Lihat FAQ, About, Kebijakan Privasi

---

## Alur Proses Utama

### 1. **Alur Pengajuan Laporan Anonim**

```
User Public
    ↓
[Buka /laporan-anon/submit]
    ↓
[Isi Form Laporan]
    ├─ Kategori laporan
    ├─ Lokasi kejadian
    ├─ Kronologi
    ├─ Data pelapor (opsional)
    └─ Upload bukti (max 3 file, max 2MB each)
    ↓
[Submit via AJAX]
    ↓
[LaporanAnon::submit() validate & process]
    ├─ Validasi input
    ├─ Validate lokasi "pekalongan"
    ├─ Upload file evidence
    └─ Generate tracking_code
    ↓
[Insert ke tabel reports]
    ↓
[Return JSON response dengan tracking_code]
    ↓
User dapat tracking_code untuk cek status
```

### 2. **Alur Login User**

```
User Input (username/email + password)
    ↓
[POST /auth/attemptLogin]
    ↓
[Auth::attemptLogin()]
    ├─ Query user by username/email
    ├─ Verify password (password_verify)
    ├─ Check status aktif
    ├─ Validasi kebidangan (untuk admin_bidang)
    └─ Set session
    ↓
[Redirect ke dashboard sesuai role]
    ├─ Admin → /admin
    ├─ Admin Bidang → /admin-bidang
    └─ User → /user/dashboard
```

### 3. **Alur Verifikasi Laporan (Admin Bidang)**

```
Admin Bidang lihat laporan pending
    ↓
[Click detail laporan]
    ↓
[AdminBidang::detailAnonim()]
    ├─ Get report data
    ├─ Get evidence files
    ├─ Get status logs
    └─ Render view
    ↓
[Form action: verify / reject / progress]
    ↓
[POST /admin-bidang/verify/{action}/{id}]
    ↓
[AdminBidang::verify()]
    ├─ Update reports.status
    ├─ Insert ke report_status_logs
    └─ Save catatan
    ↓
Status laporan berubah
```

### 4. **Alur Arsip Laporan**

```
Admin pilih laporan untuk diarsipkan
    ↓
[POST /admin/archive/{id}]
    ↓
[Admin::archive()]
    ├─ Update reports.status = "archived"
    ├─ Insert ke report_status_logs
    └─ Save note
    ↓
Laporan masuk folder arsip
    ↓
[Bisa diakses di /admin/report-arsip]
```

---

## File Penting & Fungsinya

### Controller Files

#### `/app/Controllers/Home.php`

**Fungsi**: Halaman landing page
**Methods**:

- `index()` - Menampilkan landing page dengan statistik
- `getStatistics()` - API untuk statistik laporan
- `about()` - Halaman tentang
- `faqUmum()` - FAQ umum
- `kebijakanPrivasi()` - Kebijakan privasi

#### `/app/Controllers/Auth.php`

**Fungsi**: Autentikasi & autoritas
**Methods**:

- `login()` - Form login
- `attemptLogin()` - Process login ✅
- `register()` - Form register
- `processRegister()` - Process register
- `forgot()` - Form lupa password & send email
- `reset()` - Form reset password via token
- `logout()` - Logout & destroy session

#### `/app/Controllers/Admin.php`

**Fungsi**: Admin dashboard & laporan management
**Methods** (penting):

- `index()` - Dashboard admin dengan statistik & charts
- `getReports()` - API untuk list laporan (AJAX)
- `detailAnon($id)` - Detail laporan
- `archive($id)` - Arsipkan laporan
- `delete($id)` - Hapus laporan
- `manajemenUser()` - List user
- `tambahUser()` - Form & process add user
- `editUser($id)` - Form & process edit user
- `hapusUser($id)` - Delete user
- `resetPassword($id)` - Reset password user

#### `/app/Controllers/AdminBidang.php`

**Fungsi**: Admin Bidang dashboard
**Methods**:

- `dashboard()` - Dashboard dengan statistik bidang
- `listLaporan()` - List laporan untuk bidang
- `detailAnonim($id)` - Detail laporan
- `verify($action, $id)` - Verify / reject / mark progress
- `archive($id)` - Arsipkan laporan
- `rekapLaporan()` - Rekap laporan

#### `/app/Controllers/LaporanAnon.php`

**Fungsi**: Public anonymous reporting
**Methods**:

- `submit()` - Form & process submit laporan anonim
- `checkStatus()` - API check status via tracking code
- `showImage($filename)` - Display evidence image

### Model Files

#### `/app/Models/UserModel.php`

**Fungsi**: User data management
**Key Methods**:

- `getUserByLogin($login)` - Search user by username/email
- `getKebidanganById($userId)` - Get user's kebidangan

#### `/app/Models/ReportModel.php`

**Fungsi**: Report data management
**Key Methods**:

- `getTotalReports()` - Count total reports
- `getReportsByStatus($status)` - Get reports by status
- `getReports($category, $sort, $perPage)` - List reports with filters

#### `/app/Models/ReportStatusLogModel.php`

**Fungsi**: Status change history
**Methods**:

- Standard CRUD methods

### Config Files

#### `/app/Config/Routes.php`

**Fungsi**: URL routing configuration
**Struktur**:

1. Public Routes (no auth)
2. Protected Routes (with role filter)
   - User Routes
   - Admin Routes
   - Admin Bidang Routes

#### `/app/Config/Database.php`

**Fungsi**: Database connection settings
**Important**: Gunakan `.env` untuk production!

---

## API Routes

### Public Routes

```
GET  /                              → Home::index (Landing page)
GET  /home/getStatistics            → Home::getStatistics (Stats API)
GET  /about                         → Home::about
GET  /faqUmum                       → Home::faqUmum
GET  /kebijakanPrivasi              → Home::kebijakanPrivasi

POST /laporan-anon/submit           → LaporanAnon::submit (Submit laporan anonim)
GET  /laporan-anon/check-status     → LaporanAnon::checkStatus (Check status via tracking code)
GET  /laporan-anon/show-image/:id   → LaporanAnon::showImage (View evidence image)

GET  /reports/search                → Reports::search (Public search)
GET  /reports/detail/:id            → Reports::detail (Public detail)

GET  /login                         → Auth::login (Form)
POST /auth/attemptLogin             → Auth::attemptLogin (Process)
GET  /register                      → Auth::register (Form)
POST /register/process              → Auth::processRegister (Process)
GET  /auth/forgot                   → Auth::forgot (Form & Process)
GET  /auth/reset?token=xxx          → Auth::reset (Form & Process)
```

### User Routes (`/user/*`)

```
GET  /user/dashboard                → User::dashboard
GET  /user/laporan                  → User::laporan
GET  /user/laporan/:id              → User::detail
GET  /user/profile                  → User::profile
GET  /user/edit_profil              → User::editProfile
POST /user/profile/update           → User::updateProfile
GET  /user/password                 → User::edit_password
POST /user/password                 → User::update_password
POST /user/upload_foto              → User::upload_foto
GET  /user/faq                      → User::faq
GET  /user/logout                   → Auth::logout
```

### Admin Routes (`/admin/*`)

```
GET  /admin                         → Admin::index (Dashboard)
GET  /admin/report                  → Admin::listLaporan
GET  /admin/report/:id              → Admin::detailAnon
GET  /admin/reports                 → Admin::reports
GET  /admin/reports/get-data        → Admin::getReports (AJAX)
POST /admin/reports/archive/:id     → Admin::archive
POST /admin/delete/:id              → Admin::delete
GET  /admin/history-laporan         → Admin::historyLaporan
POST /admin/archive-bulk            → Admin::archive_bulk
GET  /admin/report-arsip            → Admin::reportArsipGabungan
GET  /admin/rekap-laporan           → Admin::rekapLaporan
GET  /admin/print-pdf/:id           → Admin::printPdf
GET  /admin/manajemen-user          → Admin::manajemenUser
GET  /admin/manajemen-user/tambah   → Admin::tambahUser
POST /admin/manajemen-user/tambah   → Admin::tambahUser
GET  /admin/manajemen-user/edit/:id → Admin::editUser
POST /admin/manajemen-user/edit/:id → Admin::editUser
POST /admin/manajemen-user/hapus/:id → Admin::hapusUser
POST /admin/manajemen-user/reset-password/:id → Admin::resetPassword
```

### Admin Bidang Routes (`/admin-bidang/*`)

```
GET  /admin-bidang                  → AdminBidang::dashboard
GET  /admin-bidang/laporan          → AdminBidang::listLaporan
GET  /admin-bidang/laporan/:id      → AdminBidang::detailAnonim
POST /admin-bidang/verify/:action/:id → AdminBidang::verify
POST /admin-bidang/delete/:id       → AdminBidang::delete
POST /admin-bidang/update_progress/:id → AdminBidang::update_progress
POST /admin-bidang/update_completed/:id → AdminBidang::update_completed
GET  /admin-bidang/report-arsip     → AdminBidang::reportArsipGabungan
POST /admin-bidang/archive/:id      → AdminBidang::archive
POST /admin-bidang/archive-bulk     → AdminBidang::archive_bulk
GET  /admin-bidang/rekap-laporan    → AdminBidang::rekapLaporan
GET  /admin-bidang/print-pdf/:id    → AdminBidang::printPdf
```

---

## Tips Debugging

### 1. Enable Debug Mode

Buka `/app/Config/App.php`:

```php
public $environment = 'development'; // Change dari production
```

### 2. Check Logs

```
/writable/logs/log-YYYY-MM-DD.log
```

### 3. Database Queries

Gunakan `dd()` atau `echo` di controller:

```php
$db = \Config\Database::connect();
$result = $db->table('reports')->get()->getResultArray();
dd($result); // Die dump
```

### 4. Session Debug

```php
dd(session()->getFlashdata()); // Lihat flash data
dd(session()->get()); // Lihat semua session data
```

---

## Setup & Installation

1. **Clone Repository**

```bash
git clone <repo> Master-LaporDah
cd Master-LaporDah
```

2. **Install Dependencies**

```bash
composer install
```

3. **Setup Environment**

```bash
cp .env.example .env
```

4. **Generate Database**

```bash
php spark migrate
php spark db:seed SomeSeed
```

5. **Run Development Server**

```bash
php spark serve
```

Browser: `http://localhost:8080`

---

## Kesimpulan

Aplikasi **LaporDAH** adalah sistem pelaporan pengaduan yang terintegrasi dengan:

- ✅ Multi-role access control (user, admin_bidang, admin)
- ✅ Anonymous & registered reporting
- ✅ Real-time statistics & dashboard
- ✅ Email notifications
- ✅ Export & print capabilities
- ✅ Full audit logging

Semoga dokumentasi ini membantu Anda memahami aplikasi! 🎉
