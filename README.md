# 📌 Aplikasi E-Pengaduan Masyarakat (LAPORDAH!) - Dinas Lingkungan Hidup Kota Pekalongan

## 📖 Deskripsi
**LAPORDAH!** (Laporan Daerah) adalah aplikasi berbasis web yang digunakan untuk mendukung pengelolaan pengaduan masyarakat mengenai masalah lingkungan hidup secara digital dan *real-time*. Sistem ini memfasilitasi proses penerimaan laporan dari masyarakat, verifikasi status, disposisi ke bidang terkait, hingga pemantauan penyelesaian pengaduan secara transparan.

Aplikasi ini dikembangkan untuk mempermudah masyarakat dalam melaporkan permasalahan lingkungan (seperti perapihan pohon, sampah, pencemaran, dll.) serta membantu pihak DLH Kota Pekalongan dalam menindaklanjutinya secara terstruktur.

---

## ✨ Fitur Utama & Hak Akses Pengguna

Sistem ini memiliki pembagian 3 hak akses utama untuk menjaga keamanan dan prosedur kerja:

### 1. Super Admin (Dinas Lingkungan Hidup)
- 🔐 Login Akun & Dashboard Utama
- 👥 Kelola Data Pengguna (Tambah/Hapus/Reset Password Admin Bidang)
- 📁 Kelola & Verifikasi Data Laporan Pengaduan
- 🔄 Disposisi Laporan masuk ke Admin Bidang terkait
- 🖨️ Cetak & Rekapitulasi Laporan Pengaduan Tahunan/Bulanan (Ekspor PDF)

### 2. Admin Bidang (Sekretariat, PPKL, TALING, Kebersihan)
- 📊 Dashboard Statistik Khusus Bidang (Grafik tren 6 bulan terakhir)
- ✅ Verifikasi & Tindak Lanjut Laporan (Setujui / Tolak Aduan beserta catatan)
- 📈 Pembaruan Status Pengaduan (*Set Progress* & *Set Completed*)
- 🖼️ Unggah Bukti Penyelesaian Dokumen (Foto / PDF hasil kerja)

### 3. Masyarakat (Pelapor Daring)
- 📑 Pengisian Formulir Pengaduan (3 Langkah Mudah: Ketentuan, Data Diri, Form Laporan)
- 📍 Integrasi Peta / GPS untuk pengambilan lokasi kejadian otomatis
- 📸 Unggah Bukti Pendukung (Maksimal 3 foto format JPG/PNG/JPEG, maks 2MB per file)
- 🔍 Pemantauan Status Laporan secara *real-time* menggunakan Kode Tracking di menu Jelajah Laporan

---

## 🛠️ Teknologi yang Digunakan
- PHP 8+
- CodeIgniter 4
- MySQL / MariaDB
- Bootstrap (Frontend)
- Google Maps API / Leaflet (Sistem Peta Geokoding Lokasi Otomatis)
- mPDF / DomPDF (Export Rekap & Arsip Laporan ke PDF)

---

## 🗂️ Struktur Project (Singkat)
- `app/` → Controller, Model, View (Logika Aplikasi LAPORDAH)
- `public/` → Entry point, assets, & berkas gambar manual book
- `writable/` → Cache, log, dan folder unggahan bukti aduan masyarakat (`uploads/status_log/`)

---

## 🗄️ Database
Sebelum menjalankan aplikasi, pastikan Anda telah membuat database baru di phpMyAdmin / MySQL dan mengimpor file basis data berikut:
```bash
lapordah_db.sql
```

---

## ⚙️ Cara Menjalankan Project

1. **Clone repository:**
```bash
git clone https://github.com
```

2. **Masuk ke folder project:**
```bash
cd lapordah-dlh
```

3. **Install dependency via Composer:**
```bash
composer install
```

4. **Salin file lingkungan (environment):**
```bash
cp env .env
```

5. **Konfigurasi Database pada file `.env`:**
```bash
database.default.hostname = localhost
database.default.database = nama_database_lapordah
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

6. **Jalankan server lokal CodeIgniter:**
```bash
php spark serve
```

7. **Akses aplikasi melalui browser:**
- Portal Utama / Masyarakat: `http://localhost:8080`
- Halaman Login Instansi: `http://localhost:8080/login`

---

## ⚠️ Catatan Penting
- Folder `writable/` dan file `.env` bersifat privat dan tidak ikut diunggah ke repositori GitHub.
- Penggunaan fitur **Ambil Lokasi Otomatis** pada formulir masyarakat memerlukan izin (*permission*) akses GPS dari peramban/browser yang digunakan.
- Kategori aduan default meliputi: *Pencemaran Lingkungan, Sampah, Perapihan Pohon,* dan *Lainnya*.

---

## 👨‍💻 Author
**Hermawan Prihatno**
Mahasiswa Teknik Informatika Institut Widya Pratama Pekalongan
Untuk Dinas Lingkungan Hidup (DLH) Kota Pekalongan. 

---

## 📌 Tujuan Project

Project ini dibuat sebagai:

- Tugas akhir / skripsi
- Media pembelajaran CodeIgniter 4
- Sistem informasi pengaduan masyarakat yang transparan & real-time
- Portofolio pengembangan web

---


