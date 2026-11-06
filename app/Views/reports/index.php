<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= esc($title) ?></title>
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
     
        /* --- END NAVBAR STYLE --- */
        /* Wallpaper background */
        body {
            min-height: 100vh;
            background: url('<?= base_url('assets/wallpaper/wallpaper.jpg') ?>') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
        .main-content {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.85);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            padding: 2.5rem 2rem 2rem 2rem;
            margin-top: 75px;
            margin-bottom: 40px;
        }
        .brand-logo {
            height: 32px;
            margin-right: 10px;
        }
        .brand-text {
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
        }
        .btn-nav {
            color: white !important;
            padding: 8px 16px !important;
            margin: 0 5px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-nav:hover {
            background-color: rgba(255, 255, 255, 0.13);
        }
        .login-btn {
            background-color: #0d6efd;
            color: white !important;
        }
        .login-btn:hover {
            background-color: #0b5ed7;
        }
        .main-title {
            font-weight: 700;
            font-size: 2.2rem;
            color: #198754;
            letter-spacing: 1px;
            margin-bottom: 0.2rem;
        }
        .main-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        .filter-card {
            border: none !important;
            box-shadow: none !important;
            background: rgba(255,255,255,0.92);
            border-radius: 0 !important;
}
        .filter-card .form-label {
            font-size: 0.95rem;
            color: #198754;
            margin-bottom: 0.25rem;
        }
        .filter-card .form-select,
        .filter-card .form-control {
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        .filter-card .form-select:focus,
        .filter-card .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.13);
        }
        .report-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: transform 0.18s, box-shadow 0.18s;
            background: rgba(255,255,255,0.97);
        }
        .report-card:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 6px 18px rgba(0,0,0,0.13);
        }
        .report-image-container {
            position: relative;
            overflow: hidden;
            height: 200px;
            border-radius: 14px 14px 0 0;
            background: #f8f9fa;
        }
        .report-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background-color: #f8f9fa;
        }
        .report-status {
            position: absolute;
            top: 12px;
            right: 12px;
        }
        .badge {
            padding: 8px 12px;
            font-weight: 500;
            font-size: 0.93rem;
        }
        .badge.bg-secondary {
            background-color: #6c757d;
            color: #fff;
            margin-left: 5px;
        }
        .pagination {
            justify-content: center;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .pagination .page-item {
            margin: 0 2px;
        }
        .pagination .page-link {
            border: none;
            border-radius: 0 !important;
            color: #212529;
            background: #fff;
            min-width: 44px;
            text-align: center;
            font-weight: 500;
            transition: color 0.2s;
        }
        .pagination .page-link:hover,
        .pagination .page-item.active .page-link {
            background: #e9ecef;
            color: #198754;
        }
        .pagination .page-item.active .page-link {
            background: #198754;
            color: #fff;
            border: none;
        }
        .pagination .page-link:focus {
            box-shadow: none;
        }
        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            background: #fff;
            border: none;
            pointer-events: none;
        }
        .alert {
            margin-top: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            padding: 1rem;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .main-content .row-cols-md-2 .col {
            margin-bottom: 1rem;
        }
        @media (max-width: 991.98px) {
            .main-content {
                padding: 1.2rem 0.5rem 1.5rem 0.5rem;
            }
            .navbar-collapse {
                background-color: #28a745;
            }
        }
        @media (max-width: 767.98px) {
            .main-content {
                margin-top: 65px;
                margin-bottom: 15px;
            }
            .report-image-container {
                height: 140px;
            }
        }
        .bg-purple {
            background-color: #7c3aed !important;
            color: #fff !important;
        }
        footer, .footer, #footer {
    background:rgba(40,167,69,0.98) !important;
    box-shadow: none !important;
    position: relative;
    z-index: 2;
}

.footer-bottom, .footer-copyright, .footer .copyright, .footer-bottom-bar {
    background:rgba(35,124,59,0.98) !important;
    color: #fff !important;
    border-top: none !important;

}
#searchForm .input-group {
    width: 100%;
}
    </style>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (session()->getFlashdata('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Tidak Ditemukan',
        text: '<?= esc(session('error')) ?>',
        icon: 'info',
        confirmButtonText: 'OK'
    }).then(() => {
        // Kosongkan input pencarian jika ada
        var input = document.getElementById('searchTrackingCode');
        if (input) input.value = '';
        var input2 = document.querySelector('input[name="q"]');
        if (input2) input2.value = '';
         // Redirect ke state awal (tanpa parameter q)
        window.location.href = '<?= base_url('reports') ?>';
    });
});
</script>
<?php elseif (!empty($q) && empty($reports)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Tidak Ditemukan',
        text: 'Laporan tidak ditemukan untuk pencarian: <?= esc($q) ?>',
        icon: 'info',
        confirmButtonText: 'OK'
    }).then(() => {
        // Kosongkan input pencarian jika ada
        var input = document.getElementById('searchTrackingCode');
        if (input) input.value = '';
        var input2 = document.querySelector('input[name="q"]');
        if (input2) input2.value = '';
         // Redirect ke state awal (tanpa parameter q)
        window.location.href = '<?= base_url('reports') ?>';
    });
});
</script>
<?php endif; ?>

