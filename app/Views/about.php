<!-- filepath: app/Views/about.php -->
<?= $this->include('layouts/header') ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang LaporDAH</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS (optional, jika belum ada di layout utama) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #f8fafc 100%);
        }
        .about-hero {
            background: linear-gradient(120deg, #28a745 60%, #007bff 100%);
            color: #fff;
            padding: 60px 0 40px 0;
            border-radius: 0 0 40px 40px;
            box-shadow: 0 8px 32px rgba(40,167,69,0.08);
        }
        .about-hero h1 {
            font-weight: 700;
            letter-spacing: 1px;
        }
        .about-hero p {
            font-size: 1.2rem;
        }
        .about-section {
            margin-top: -40px;
        }
        .card {
            border: none;
            border-radius: 18px;
        }
        .card .bi {
            font-size: 2.5rem;
        }
        .alert ol {
            padding-left: 1.2em;
        }
       
        
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="about-hero text-center">
        <div class="container" data-aos="fade-down">
            <h1 class="mb-3">Tentang <span style="color:#ffe066;">LaporDAH</span></h1>
            <p class="mb-0">Portal pengaduan lingkungan <b>Kota Pekalongan</b> yang mudah, cepat, dan transparan.<br>
            Bersama menjaga lingkungan, mulai dari laporan Anda!</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-section py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-right">
                    <img src="<?= base_url('assets/icons/logo lapordah.png') ?>" alt="Maskot LaporDAH" class="img-fluid rounded shadow" style="max-width: 320px;">
                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <h2 class="fw-bold mb-3" style="color:#28a745;">Apa itu <span style="color:#007bff;">LaporDAH</span>?</h2>
                    <p class="lead" style="color:#444;">
                        <b>LaporDAH</b> adalah sistem pengaduan online resmi Dinas Lingkungan Hidup Kota Pekalongan. Layanan ini memudahkan masyarakat untuk melaporkan permasalahan lingkungan seperti sampah, pencemaran, pohon tumbang, dan lainnya secara <b>mudah</b>, <b>cepat</b>, dan <b>terintegrasi</b>.
                    </p>
                    <ul class="list-unstyled mt-4">
                        <li data-aos="fade-up" data-aos-delay="100">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Laporan langsung diterima dan diproses oleh DLH Kota Pekalongan.
                        </li>
                        <li data-aos="fade-up" data-aos-delay="200">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Proses pelaporan mudah, cukup isi data dan upload bukti.
                        </li>
                        <li data-aos="fade-up" data-aos-delay="300">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Status laporan dapat dipantau secara real-time.
                        </li>
                        <li data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Data pelapor dijamin kerahasiaannya.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row text-center mt-5">
                <div class="col-md-4 mb-4" data-aos="zoom-in">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <i class="bi bi-people-fill text-success"></i>
                            <h5 class="mt-3 mb-2">Masyarakat Terlibat</h5>
                            <p class="text-muted">Siapapun warga Kota Pekalongan dapat berpartisipasi menjaga lingkungan.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="150">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <i class="bi bi-shield-lock-fill text-primary"></i>
                            <h5 class="mt-3 mb-2">Aman & Terpercaya</h5>
                            <p class="text-muted">Laporan Anda diproses oleh petugas resmi DLH dan data Anda aman.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <i class="bi bi-lightning-charge-fill text-warning"></i>
                            <h5 class="mt-3 mb-2">Respon Cepat</h5>
                            <p class="text-muted">Setiap laporan akan segera diverifikasi dan ditindaklanjuti.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mt-5">
                <div class="col-md-8" data-aos="fade-up">
                    <div class="alert alert-success shadow-sm p-4">
                        <h5 class="mb-2"><i class="bi bi-info-circle-fill me-2"></i>Bagaimana Cara Melapor?</h5>
                        <ol class="mb-0" style="text-align:left;">
                            <li>Klik tombol <b>Buat Aduan</b> di halaman utama.</li>
                            <li>Isi data diri dan detail laporan secara lengkap.</li>
                            <li>Ambil lokasi otomatis dan upload bukti foto jika ada.</li>
                            <li>Kirim laporan dan catat kode tracking Anda.</li>
                            <li>Pantau status laporan secara online.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer (optional, bisa pakai layout utama) -->
    <footer class="text-center py-4 mt-5" style="background:#28a745;color:#fff;">
        <div class="container">
            Hak Cipta &copy; <?= date('Y') ?> Dinas Lingkungan Hidup Kota Pekalongan
        </div>
    </footer>

    <!-- JS: Bootstrap, AOS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
      AOS.init({
        duration: 900,
        once: true
      });
    </script>
</body>
</html>