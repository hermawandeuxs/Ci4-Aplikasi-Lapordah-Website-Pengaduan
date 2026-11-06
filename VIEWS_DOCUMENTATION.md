# 📄 DOKUMENTASI VIEWS - LaporDAH

Dokumentasi lengkap struktur dan fungsi view template di LaporDAH.

## Struktur Folder Views

```
/app/Views/
├── auth/                      ← Autentikasi
│   ├── login.php             ← Form login
│   ├── register.php          ← Form register
│   ├── forgot.php            ← Form lupa password
│   └── reset.php             ← Form reset password (via token)
│
├── admin/                     ← Admin dashboard
│   ├── arsip_pdf.php
│   ├── historyLaporan.php    ← History/log perubahan status
│   ├── rekap_laporan_pdf.php
│   └── userManajemen/
│       ├── manajement_user.php  ← List user
│       ├── tambah_user.php      ← Form tambah user
│       └── edit_user.php        ← Form edit user
│
├── admin_bidang/              ← Admin Bidang dashboard
│   └── detail.php            ← Detail laporan
│
├── layouts/                   ← Template layout
│   ├── auth.php              ← Layout untuk auth pages
│   ├── footer.php            ← Footer template
│   ├── header.php            ← Header template
│   └── main.php              ← Main layout template
│
├── universal/                 ← Shared views
│   ├── dashboard.php         ← Dashboard (untuk admin & admin_bidang)
│   ├── laporan_reject.php    ← List laporan reject
│   ├── layout.php            ← Layout untuk universal pages
│   ├── list_report.php       ← List reports
│   ├── print_template.php    ← Template untuk print
│   ├── rekap_laporan.php     ← Recap reports
│   ├── report_arsip.php      ← Archived reports
│   └── report_detail.php     ← Detail laporan
│
├── reports/                   ← Public reports
│   ├── detail_report.php     ← Detail laporan publik
│   └── index.php             ← List reports publik
│
├── pagers/
│   └── bootstrap_pagination.php  ← Pagination template
│
├── errors/
│   ├── cli/
│   └── html/
│
└── Top-level views
    ├── about.php             ← About page
    ├── faqUmum.php          ← FAQ umum
    ├── kebijakanPrivasi.php  ← Kebijakan privasi
    ├── welcome_message.php   ← Landing page
    └── SOP.php              ← SOP page
```

---

## Detail Setiap View

### Authentication Views

#### `auth/login.php`

**Fungsi**: Form login user

**Fitur**:

- Input username/email
- Input password
- "Remember me" checkbox (optional)
- Link ke register & forgot password
- Validasi error messages

**Data yang diterima**:

```php
$errors    // Array of validation errors
$success   // Success message (jika ada)
```

**Flow**:

```
User input username & password
    ↓
POST /auth/attemptLogin
    ↓
Auth::attemptLogin() process
    ↓
Redirect ke dashboard atau back dengan error
```

---

#### `auth/register.php`

**Fungsi**: Form registrasi user baru

**Fitur**:

- Input username
- Input email
- Input password
- Input confirm password
- Validasi real-time
- Link ke login page

**Data yang diterima**:

```php
$errors    // Array of validation errors
```

**Validasi Rules**:

- Username: minimal 4 karakter, unique
- Email: valid format, unique
- Password: minimal 6 karakter, match dengan confirm

---

#### `auth/forgot.php`

**Fungsi**: Form lupa password

**Fitur**:

- Input email
- Kirim email dengan reset link

**Data yang diterima**:

```php
$error     // Error message jika ada
$success   // Success message jika email terkirim
```

---

#### `auth/reset.php`

**Fungsi**: Form reset password (via token)

**Fitur**:

- Input password baru
- Input confirm password
- Validasi token dari URL

**Data yang diterima**:

```php
$token     // Token dari URL parameter
$error     // Error message jika token invalid
```

**Security**: Token harus valid dan belum expired (1 jam)

---

### Dashboard Views

#### `universal/dashboard.php`

**Fungsi**: Dashboard untuk admin & admin bidang

**Fitur**:

- Statistik overview (total, pending, verified, completed)
- Grafik laporan bulanan (6 bulan terakhir)
- Top kategori laporan (5 kategori)
- Statistik per bidang (hanya admin)
- Recent reports
- Quick actions (buttons)

**Data yang diterima**:

