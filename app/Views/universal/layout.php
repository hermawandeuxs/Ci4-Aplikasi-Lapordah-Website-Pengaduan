<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? (($userRole ?? 'admin') === 'admin' ? 'Admin Dashboard' : 'Admin Bidang Dashboard') ?> - LaporDah</title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
            --content-min-height: 600px; /* atau sesuai kebutuhan */
        }
        body { background-color: #f8f9fc; }
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand { height: var(--topbar-height); display: flex; align-items: center; padding: 1rem; background: rgba(0,0,0,0.1); }
        .sidebar-brand-text { color: white; font-size: 1.2rem; font-weight: 800; }
        .nav-item { position: relative; margin-bottom: 0.25rem; }
        .sidebar .nav-link {
            color: #fff !important;
            background: none !important;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            font-weight: 800;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-height: 48px; /* Konsistensi tinggi */
            line-height: 1.2;
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link:focus {
            color: #ffd700 !important;
            background: rgba(255, 215, 0, 0.08) !important;
        }
        .sidebar .nav-link.active {
            color: #222 !important;
            background: linear-gradient(90deg, #ffd700 60%, #fffbe7 100%) !important;
            box-shadow: 0 2px 8px rgba(255,215,0,0.15);
            font-weight: 800;
        }
        .sidebar .nav-link i {
            font-size: 1.2em;
            min-width: 1.5em;
        }
        .sidebar .nav-link span {
    white-space: normal;
    overflow: visible;
    text-overflow: unset;
    word-break: break-word;
    flex: 1 1 auto;
}
        .sidebar .nav-link .ms-auto {
    margin-left: auto !important;
}
        .sidebar .collapse .nav-link {
    font-weight: 600;
    font-size: 0.98em;
    padding-left: 2.5rem;
}
        .nav-item.mt-auto { margin-top: auto !important; }

        .content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            margin-top: var(--topbar-height);
            min-height: calc(100vh - var(--topbar-height));
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .topbar {
    height: var(--topbar-height);
    background:rgb(0, 132, 255);
    border-bottom: 1px solidrgb(44, 150, 255);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    padding: 0 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: fixed;
    top: 0;
    left: var(--sidebar-width);
    right: 0;
    z-index: 999;
}
.topbar-greeting {
    font-size: 1.2rem;
    font-weight: 800;
    color:rgb(255, 255, 255); /* warna netral gelap */
    text-transform: uppercase;
    letter-spacing: 0.5px;
}


        .card { border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15); }
        .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
        .border-left-primary { border-left: 0.25rem solid var(--primary-color) !important; }
        .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
        .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
        @media (max-width: 991.98px) {
    .sidebar {
        left: -250px;
        transition: left 0.3s;
    }
    .sidebar.show {
        left: 0;
    }
    .content, .topbar {
        margin-left: 0 !important;
        left: 0 !important;
        position: static !important;
        width: 100% !important;
    }
    #sidebarToggle { display: block !important; }
}

/* Hapus/override media query agar tidak responsif */
@media (max-width: 1200px), (max-width: 991.98px), (max-width: 768px), (max-width: 767.98px) {
    .sidebar, .content, .topbar {
        margin-left: var(--sidebar-width) !important;
        left: var(--sidebar-width) !important;
                width: auto !important;
                position: fixed !important;
                transform: none !important;
            }
            .sidebar.show { transform: none !important; }
        }
        /* Optional: Nonaktifkan tombol sidebar mobile */
        #sidebarToggle { display: none !important; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <span class="sidebar-brand-text">
                <?= ($userRole ?? 'admin') === 'admin' ? 'LAPORDAH ADMIN' : 'LAPORDAH ADMIN BIDANG' ?>
            </span>
        </div>
        <div class="nav flex-column">
            <!-- Dashboard -->
            <div class="nav-item">
                <a class="nav-link" href="<?= base_url(($userRole ?? 'admin') === 'admin' ? 'admin' : 'admin-bidang') ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <!-- Laporan -->
            <div class="nav-item">
                <a class="nav-link" href="<?= base_url(($userRole ?? 'admin') === 'admin' ? 'admin/reports' : 'admin-bidang/laporan') ?>">
                    <i class="bi bi-file-text"></i>
                    <span>Laporan</span>
                    <?php if (!empty($countReportsToday)): ?>
                        <span class="badge bg-danger ms-2" style="font-size:0.85em;vertical-align:middle;"><?= $countReportsToday ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <!-- Laporan Rejected -->
            <div class="nav-item">
                <a class="nav-link" href="<?= base_url(($userRole ?? 'admin') === 'admin' ? 'admin/laporan-reject' : 'admin-bidang/laporan-reject') ?>">
                    <i class="bi bi-file-earmark-x"></i>
                    <span>Laporan Rejected</span>
                    <?php if (!empty($countRejectedToday)): ?>
                        <span class="badge bg-danger ms-2" style="font-size:0.85em;vertical-align:middle;"><?= $countRejectedToday ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <!-- Arsip Laporan -->
            <div class="nav-item">
                <a class="nav-link" href="<?= base_url(($userRole ?? 'admin') === 'admin' ? 'admin/report-arsip' : 'admin-bidang/report-arsip') ?>">
                    <i class="bi bi-archive"></i>
                    <span>Arsip Laporan</span>
                    <?php if (!empty($countArsipToday)): ?>
                        <span class="badge bg-danger ms-2" style="font-size:0.85em;vertical-align:middle;"><?= $countArsipToday ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <?php if (($userRole ?? 'admin') === 'admin'): ?>
            <!-- Manajemen User (Dropdown) -->
            <?php
                $isUserManajemenActive =
                    url_is('admin/manajemen-user') ||
                    url_is('admin/manajemen-user/tambah') ||
                    url_is('admin/manajemen-user/edit/*');
            ?>
            <div class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center<?= $isUserManajemenActive ? ' active' : '' ?>"
                   href="#"
                   data-bs-toggle="collapse"
                   data-bs-target="#userManajemenSubmenu"
                   aria-expanded="<?= $isUserManajemenActive ? 'true' : 'false' ?>"
                   aria-controls="userManajemenSubmenu">
                    <span>
                        <i class="bi bi-person-gear me-2"></i>
                        Manajemen User
                    </span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse<?= $isUserManajemenActive ? ' show' : '' ?>" id="userManajemenSubmenu">
                    <ul class="nav flex-column ms-4">
                        <li class="nav-item">
                            <a class="nav-link<?= url_is('admin/manajemen-user') ? ' active' : '' ?>" href="<?= base_url('admin/manajemen-user') ?>">
                                <i class="bi bi-people me-2"></i> <span>Daftar Admin & User</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?= url_is('admin/manajemen-user/tambah') ? ' active' : '' ?>" href="<?= base_url('admin/manajemen-user/tambah') ?>">
                                <i class="bi bi-person-plus me-2"></i> <span>Tambah User</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Histori Status Laporan -->
            <div class="nav-item">
                <a class="nav-link<?= url_is('admin/history-laporan') ? ' active' : '' ?>" href="<?= base_url('admin/history-laporan') ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Histori Status Laporan</span>
                </a>
            </div>
            <?php endif; ?>
            <!-- Rekap Laporan -->
            <div class="nav-item">
                <a class="nav-link" href="<?= base_url(($userRole ?? 'admin') === 'admin' ? 'admin/rekap-laporan' : 'admin-bidang/rekap-laporan') ?>">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Rekap Laporan</span>
                </a>
            </div>
            <!-- Logout -->
            <div class="nav-item mt-auto">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </nav>
    <!-- Topbar -->
    <nav class="topbar">
        <button class="btn btn-link d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="d-flex align-items-center">
            <span class="topbar-greeting">SUGENG RAWUH, <?= session()->get('nama_user') ?></span>
                <?php
                    $role = session()->get('role');
                    $kebidangan = session()->get('kebidangan');
                    if ($role === 'admin_bidang' && !empty($kebidangan)):
                ?>
                <?php endif; ?>
            </span>
        </div>
    </nav>
    <!-- Main Content -->
    <main class="content">
        <div class="container-fluid py-4">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #ff9800;"></i>
                    </div>
                    <h5 class="modal-title mb-2" id="logoutModalLabel">Logout?</h5>
                    <p class="text-muted mb-4">Apakah Anda ingin Keluar?</p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-center">
                        <a href="<?= base_url('logout') ?>" class="btn btn-danger px-4">
                            <i class="bi bi-box-arrow-right me-2"></i>Ya, Keluar
                        </a>
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Sidebar on Mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
    <script>
function getBackPath() {
    const urlParams = new URLSearchParams(window.location.search);
    const back = urlParams.get('back');
    if (back) {
        try {
            // Ambil hanya path dari back (tanpa domain)
            return new URL(back, window.location.origin).pathname;
        } catch (e) {
            // Jika back hanya path relatif
            if (back.startsWith('/')) return back;
        }
    }
    return null;
}

function highlightSidebarNav() {
    $(".sidebar .nav-link").removeClass("active");
    var backPath = getBackPath();
    var currentUrl = backPath || window.location.pathname;

    // Mapping: keyword pada URL => teks menu sidebar
   var menuMap = [
    { keyword: ['/admin$', '/admin/$'], text: 'Dashboard' },
    { keyword: ['/admin-bidang$', '/admin-bidang/$'], text: 'Dashboard' },
    
    // Letakkan yang spesifik dulu
    { keyword: ['/admin-bidang/laporan-reject$'], text: 'Laporan Rejected' },
    { keyword: ['/admin/laporan-reject$'], text: 'Laporan Rejected' },

    { keyword: ['/admin-bidang/report-arsip$'], text: 'Arsip Laporan' },
    { keyword: ['/admin/report-arsip$'], text: 'Arsip Laporan' },

    { keyword: ['/admin-bidang/rekap-laporan$'], text: 'Rekap Laporan' },
    { keyword: ['/admin/rekap-laporan$'], text: 'Rekap Laporan' },

    { keyword: ['/admin-bidang/laporan$'], text: 'Laporan' },
    { keyword: ['/admin/reports$', '/admin/report/$'], text: 'Laporan' },

    { keyword: ['/admin/history-laporan'], text: 'Histori Status Laporan' },
    { keyword: ['/admin/manajemen-user/tambah'], text: 'Tambah User' },
    { keyword: ['/admin/manajemen-user$'], text: 'Daftar Admin & User' },
    { keyword: ['/admin/manajemen-user'], text: 'Manajemen User' },
    { keyword: ['/logout'], text: 'Logout' }
];


    // Temukan menu yang cocok
    $(".sidebar .nav-link").each(function() {
        var linkText = $(this).text().trim().toLowerCase();
        for (var i = 0; i < menuMap.length; i++) {
            var map = menuMap[i];
            for (var j = 0; j < map.keyword.length; j++) {
                var pattern = map.keyword[j];
                var regex = new RegExp(pattern, 'i');
                if (regex.test(currentUrl) && linkText.includes(map.text.toLowerCase())) {
                    $(this).addClass("active");
                    // Jika submenu, buka parent collapse
                    var collapse = $(this).closest(".collapse");
                    if (collapse.length) {
                        collapse.addClass("show");
                        collapse.prev(".nav-link").addClass("active");
                    }
                    return false; // stop loop jika sudah match
                }
            }
        }
    });
}

// Panggil dengan delay agar pasti jalan setelah navigasi back/forward
function highlightSidebarNavWithDelay() {
    setTimeout(highlightSidebarNav, 10);
}

$(function() {
    highlightSidebarNav();
});
window.addEventListener('pageshow', highlightSidebarNavWithDelay);
window.addEventListener('popstate', highlightSidebarNavWithDelay);
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        highlightSidebarNavWithDelay();
    }
});
    </script>
</body>
</html>
