# 📚 DOKUMENTASI LENGKAP - LaporDAH (INDEX)

Welcome! Dokumentasi ini dirancang untuk membantu Anda memahami aplikasi LaporDAH dengan cepat.

---

## 🎯 Mulai dari Sini

Jika Anda baru pertama kali, ikuti urutan berikut:

### 1️⃣ **[QUICK_START_GUIDE.md](./QUICK_START_GUIDE.md)** ⭐ START HERE

- Overview aplikasi
- Alur utama (3 alur penting)
- User roles & access control
- Database schema basics
- Debugging tips
- **Waktu: 15 menit**

### 2️⃣ **[DOKUMENTASI_APLIKASI.md](./DOKUMENTASI_APLIKASI.md)**

- Penjelasan mendetail setiap file
- API routes lengkap
- Struktur folder
- Konfigurasi database
- **Waktu: 30 menit**

### 3️⃣ **[DATABASE_MODELS_DOCUMENTATION.md](./DATABASE_MODELS_DOCUMENTATION.md)**

- Database schema lengkap (5 tabel)
- SQL CREATE TABLE statements
- Model methods & usage
- Query builder examples
- Best practices
- **Waktu: 25 menit**

### 4️⃣ **[VIEWS_DOCUMENTATION.md](./VIEWS_DOCUMENTATION.md)**

- Struktur view folder
- Detail setiap view
- Template syntax
- CSS/Bootstrap classes
- AJAX integration
- **Waktu: 20 menit**

---

## 📖 Dokumentasi Kode Source

### Controllers dengan Komentar

Setiap controller sudah ditambahkan komentar detail:

#### 🔐 Authentication

- **[Auth.php](./app/Controllers/Auth.php)** - Login, Register, Forgot Password
  - Methods: login, attemptLogin, register, processRegister, forgot, reset, logout

#### 🏠 Public Pages

- **[Home.php](./app/Controllers/Home.php)** - Landing page & statistics
  - Methods: index, getStatistics, about, faqUmum, kebijakanPrivasi

#### 📝 Laporan Anonim

- **[LaporanAnon.php](./app/Controllers/LaporanAnon.php)** - Public report submission
  - Methods: submit, checkStatus, showImage

#### 🛡️ Shared Base

- **[BaseController.php](./app/Controllers/BaseController.php)** - Parent class untuk semua controller
  - Methods: initController, checkSession, mapMonthlyStats, mapCategoryStats, getSidebarNotif

#### 👨‍💼 Admin Dashboard

- **[Admin.php](./app/Controllers/Admin.php)** - Admin functionality
  - Methods: index, getReports, detailAnon, archive, delete, manajemenUser, dll

#### 👔 Admin Bidang

- **[AdminBidang.php](./app/Controllers/AdminBidang.php)** - Admin bidang functionality
  - Methods: dashboard, listLaporan, detailAnonim, verify, archive, dll

#### 👤 User Dashboard

- **[User.php](./app/Controllers/User.php)** - User dashboard
  - Methods: dashboard, laporan, profile, editProfile, updateProfile, etc

### Models dengan Komentar

Setiap model memiliki dokumentasi:

- **[UserModel.php](./app/Models/UserModel.php)**

  - Methods: getUserByLogin, getKebidanganById

- **[ReportModel.php](./app/Models/ReportModel.php)**

  - Methods: getTotalReports, getReportsByStatus, getReports, insert, dll

- **[ReportStatusLogModel.php](./app/Models/ReportStatusLogModel.php)**

  - Basic CRUD dengan soft delete

- **[AuditLogModel.php](./app/Models/AuditLogModel.php)**

  - Track perubahan di sistem

- **[VisitorModel.php](./app/Models/VisitorModel.php)**
  - Track pengunjung website

### Configuration dengan Komentar

- **[app/Config/Routes.php](./app/Config/Routes.php)** ✅ SUDAH DIKOMENTAR
  - Semua route grouped by role
  - Public, User, Admin, Admin Bidang routes

---

## 📊 Learning Path (Rekomendasi)

### Hari 1: Dasar-Dasar

