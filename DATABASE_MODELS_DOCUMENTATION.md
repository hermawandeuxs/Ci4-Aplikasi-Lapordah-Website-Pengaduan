# 🗄️ DOKUMENTASI DATABASE & MODELS - LaporDAH

Panduan lengkap struktur database dan bagaimana menggunakannya melalui Models.

---

## 📊 Database Schema

### 1. Tabel `users`

Menyimpan data pengguna aplikasi.

**Struktur**:

```sql
CREATE TABLE users (
    id                INT PRIMARY KEY AUTO_INCREMENT,
    nama_user         VARCHAR(100) NOT NULL,
    username          VARCHAR(50) UNIQUE NOT NULL,
    email             VARCHAR(100) UNIQUE NOT NULL,
    password_hash     VARCHAR(255) NOT NULL,
    photo             VARCHAR(255) NULL,
    role              ENUM('user', 'admin', 'admin_bidang') DEFAULT 'user',
    status            TINYINT(1) DEFAULT 1,          -- 1=aktif, 0=nonaktif
    kebidangan        VARCHAR(100) NULL,             -- Bidang (untuk admin_bidang)
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Field Details**:

| Field         | Type         | Nullable | Default           | Keterangan                        |
| ------------- | ------------ | -------- | ----------------- | --------------------------------- |
| id            | INT          | ❌       | AUTO_INCREMENT    | Primary Key                       |
| nama_user     | VARCHAR(100) | ❌       | -                 | Nama lengkap                      |
| username      | VARCHAR(50)  | ❌       | -                 | Unik, untuk login                 |
| email         | VARCHAR(100) | ❌       | -                 | Unik, untuk login/reset pw        |
| password_hash | VARCHAR(255) | ❌       | -                 | Password yang sudah di-hash       |
| photo         | VARCHAR(255) | ✅       | NULL              | Path foto profil                  |
| role          | ENUM         | ❌       | 'user'            | user, admin, atau admin_bidang    |
| status        | TINYINT(1)   | ❌       | 1                 | 1=aktif, 0=nonaktif               |
| kebidangan    | VARCHAR(100) | ✅       | NULL              | Bidang (wajib untuk admin_bidang) |
| created_at    | TIMESTAMP    | ❌       | CURRENT_TIMESTAMP | Created time                      |
| updated_at    | TIMESTAMP    | ❌       | CURRENT_TIMESTAMP | Updated time                      |

**Model File**: `/app/Models/UserModel.php`

**Common Queries**:

```php
// Get all users
$users = $userModel->findAll();

// Get user by ID
$user = $userModel->find($id);

// Get user by username/email (for login)
$user = $userModel->getUserByLogin('username_or_email');

// Get user's kebidangan
$kebidangan = $userModel->getKebidanganById($userId);

// Create new user
$userModel->save([
    'username' => 'user123',
    'email' => 'user@example.com',
    'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
    'nama_user' => 'Nama Lengkap',
    'role' => 'user',
    'status' => 1
]);

// Update user
$userModel->update($id, [
    'nama_user' => 'Nama Baru',
    'email' => 'newemail@example.com'
]);

