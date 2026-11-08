# 📌 Aplikasi Sistem Informasi Pengaduan Masyarakat - LAPORDAH! (CodeIgniter 4)

## 📖 Deskripsi

Aplikasi ini dibuat untuk mengelola pengaduan masyarakat secara digital, mulai dari pembuatan laporan, verifikasi, disposisi, tindak lanjut, hingga pencetakan laporan.

Aplikasi ini dikembangkan menggunakan framework **CodeIgniter 4** sebagai bagian dari tugas akhir untuk Dinas Lingkungan Hidup (DLH) Kota Pekalongan.


---

## ✨ Fitur Utama

- 📝 Buat Aduan + Unggah Bukti Pendukung
- 🔍 Jelajah Laporan & Lacak Status via Kode Tracking
- ✅ Verifikasi & Disposisi Laporan ke Admin Bidang
- 📈 Update Progres Laporan secara Transparan
- 📊 Dashboard Super Admin & Admin Bidang
- 🗂️ Histori, Arsip & Rekap Laporan
- 🖨️ Cetak Laporan Tahunan / Rekap (PDF)
- 👥 Manajemen Pengguna (Admin Bidang & Super Admin)
- 🔎 Pencarian & Filter Data (Status, Kategori, Bidang, Bulan, Tahun)

---

## 👥 Hak Akses

- **Super Admin:** Dashboard, Kelola Data Pengguna, Kelola Data Laporan Pengaduan, Monitoring Riwayat & Rekapitulasi, Kelola Laporan Tahunan
- **Admin Bidang:** Dashboard, Verifikasi & Tindak Lanjut Laporan, Pembaruan Status Pengaduan, Cetak / Unduh per Laporan Selesai
- **Masyarakat:** Pengisian Formulir Pengaduan, Unggah Bukti Pendukung, Pemantauan Status Laporan

---

## 🛠️ Teknologi yang Digunakan

- PHP 8+
- CodeIgniter 4
- MySQL / MariaDB
- Bootstrap (Frontend)
- Chart.js (Dashboard)
- Google Maps (Preview Lokasi)
- mPDF (Export PDF)

---

## 🗂️ Struktur Project (Singkat)

- `app/` → Controller, Model, View
- `public/` → Entry point & assets
- `writable/` → Cache & log (tidak diupload)

---

## 🗄️ Database

Database diatur terpisah oleh pengembang. Pastikan sudah import database sebelum menjalankan aplikasi.

---

## ⚙️ Cara Menjalankan Project

1. Clone repository:

```bash
git clone https://github.com/username/nama-repo.git
2. Masuk ke folder project:
cd nama-repo
3. Install dependency:
composer install
4. Copy file env:
cp env .env
5. Setting database di file `.env`:
database.default.hostname = localhost
database.default.database = nama_database
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
6. Jalankan server:
php spark serve
7. Akses di browser:
http://localhost:8080
---

## 📖 Panduan Penggunaan

Dokumentasi lengkap ada di file `PETUNJUK_PEMAKAIAN_SISTEM.pdf` (36 Halaman):
- *A.* Hak Akses dan Menu Pengguna
- *B.* Panduan Penggunaan Sistem LAPORDAH Admin
- *C.* Petunjuk Pemakaian Sistem LAPORDAH Admin Bidang
- *D.* Petunjuk Pemakaian Sistem LAPORDAH Masyarakat

---

## ⚠️ Catatan

- Folder `writable/` dan file `.env` tidak disertakan di repository
- Akun Admin / Admin Bidang dibuatkan oleh Super Admin (bukan register mandiri)
- Masyarakat melapor tanpa login, tapi wajib isi nama, email / nomor HP yang bisa dihubungi
- Laporan yang belum berstatus `Selesai` tidak bisa diarsipkan

---

## 👨‍💻 Author

*Hermawan Prihatno*
Mahasiswa Teknik Informatika Institut Widya Pratama Pekalongan
Untuk Dinas Lingkungan Hidup (DLH) Kota Pekalongan

---

## 📌 Tujuan Project

Project ini dibuat sebagai:

- Tugas akhir / skripsi
- Media pembelajaran CodeIgniter 4
- Sistem informasi pengaduan masyarakat yang transparan & real-time
- Portofolio pengembangan web

---