- [ ] Baca QUICK_START_GUIDE (15 menit)
- [ ] Baca DOKUMENTASI_APLIKASI (30 menit)
- [ ] Pahami Routes.php structure (15 menit)
- [ ] Coba login sebagai user (10 menit)

### Hari 2: Database & Models

- [ ] Baca DATABASE_MODELS_DOCUMENTATION (25 menit)
- [ ] Baca UserModel & ReportModel (15 menit)
- [ ] Pahami 5 tabel utama (20 menit)
- [ ] Coba query manual di MySQL (15 menit)

### Hari 3: Controllers & Views

- [ ] Baca VIEWS_DOCUMENTATION (20 menit)
- [ ] Pahami Auth.php flow (20 menit)
- [ ] Pahami Home.php & BaseController (15 menit)
- [ ] Coba akses berbagai page (15 menit)

### Hari 4: Advanced

- [ ] Pahami LaporanAnon submission (20 menit)
- [ ] Pahami Admin dashboard & filtering (20 menit)
- [ ] Pahami AdminBidang verification flow (15 menit)
- [ ] Test end-to-end: submit → verify → complete (30 menit)

---

## 🔗 Map Dokumentasi

```
DOKUMENTASI APLIKASI
├── Overview Aplikasi
├── Technology Stack
├── Struktur Database
├── Struktur Folder & File
├── User Roles & Access Control
├── Alur Proses Utama (3 alur)
└── API Routes Lengkap

QUICK START GUIDE
├── File Penting (Urutan Belajar)
├── Alur Utama Aplikasi
├── Role-Based Access Control
├── Status Laporan
├── Database Schema
├── Code Snippets
├── Folder Structure
└── Debugging Tips

DATABASE_MODELS_DOCUMENTATION
├── Database Schema (5 tabel)
├── Field Details Lengkap
├── Model Details & Methods
├── Query Builder Examples
├── Best Practices
└── Useful Queries

VIEWS_DOCUMENTATION
├── Struktur Folder Views
├── Detail Setiap View
├── Template Partials
├── Blade/Template Syntax
├── CSS & Bootstrap Classes
├── Form Helpers
├── AJAX Integration
└── Best Practices
```

---

## 🚀 Quick Reference

### File Paling Penting

| File               | Fungsi                 | Prioritas |
| ------------------ | ---------------------- | --------- |
| Routes.php         | URL routing            | 🔴 HIGH   |
| Auth.php           | Login/Register         | 🔴 HIGH   |
| ReportModel.php    | Report queries         | 🔴 HIGH   |
| UserModel.php      | User queries           | 🔴 HIGH   |
| Home.php           | Landing page           | 🟠 MEDIUM |
| Admin.php          | Admin dashboard        | 🟠 MEDIUM |
| AdminBidang.php    | Admin bidang dashboard | 🟠 MEDIUM |
| BaseController.php | Shared methods         | 🟡 LOW    |
| Views/             | UI templates           | 🟡 LOW    |

### URL Penting untuk Testing

```
Landing Page:      http://localhost:8080/
Login:             http://localhost:8080/login
Register:          http://localhost:8080/register
Forgot Password:   http://localhost:8080/auth/forgot
Report Anonim:     http://localhost:8080/laporan-anon/submit
Check Status:      http://localhost:8080/laporan-anon/check-status

Admin Dashboard:   http://localhost:8080/admin (role: admin)
Admin Bidang:      http://localhost:8080/admin-bidang (role: admin_bidang)
User Dashboard:    http://localhost:8080/user/dashboard (role: user)
```

### Database Tables

```
users
├── id
├── username (unik)
├── email (unik)
├── password_hash
├── role (user, admin, admin_bidang)
├── status (1=aktif, 0=nonaktif)
└── kebidangan (untuk admin_bidang)

reports
├── id
├── tracking_code (LAPORDAN-xxxxx)
├── category
├── location
├── chronology
├── evidence_1/2/3
├── status (pending, verified, disposition, progress, completed, archived, rejected)
├── note
├── verified_by
└── verified_at

report_status_logs
├── id
├── report_id
├── old_status
├── new_status
├── changed_by
└── updated_at

password_reset
├── id
├── email
├── token
└── expires_at

visitors
├── id
├── ip_address
├── user_agent
└── visited_at
```