```php
$title              // Page title
$stats              // Overall statistics
$monthlyStats       // Monthly data untuk chart
$topCategories      // Top 5 categories
$bidangStats        // Stats per bidang (admin only)
$visitorCount       // Total visitors
$userName           // Current user name
$userRole           // Current user role
$kebidangan         // List of kebidangan
```

**Chart Integration**:

- Uses Chart.js untuk visualisasi
- Monthly trend chart
- Category distribution chart

---

### Report Views

#### `universal/list_report.php`

**Fungsi**: List laporan dengan filter & pagination

**Fitur**:

- Filter by status (pending, verified, completed, dll)
- Filter by category
- Sort (newest, oldest)
- Datatable dengan AJAX
- Quick actions (view, edit, delete)

**Data yang diterima**:

```php
$title       // Page title
$reports     // Array of reports
$statuses    // Available statuses
$categories  // Available categories
```

**AJAX Endpoint**:

```
GET /admin/reports/get-data?status=pending&category=korupsi&sort=newest
```

Response:

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tracking_code": "LAPORDAN-xxxxx",
      "category": "korupsi",
      "location": "Pekalongan",
      "status": "pending",
      "created_at_formatted": "01 Feb 2026 10:30",
      "status_badge": "<span class='badge'>Pending</span>"
    }
  ]
}
```

---

#### `universal/report_detail.php`

**Fungsi**: Detail laporan lengkap

**Fitur**:

- Report info (kategori, lokasi, kronologi)
- Pelapor info (nama, email, HP)
- Evidence images (hingga 3 file)
- Status history / timeline
- Action buttons (verify, reject, progress, dll)
- Admin notes/dispositions

**Data yang diterima**:

```php
$title           // Page title
$report          // Report data
$evidence_urls   // Array of evidence image URLs
$statusLogs      // Array of status change history
$sumber          // Source (anonim atau terdaftar)
```

**Status Timeline**:
Menampilkan history perubahan status dengan waktu & user yang mengubah.

---

### User Views

#### `admin/userManajemen/manajement_user.php`

**Fungsi**: List user untuk admin management

**Fitur**:

- List semua user (admin, admin_bidang, user)
- Filter by role
- Search by username/email
- Action: edit, delete, reset password

**Data yang diterima**:

```php
$title   // Page title
$users   // Array of users
```

---

#### `admin/userManajemen/tambah_user.php`

**Fungsi**: Form tambah user baru

**Fitur**:

- Input nama user
- Input username
- Input email
- Input password
- Select role (user, admin, admin_bidang)
- Select kebidangan (untuk admin_bidang)
- Input status (aktif/nonaktif)

**Data yang diterima**:

```php
$title           // Page title
$kebidanganList  // List of kebidangan (for dropdown)
```

---

#### `admin/userManajemen/edit_user.php`

**Fungsi**: Form edit user

**Fitur**:

- Edit nama, username, email, role
- Edit kebidangan
- Edit status
- Reset password option

**Data yang diterima**:

```php
$title    // Page title
$user     // User data to edit
$kebidanganList
```

---

## Template Partials

### `layouts/header.php`

**Konten**:

- Navigation bar
- User menu (profile, logout)
- Logo

**Variables**:

```php
session()->get('username')  // Current user
session()->get('role')      // Current role
session()->get('userPhoto') // User photo
```

---

### `layouts/footer.php`

**Konten**:

- Footer info
- Copyright
- Links

---

### `layouts/main.php`

**Layout wrapper** untuk semua page (selain auth)

**Structure**:

```html
<!DOCTYPE html>
<html>
  <head>
    <!-- Meta, CSS, dll -->
  </head>
  <body>
    <?php include 'header.php'; ?>
    <main><?php echo $this->renderSection('content'); ?></main>
    <?php include 'footer.php'; ?>
  </body>
</html>
```

---

### `layouts/auth.php`

**Layout wrapper** untuk auth pages (login, register, forgot)

**Structure**:

```html
<!DOCTYPE html>
<html>
  <head>
    <!-- Meta, CSS -->
  </head>
  <body class="auth-page">
    <div class="auth-container">
      <?php echo $this->renderSection('content'); ?>
    </div>
  </body>
</html>
```

---

## Blade/Template Syntax

View di CodeIgniter 4 menggunakan PHP native (bukan Blade):

```php
<!-- Display variable -->
<?php echo $variable; ?>
<?= $variable; ?>