<?= $this->include('layouts/header') ?>

<div class="container main-content shadow-lg">
    <!-- Judul & Subtitle -->
    <div class="mb-4">
        <h2 class="main-title"><?= esc($title ?? 'Semua Aduan') ?></h2>
        <p class="main-subtitle">
            Terdapat <?= $total ?> aduan
            <?php if(isset($_GET['status']) && $_GET['status']): ?>
                dengan status "<b><?= ucfirst(esc($_GET['status'])) ?></b>"
            <?php endif; ?>
            <?php if(isset($_GET['category']) && $_GET['category']): ?>
                dalam kategori "<b><?= esc($categories[$_GET['category']] ?? $_GET['category']) ?></b>"
            <?php endif; ?>
        </p>
    </div>

    <!-- Search Form (tanpa card, lebar penuh) -->
<form id="searchForm" method="get" class="mb-4">
    <div class="input-group input-group-lg">
        <input type="text"
               name="q"
               id="searchTrackingCode"
               class="form-control rounded-start-pill shadow-sm"
               placeholder="Cari laporan (kode, lokasi, kategori, kronologi)..."
               value="<?= esc($q ?? '') ?>">
        <button type="submit"
                class="btn btn-success rounded-end-pill px-4 d-flex align-items-center gap-2"
                style="white-space: nowrap;">
            <i class="bi bi-search"></i>
            <span>Cari</span>
        </button>
    </div>
</form>

    <!-- Filter Card (beri mb-4 agar ada gap ke daftar laporan) -->
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form action="<?= base_url('reports') ?>" method="get">
                <div class="row g-3">
                    <!-- Status Filter -->
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="disposition" <?= ($status ?? '') === 'disposition' ? 'selected' : '' ?>>Disposisi</option>
                            <option value="progress" <?= ($status ?? '') === 'progress' ? 'selected' : '' ?>>Proses</option>
                            <option value="verified" <?= ($status ?? '') === 'verified' ? 'selected' : '' ?>>Verifikasi</option>
                            <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <!-- Category Filter -->
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="all" <?= $category === 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                            <?php foreach($categories as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($category === $key) ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Sort Filter -->
                    <div class="col-md-3">
                        <label class="form-label">Urutan</label>
                        <select name="sort" class="form-select">
                            <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Terbaru</option>
                            <option value="oldest" <?= ($sort === 'oldest') ? 'selected' : '' ?>>Terlama</option>
                        </select>
                    </div>
                    <!-- Filter Button -->
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hasil Pencarian -->
    <?php if (!empty($q)): ?>
        <h5 class="mb-3">Hasil Pencarian: "<span class="text-primary"><?= esc($q) ?></span>"</h5>
    <?php endif; ?>

    <!-- Reports List -->
    <?php if (!empty($reports)): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($reports as $report): ?>
                <div class="col">
                    <a href="<?= base_url('reports/detail/' . $report['id']) ?>" class="text-decoration-none text-dark">
                        <div class="card report-card h-100">
                            <div class="report-image-container">
                                <img src="<?= get_report_image($report['evidence_1']) ?>"
                                     class="report-image"
                                     alt="Report Evidence"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='<?= base_url('assets/img/no-image.png') ?>';">
                                <div class="report-status">
                                    <?php
                                        $status = strtolower($report['status']);
                                        $statusClass = match($status) {
                                            'pending'    => 'warning text-dark',
                                            'verified'   => 'success',
                                            'rejected'   => 'danger',
                                            'progress'   => 'warning',
                                            'completed'  => 'info',
                                            'disposition'=> 'purple',
                                            default      => 'secondary'
                                        };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?> px-3 py-2">
                                        <?= ucfirst(esc($report['status'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($report['tracking_code']) ?></h5>
                                <p class="text-muted">
                                    <i class="bi bi-calendar3"></i>
                                    <?= date('d M Y', strtotime($report['created_at'])) ?>
                                    &bull;
                                    <i class="bi bi-geo-alt"></i>
                                    <?= esc($report['location']) ?>
                                </p>
                                <p class="card-text"><?= esc($report['chronology']) ?></p>
                                <span class="badge bg-info">
                                    <i class="bi bi-tag"></i>
                                    <?= esc($report['category'] ?: 'Lainnya') ?>
                                </span>
                                <?php if (strtolower($report['category']) === 'other_category' && !empty($report['other_category'])): ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-tag"></i> <?= esc($report['other_category']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
 if (!empty($q) && empty($reports)): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Tidak Ditemukan',
            text: 'Laporan tidak ditemukan untuk pencarian: <?= esc($q) ?>',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    });
    </script>
<?php endif; ?>

<?php endif; ?>
    <!-- Pagination -->
    <?php if (empty($tracking_code) && !isset($filter_active)): ?>
        <?php if (isset($pager_links) && $total > 0): ?>
            <?php
                $currentPage = (int)($currentPage ?? ($_GET['page_reports'] ?? 1));
                $perPage = 10; // Atur sesuai controller
                $totalPages = ceil($total / $perPage);

                // Build base url for pagination (tanpa page_reports)
                $baseUrl = current_url();
                $queryArr = $_GET;
                unset($queryArr['page_reports']);
                $baseQuery = http_build_query($queryArr);
                $baseUrlWithQuery = $baseUrl . ($baseQuery ? '?' . $baseQuery . '&' : '?');
            ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Menampilkan <?= count($reports) ?> dari <?= $total ?> laporan
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <!-- Tombol Sebelumnya -->
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $currentPage > 1 ? $baseUrlWithQuery . 'page_reports=' . ($currentPage - 1) : '#' ?>" aria-label="Previous">
                                <span aria-hidden="true">&lt;</span>
                            </a>
                        </li>
                        <!-- Nomor Halaman -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $baseUrlWithQuery . 'page_reports=' . $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <!-- Tombol Berikutnya -->
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $currentPage < $totalPages ? $baseUrlWithQuery . 'page_reports=' . ($currentPage + 1) : '#' ?>" aria-label="Next">
                                <span aria-hidden="true">&gt;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->include('layouts/footer') ?>

<!-- Loading Overlay -->
<div id="search-loading" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:rgba(255,255,255,0.7);align-items:center;justify-content:center;">
    <div class="spinner-border text-success" style="width:3rem;height:3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- JS scripts tetap di bawah -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu handling
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    document.addEventListener('click', (e) => {
        if (!navbarCollapse.contains(e.target) && !navbarToggler.contains(e.target)) {
            navbarCollapse.classList.remove('show');
        }
    });

    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', searchReport);
    }
});