---

## 💡 Tips Penting

### 1. Pahami Role-Based Access

- **user**: Dashboard pribadi, lihat profil
- **admin_bidang**: Verifikasi laporan untuk bidang mereka
- **admin**: Full access semua laporan & user management
- **public**: Submit laporan anonim, cek status

### 2. Status Laporan adalah Kunci

Pahami alur status: `pending → verified/disposition → progress → completed → archived`

### 3. Security Matters

- Password di-hash dengan `password_hash()`
- Verified dengan `password_verify()`
- Gunakan session untuk track login
- Soft delete (jangan permanent delete)

### 4. Evidence Files

Bukti laporan disimpan di `/public/uploads/reports/`
Akses via `/uploads/reports/{filename}`

### 5. Tracking Code

Format: `LAPORDAN-xxxxx` (unik untuk setiap laporan)
User gunakan ini untuk cek status laporan anonim

---

## 🔍 Debugging Checklist

- [ ] Enable debug mode di App.php
- [ ] Check logs di `/writable/logs/`
- [ ] Gunakan `dd()` untuk dump variable
- [ ] Check session data dengan `dd(session()->get())`
- [ ] Test dengan browser console (F12)
- [ ] Check network tab untuk AJAX requests
- [ ] Verify database dengan phpMyAdmin
- [ ] Check file permissions untuk uploads

---

## 📱 Responsive Design

Aplikasi menggunakan **Bootstrap 4** dan responsive design:

- Desktop: Full layout dengan sidebar
- Tablet: Adaptive layout
- Mobile: Hamburger menu & full-width content

---

## 🎓 Kesimpulan

Dokumentasi ini dirancang untuk:

- ✅ Pemula: QUICK_START_GUIDE
- ✅ Developer: Baca DOKUMENTASI_APLIKASI + Database documentation
- ✅ Advanced: Explore source code dengan komentar

**Waktu Total untuk Paham**: ~2-3 hari dengan dedikasi

---

## 📞 Support & Resources

### File Dokumentasi

1. **QUICK_START_GUIDE.md** - Mulai sini! ⭐
2. **DOKUMENTASI_APLIKASI.md** - Dokumentasi lengkap
3. **DATABASE_MODELS_DOCUMENTATION.md** - Database reference
4. **VIEWS_DOCUMENTATION.md** - View templates reference
5. **README.md** - Setup & installation

### Helpful Links

- CodeIgniter 4 Docs: https://codeigniter.com/user_guide/
- Bootstrap 4 Docs: https://getbootstrap.com/docs/4.0/
- PHP Docs: https://www.php.net/docs.php
- MySQL Docs: https://dev.mysql.com/doc/

### Tanya Jawab Umum

**Q: Bagaimana cara reset password user?**
A: Admin → Manajemen User → Click user → "Reset Password"

**Q: Bagaimana tracking code laporan anonim?**
A: User bisa check status via `/laporan-anon/check-status?code=LAPORDAN-xxxxx`

**Q: Apa bedanya admin vs admin_bidang?**
A: Admin = full access semua, Admin Bidang = hanya laporan untuk bidang mereka

**Q: Di mana bukti laporan disimpan?**
A: `/public/uploads/reports/{filename}`

**Q: Bagaimana alur verifikasi laporan?**
A: Laporan → pending → admin/admin_bidang verify → verified/disposition/rejected

---

## 🎉 Happy Learning!

Semoga dokumentasi ini membantu Anda memahami LaporDAH dengan baik.

Jika ada pertanyaan, silakan cek:

1. QUICK_START_GUIDE terlebih dahulu
2. Baca file source code yang sudah dikomentar
3. Check database schema di DATABASE_MODELS_DOCUMENTATION
4. Tanyakan kepada tim development

**Last Updated**: 2 Februari 2026
**Status**: ✅ Complete dengan semua komentar dan dokumentasi