<!-- Loop -->
<?php foreach($items as $item): ?>
    <p><?= $item['name']; ?></p>
<?php endforeach; ?>

<!-- Conditional -->
<?php if($user['role'] === 'admin'): ?>
    <button>Admin Action</button>
<?php endif; ?>

<!-- Include partial -->
<?php include 'header.php'; ?>

<!-- Render section (layout inheritance) -->
<?php echo $this->renderSection('content'); ?>

<!-- Set section -->
<?php $this->section('content'); ?>
    <h1>Page content</h1>
<?php $this->endSection(); ?>
```

---

## CSS Classes & Styling

Aplikasi menggunakan **Bootstrap 4**:

```html
<!-- Button -->
<button class="btn btn-primary">Submit</button>
<button class="btn btn-danger">Delete</button>

<!-- Alert -->
<div class="alert alert-success">Success message</div>
<div class="alert alert-danger">Error message</div>

<!-- Badge -->
<span class="badge badge-success">Verified</span>
<span class="badge badge-warning">Pending</span>

<!-- Card -->
<div class="card">
  <div class="card-header">Title</div>
  <div class="card-body">Content</div>
</div>

<!-- Table -->
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Column 1</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Data</td>
    </tr>
  </tbody>
</table>
```

---

## Form Helpers

CodeIgniter menyediakan form helper yang memudahkan pembuatan form:

```php
<!-- Open form -->
<?php echo form_open('auth/attemptLogin', ['method' => 'POST']); ?>

<!-- Input field -->
<?php echo form_input('username', '', ['class' => 'form-control']); ?>

<!-- Password field -->
<?php echo form_password('password', '', ['class' => 'form-control']); ?>

<!-- Submit button -->
<?php echo form_submit('submit', 'Login', ['class' => 'btn btn-primary']); ?>

<!-- Close form -->
<?php echo form_close(); ?>
```

---

## AJAX Integration

Contoh AJAX request dari view:

```javascript
// Get reports data
$.ajax({
  url: "/admin/reports/get-data",
  method: "GET",
  data: {
    status: "pending",
    category: "korupsi",
    sort: "newest",
  },
  dataType: "json",
  success: function (response) {
    if (response.success) {
      console.log(response.data);
      // Update table dengan response data
    }
  },
});

// Submit form
$.ajax({
  url: "/laporan-anon/submit",
  method: "POST",
  data: new FormData(document.querySelector("form")),
  processData: false,
  contentType: false,
  dataType: "json",
  success: function (response) {
    if (response.success) {
      console.log("Tracking code:", response.tracking_code);
    } else {
      console.log("Error:", response.message);
    }
  },
});
```

---

## Best Practices

1. **DRY (Don't Repeat Yourself)**

   - Gunakan partial/include untuk komponen yang sering dipakai
   - Contoh: header.php, footer.php, sidebar.php

2. **Security**

   - Selalu escape output untuk mencegah XSS
   - Gunakan `htmlspecialchars()` atau helper function
   - Validasi input di controller sebelum render view

3. **Performance**

   - Load data di controller, pass ke view
   - Jangan query database di view
   - Gunakan caching untuk data yang sering diakses

4. **Readability**

   - Gunakan meaningful variable names
   - Indent code dengan konsisten
   - Tambahkan comments untuk logic yang kompleks

5. **Responsive Design**
   - Gunakan Bootstrap grid system
   - Test di berbagai screen size
   - Gunakan mobile-first approach

---

## Tips Debugging Views

### 1. Print Variable

```php
<?php d($variable); ?>  <!-- Dump only -->
<?php dd($variable); ?> <!-- Dump & die -->
```

### 2. Check Array Keys

```php
<?php
if (isset($user['name'])) {
    echo "Name: " . $user['name'];
} else {
    echo "Name key tidak ada!";
}
?>
```

### 3. Log to File

```php
<?php log_message('debug', 'View variable: ' . json_encode($data)); ?>
```

### 4. Browser Console

Gunakan `console.log()` di JavaScript untuk debug AJAX response.

---

## Kesimpulan

Views di LaporDAH menggunakan PHP native tanpa framework template khusus. Pahami struktur folder, layout inheritance, dan form helpers untuk produktivitas maksimal!

Selamat belajar! 🎉
