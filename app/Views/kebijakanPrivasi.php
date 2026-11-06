<?= $this->include('layouts/header') ?>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kebijakan Privasi | LaporDAH</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fc;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .privacy-card {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(40,167,69,0.08);
            padding: 32px 28px;
        }
        .privacy-title {
            color: #28a745;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .privacy-section-title {
            color: #218838;
            font-weight: 600;
            margin-top: 28px;
            margin-bottom: 10px;
        }
        .privacy-icon {
            color: #28a745;
            margin-right: 8px;
        }
        ul {
            margin-left: 1.2em;
        }
    </style>
</head>
<body>
    <div class="privacy-card">
        <h2 class="privacy-title"><i class="bi bi-shield-lock privacy-icon"></i>Kebijakan Privasi LaporDAH</h2>
        <p>
            Selamat datang di <b>LaporDAH</b>! Kami berkomitmen untuk menjaga privasi dan keamanan data pribadi Anda. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi yang Anda berikan saat menggunakan layanan kami.
        </p>

        <h4 class="privacy-section-title"><i class="bi bi-person-lock privacy-icon"></i>Informasi yang Kami Kumpulkan</h4>
        <ul>
            <li><b>Data Pribadi:</b> Nama, alamat, email, nomor telepon, dan informasi lain yang Anda masukkan saat membuat laporan atau mendaftar.</li>
            <li><b>Data Laporan:</b> Kategori, lokasi, kronologi, foto, dan bukti pendukung laporan.</li>
            <li><b>Data Teknis:</b> Alamat IP, jenis perangkat, browser, dan data akses lainnya yang terekam secara otomatis.</li>
        </ul>

        <h4 class="privacy-section-title"><i class="bi bi-shield-check privacy-icon"></i>Penggunaan Informasi</h4>
        <ul>
            <li>Memproses dan menindaklanjuti laporan yang Anda kirimkan.</li>
            <li>Meningkatkan kualitas layanan dan keamanan sistem.</li>
            <li>Menghubungi Anda terkait tindak lanjut laporan atau informasi penting lainnya.</li>
            <li>Melakukan analisis statistik tanpa mengungkap identitas pribadi.</li>
        </ul>

        <h4 class="privacy-section-title"><i class="bi bi-eye-slash privacy-icon"></i>Perlindungan & Kerahasiaan Data</h4>
        <ul>
            <li>Data pribadi Anda <b>tidak akan dibagikan</b> kepada pihak ketiga tanpa izin, kecuali diwajibkan oleh hukum.</li>
            <li>Kami menerapkan langkah-langkah keamanan teknis dan administratif untuk melindungi data Anda.</li>
            <li>Identitas pelapor dijaga kerahasiaannya dan hanya digunakan untuk keperluan verifikasi laporan.</li>
        </ul>

        <h4 class="privacy-section-title"><i class="bi bi-gear privacy-icon"></i>Hak Pengguna</h4>
        <ul>
            <li>Anda dapat memperbarui atau menghapus data pribadi melalui pengaturan akun.</li>
            <li>Anda berhak meminta penghapusan laporan tertentu dengan alasan yang jelas.</li>
        </ul>

        <h4 class="privacy-section-title"><i class="bi bi-link-45deg privacy-icon"></i>Tautan ke Situs Lain</h4>
        <p>
            Situs kami dapat berisi tautan ke situs eksternal. Kami tidak bertanggung jawab atas kebijakan privasi situs tersebut.
        </p>

        <h4 class="privacy-section-title"><i class="bi bi-info-circle privacy-icon"></i>Perubahan Kebijakan</h4>
        <p>
            Kebijakan privasi ini dapat diperbarui sewaktu-waktu. Perubahan akan diinformasikan melalui situs ini.
        </p>

        <h4 class="privacy-section-title"><i class="bi bi-envelope privacy-icon"></i>Kontak</h4>
        <p>
            Jika Anda memiliki pertanyaan atau permintaan terkait privasi, silakan hubungi kami di <a href="mailto:dlhpekalongankota.go.id">dlhpekalongankota.go.id</a> atau melalui kontak yang tersedia di halaman utama.
        </p>
        <hr>
        <div class="text-center text-secondary small mt-3">
            &copy; <?= date('Y') ?> Dinas Lingkungan Hidup Kota Pekalongan. Seluruh hak cipta dilindungi undang-undang.
        </div>
    </div>
</body>
<?= $this->include('layouts/footer') ?>