const qInput = document.getElementById('searchTrackingCode');
    if (qInput) {
        qInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                // Cek jika ada parameter q di URL, baru redirect
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('q')) {
                    window.location.href = BASE_URL + 'reports';
                }
            }
        });
    }
    
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navbarCollapse.classList.remove('show');
        });
    });

    document.querySelectorAll('.report-image').forEach(img => {
        img.onerror = function() {
            if (this.src !== '<?= base_url('assets/img/no-image.png') ?>') {
                this.src = '<?= base_url('assets/img/no-image.png') ?>';
            }
        };
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#page-top') {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetElement.offsetTop - navbarHeight;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });

    const trackingInput = document.getElementById('searchTrackingCode');
    if (trackingInput) {
        trackingInput.addEventListener('paste', function (e) {
            setTimeout(() => {
                const pastedValue = this.value.trim();
                if (pastedValue) {
                    const trackingCodePattern = /^[A-Za-z0-9-]+$/;
                    if (!trackingCodePattern.test(pastedValue)) {
                        this.value = '';
                        alert('Format kode tracking yang di-paste tidak valid.');
                    }
                }
            }, 0);
        });
    }

    // Loading overlay for search
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            document.getElementById('search-loading').style.display = 'flex';
        });
    }
</script>
<script>
window.BASE_URL = window.BASE_URL || '<?= base_url() ?>';
</script>
<script>
window.addEventListener('load', function() {
    const loading = document.getElementById('search-loading');
    if (loading) loading.style.display = 'none';
});

function searchReport(e) {
    e.preventDefault();
    const qInput = document.getElementById('searchTrackingCode');
    const q = qInput.value.trim();

    // Validasi maksimal 2 kalimat
    const sentences = q.split(/[.!?]+/).filter(Boolean);
    if (sentences.length > 2) {
        Swal.fire({
            title: 'Error!',
            text: 'Pencarian maksimal 2 kalimat.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    if (!q) {
        Swal.fire({
            title: 'Error!',
            text: 'Mohon masukkan kata kunci pencarian.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Tampilkan loading
    document.getElementById('search-loading').style.display = 'flex';

    // AJAX cek laporan
    fetch(`${BASE_URL}reports/check?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('search-loading').style.display = 'none';
            if (data.found) {
                window.location.href = `${BASE_URL}reports?q=${encodeURIComponent(q)}`;
            } else {
                Swal.fire({
                    title: 'Tidak Ditemukan',
                    text: 'Laporan tidak ditemukan untuk pencarian: ' + q,
                    icon: 'info',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reset input setelah notifikasi
                    qInput.value = '';
                });
            }
        })
        .catch(() => {
            document.getElementById('search-loading').style.display = 'none';
            Swal.fire('Error', 'Terjadi kesalahan saat mencari laporan.', 'error');
        });
}
</script>
</body>
</html>