// Delete user
$userModel->delete($id);
```

---

### 2. Tabel `reports` (Tabel Utama)

Menyimpan semua laporan pengaduan.

**Struktur**:

```sql
CREATE TABLE reports (
    id                INT PRIMARY KEY AUTO_INCREMENT,
    tracking_code     VARCHAR(50) UNIQUE NOT NULL,   -- Format: LAPORDAN-xxxxx
    category          VARCHAR(100) NULL,             -- Kategori laporan
    other_category    VARCHAR(100) NULL,             -- Kategori custom
    location          VARCHAR(255) NOT NULL,
    latitude          DECIMAL(10, 8) NULL,
    longitude         DECIMAL(11, 8) NULL,
    chronology        LONGTEXT NOT NULL,             -- Deskripsi kejadian
    nama_pelapor      VARCHAR(100) NULL,
    alamat_pelapor    VARCHAR(255) NULL,
    email_pelapor     VARCHAR(100) NULL,
    hp_pelapor        VARCHAR(20) NULL,
    evidence_1        VARCHAR(255) NULL,             -- Nama file bukti
    evidence_2        VARCHAR(255) NULL,
    evidence_3        VARCHAR(255) NULL,
    status            ENUM('pending', 'verified', 'disposition', 'progress',
                          'completed', 'archived', 'rejected') DEFAULT 'pending',
    note              LONGTEXT NULL,                 -- Catatan/disposisi
    verified_by       INT NULL,                      -- FK: users.id
    verified_at       DATETIME NULL,
    deleted_by        INT NULL,                      -- FK: users.id
    deleted_at        DATETIME NULL,                 -- Soft delete
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Field Details**:

| Field          | Type         | Nullable | Default           | Keterangan                 |
| -------------- | ------------ | -------- | ----------------- | -------------------------- |
| id             | INT          | ❌       | AUTO_INCREMENT    | Primary Key                |
| tracking_code  | VARCHAR(50)  | ❌       | -                 | Kode unik tracking         |
| category       | VARCHAR(100) | ✅       | NULL              | Kategori laporan           |
| other_category | VARCHAR(100) | ✅       | NULL              | Kategori custom            |
| location       | VARCHAR(255) | ❌       | -                 | Lokasi kejadian            |
| latitude       | DECIMAL      | ✅       | NULL              | Koordinat latitude         |
| longitude      | DECIMAL      | ✅       | NULL              | Koordinat longitude        |
| chronology     | LONGTEXT     | ❌       | -                 | Deskripsi kejadian         |
| nama_pelapor   | VARCHAR(100) | ✅       | NULL              | Nama pelapor               |
| alamat_pelapor | VARCHAR(255) | ✅       | NULL              | Alamat pelapor             |
| email_pelapor  | VARCHAR(100) | ✅       | NULL              | Email pelapor              |
| hp_pelapor     | VARCHAR(20)  | ✅       | NULL              | No. HP pelapor             |
| evidence_1/2/3 | VARCHAR(255) | ✅       | NULL              | File bukti (path)          |
| status         | ENUM         | ❌       | 'pending'         | Status laporan             |
| note           | LONGTEXT     | ✅       | NULL              | Catatan admin              |
| verified_by    | INT          | ✅       | NULL              | ID user yang verifikasi    |
| verified_at    | DATETIME     | ✅       | NULL              | Waktu verifikasi           |
| deleted_by     | INT          | ✅       | NULL              | ID user yang delete        |
| deleted_at     | DATETIME     | ✅       | NULL              | Waktu delete (soft delete) |
| created_at     | TIMESTAMP    | ❌       | CURRENT_TIMESTAMP | Created time               |
| updated_at     | TIMESTAMP    | ❌       | CURRENT_TIMESTAMP | Updated time               |

**Model File**: `/app/Models/ReportModel.php`

**Status Flow**:

```
pending → verified/disposition → progress → completed → archived
       ↓
       rejected
```

**Common Queries**:

```php
// Get all reports
$reports = $reportModel->findAll();

// Get report by ID
$report = $reportModel->find($id);

// Get report by tracking code
$report = $reportModel->where('tracking_code', 'LAPORDAN-xxxxx')->first();

// Get reports by status
$pending = $reportModel->where('status', 'pending')->findAll();
$completed = $reportModel->where('status', 'completed')->findAll();

// Get reports with multiple status
$inProgress = $reportModel->whereIn('status', ['verified', 'disposition', 'progress'])->findAll();

// Count reports
$total = $reportModel->countAllResults();
$pendingCount = $reportModel->where('status', 'pending')->countAllResults();

// Create report
$reportModel->insert([
    'tracking_code' => 'LAPORDAN-' . uniqid(),
    'category' => 'korupsi',
    'location' => 'Pekalongan, Jawa Tengah',
    'chronology' => 'Deskripsi kejadian...',
    'status' => 'pending'
]);

// Update report status
$reportModel->update($id, [
    'status' => 'verified',
    'verified_by' => $userId,
    'verified_at' => date('Y-m-d H:i:s'),
    'note' => 'Laporan sudah diverifikasi'
]);

// Soft delete (set deleted_at)
$reportModel->update($id, [
    'deleted_at' => date('Y-m-d H:i:s'),
    'deleted_by' => $userId
]);

// Get non-deleted reports only
$reports = $reportModel->where('deleted_at IS NULL')->findAll();
```

---

### 3. Tabel `report_status_logs`

Mencatat history perubahan status laporan.

**Struktur**:

```sql
CREATE TABLE report_status_logs (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    report_id       INT NOT NULL,                   -- FK: reports.id
    old_status      VARCHAR(50) NULL,
    new_status      VARCHAR(50) NOT NULL,
    changed_by      INT NOT NULL,                   -- FK: users.id
    note            LONGTEXT NULL,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);
```

**Field Details**:

| Field      | Type        | Nullable | Default           | Keterangan                     |
| ---------- | ----------- | -------- | ----------------- | ------------------------------ |
| id         | INT         | ❌       | AUTO_INCREMENT    | Primary Key                    |
| report_id  | INT         | ❌       | -                 | FK ke reports.id               |
| old_status | VARCHAR(50) | ✅       | NULL              | Status sebelumnya              |
| new_status | VARCHAR(50) | ❌       | -                 | Status baru                    |
| changed_by | INT         | ❌       | -                 | FK ke users.id (yang mengubah) |
| note       | LONGTEXT    | ✅       | NULL              | Catatan perubahan              |
| updated_at | DATETIME    | ❌       | CURRENT_TIMESTAMP | Waktu perubahan                |

**Model File**: `/app/Models/ReportStatusLogModel.php`

**Common Queries**:

```php
// Get status history untuk satu laporan
$logs = $statusLogModel->where('report_id', $reportId)
                        ->orderBy('updated_at', 'ASC')
                        ->findAll();

// Get logs dengan user info (join)
$logs = $statusLogModel->select('report_status_logs.*, users.username')
                        ->join('users', 'users.id = report_status_logs.changed_by')
                        ->where('report_id', $reportId)
                        ->findAll();

// Record status change
$statusLogModel->insert([
    'report_id' => $reportId,
    'old_status' => $oldStatus,
    'new_status' => $newStatus,
    'changed_by' => $userId,
    'note' => 'Catatan perubahan'
]);
```

---

### 4. Tabel `password_reset`

Menyimpan token untuk reset password.

**Struktur**:

```sql
CREATE TABLE password_reset (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    email       VARCHAR(100) NOT NULL,
    token       VARCHAR(255) UNIQUE NOT NULL,
    expires_at  DATETIME NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Field Details**:

| Field      | Type         | Nullable | Default           | Keterangan             |
| ---------- | ------------ | -------- | ----------------- | ---------------------- |
| id         | INT          | ❌       | AUTO_INCREMENT    | Primary Key            |
| email      | VARCHAR(100) | ❌       | -                 | Email user             |
| token      | VARCHAR(255) | ❌       | -                 | Token unik untuk reset |
| expires_at | DATETIME     | ❌       | -                 | Waktu kadaluarsa token |
| created_at | TIMESTAMP    | ❌       | CURRENT_TIMESTAMP | Waktu pembuatan        |

**Model File**: `/app/Models/PasswordResetModel.php`

**Common Queries**:

```php
// Create reset token
$passwordResetModel->insert([
    'email' => 'user@example.com',
    'token' => bin2hex(random_bytes(32)),
    'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))
]);

// Verify token
$reset = $passwordResetModel->where('token', $token)->first();
if ($reset && strtotime($reset['expires_at']) > time()) {
    // Token valid
}

// Delete token setelah digunakan
$passwordResetModel->where('token', $token)->delete();
```

---

### 5. Tabel `visitors`

Tracking pengunjung website.

**Struktur**:

```sql
CREATE TABLE visitors (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    ip_address  VARCHAR(50) NOT NULL,
    user_agent  LONGTEXT NULL,
    visited_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**Common Queries**:

```php
// Record visitor
$visitorModel->insert([
    'ip_address' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    'visited_at' => date('Y-m-d H:i:s')
]);

// Get total unique visitors
$total = $visitorModel->select('DISTINCT ip_address')->countAllResults();

// Get visitors per day
$todayVisitors = $visitorModel->where('DATE(visited_at)', date('Y-m-d'))->countAllResults();
```

---

## 📚 Models Detail

### UserModel

**File**: `/app/Models/UserModel.php`

**Methods**:

```php
// Get user by username or email
public function getUserByLogin($login)
{
    return $this->where('username', $login)
                ->orWhere('email', $login)
                ->first();
}

// Get user's kebidangan
public function getKebidanganById($userId)
{
    return $this->select('kebidangan')
                ->where('id', $userId)
                ->first();
}
```

**Usage**:

```php
$userModel = new UserModel();

// Login validation
$user = $userModel->getUserByLogin('username');
if ($user && password_verify($password, $user['password_hash'])) {
    // Login success
}

// Get kebidangan for admin_bidang
$kebidangan = $userModel->getKebidanganById($userId);
```

---

### ReportModel

**File**: `/app/Models/ReportModel.php`

**Methods**:

```php
// Get total reports
public function getTotalReports()
{
    return $this->countAllResults();
}

// Get reports by status
public function getReportsByStatus($status)
{
    return $this->where('status', $status)->findAll();
}

// Get reports with filters & pagination
public function getReports($category = 'all', $sort = 'newest', $perPage = 10)
{
    // Implementation
}
```

**Usage**:

```php
$reportModel = new ReportModel();

// Get pending reports
$pending = $reportModel->where('status', 'pending')->findAll();

// Get reports by category
$korupsi = $reportModel->where('category', 'korupsi')->findAll();

// Get total reports
$total = $reportModel->getTotalReports();

// Count by status
$pendingCount = $reportModel->where('status', 'pending')->countAllResults();
```

---

## 🔗 Query Builder Examples

CodeIgniter 4 menggunakan Query Builder untuk membuat query yang aman dari SQL injection.

### Basic Queries

```php
$db = \Config\Database::connect();

// SELECT
$result = $db->table('reports')->get()->getResultArray();

// WHERE
$result = $db->table('reports')
    ->where('status', 'pending')
    ->get()
    ->getResultArray();

// WHERE IN
$result = $db->table('reports')
    ->whereIn('status', ['pending', 'verified'])
    ->get()
    ->getResultArray();

// WHERE NOT IN
$result = $db->table('reports')
    ->whereNotIn('status', ['archived', 'rejected'])
    ->get()
    ->getResultArray();

// ORDER BY
$result = $db->table('reports')
    ->orderBy('created_at', 'DESC')
    ->get()
    ->getResultArray();

// LIMIT & OFFSET
$result = $db->table('reports')
    ->limit(10, 20)  // 10 items, offset 20
    ->get()
    ->getResultArray();

// COUNT
$count = $db->table('reports')
    ->where('status', 'pending')
    ->countAllResults();
```

### JOIN Queries

```php
// INNER JOIN
$result = $db->table('reports')
    ->join('users', 'users.id = reports.verified_by')
    ->select('reports.*, users.username')
    ->get()
    ->getResultArray();

// LEFT JOIN
$result = $db->table('reports')
    ->select('reports.*, users.username')
    ->join('users', 'users.id = reports.verified_by', 'left')
    ->get()
    ->getResultArray();
```

### INSERT, UPDATE, DELETE

```php
// INSERT
$db->table('reports')->insert([
    'tracking_code' => 'LAPORDAN-xxxxx',
    'category' => 'korupsi',
    'location' => 'Pekalongan',
    'status' => 'pending'
]);

// UPDATE
$db->table('reports')
    ->where('id', 1)
    ->update([
        'status' => 'verified',
        'verified_at' => date('Y-m-d H:i:s')
    ]);

// DELETE
$db->table('reports')->where('id', 1)->delete();
```

---

## 🎯 Best Practices

### 1. **Use Models, Not Raw Queries**

```php
// ✅ Good
$report = $reportModel->find($id);

// ❌ Bad
$report = $db->query("SELECT * FROM reports WHERE id = $id");
```

### 2. **Hash Passwords Properly**

```php
// ✅ Good
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// ❌ Bad
$plainPassword = 'password123'; // Never store plain passwords
```

### 3. **Use Soft Deletes**

```php
// ✅ Good - Mark as deleted
$reportModel->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

// Query non-deleted
$reports = $reportModel->where('deleted_at IS NULL')->findAll();

// ❌ Bad - Permanent delete (data loss!)
$reportModel->delete($id);
```

### 4. **Validate Input Before Insert**

```php
// ✅ Good
if ($reportModel->validate($data)) {
    $reportModel->save($data);
}

// ❌ Bad - No validation
$reportModel->insert($data);
```

### 5. **Use Transactions for Related Operations**

```php
$db = \Config\Database::connect();
$db->transBegin();

try {
    // Insert report
    $reportId = $reportModel->insert($reportData);

    // Insert status log
    $statusLogModel->insert([
        'report_id' => $reportId,
        'new_status' => 'pending'
    ]);

    $db->transCommit();
} catch (\Exception $e) {
    $db->transRollback();
    throw $e;
}
```

---

## 📊 Useful Queries

### Get Report Stats

```php
// Total reports by status
$stats = $db->table('reports')
    ->select('status, COUNT(*) as count')
    ->groupBy('status')
    ->get()
    ->getResultArray();

// Result:
// [
//   ['status' => 'pending', 'count' => 5],
//   ['status' => 'verified', 'count' => 3],
//   ...
// ]
```

### Get Monthly Stats

```php
$monthlyStats = $db->table('reports')
    ->select('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
    ->groupBy('month')
    ->orderBy('month', 'DESC')
    ->get()
    ->getResultArray();
```

### Get Top Categories

```php
$topCategories = $db->table('reports')
    ->select('category, COUNT(*) as total')
    ->where('category IS NOT NULL')
    ->groupBy('category')
    ->orderBy('total', 'DESC')
    ->limit(5)
    ->get()
    ->getResultArray();
```

---

## 🔐 Security Notes

1. **Always hash passwords**: `password_hash($password, PASSWORD_DEFAULT)`
2. **Verify passwords**: `password_verify($input, $hash)`
3. **Use prepared statements**: Query Builder handles this automatically
4. **Escape user input**: Don't concatenate user input into queries
5. **Use soft deletes**: Don't permanently delete sensitive data
6. **Validate input**: Validate before insert/update
7. **Use transactions**: For related operations

---

## Kesimpulan

Database LaporDAH terdiri dari 5 tabel utama yang terintegrasi dengan baik. Pahami struktur setiap tabel dan gunakan Models untuk query data dengan aman!

Selamat belajar! 🎉
