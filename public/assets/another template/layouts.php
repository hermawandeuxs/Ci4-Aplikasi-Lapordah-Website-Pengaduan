<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - LaporDah</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
        }

        body {
            background-color: #f8f9fc;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 1rem;
            background: rgba(0,0,0,0.1);
        }

        .sidebar-brand-text {
            color: white;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .nav-item {
            position: relative;
            margin-bottom: 0.25rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 1rem;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        /* Main Content */
        .content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            margin-top: var(--topbar-height);
        }

        /* Topbar */
        .topbar {
            height: var(--topbar-height);
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 999;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        /* Stats Cards */
        .border-left-primary { border-left: 0.25rem solid var(--primary-color) !important; }
        .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
        .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }

        /* Sidebar Dropdown */
        .sidebar .dropdown-menu {
            position: static;
            float: none;
            box-shadow: none;
            background: transparent;
            border: none;
            padding-left: 2rem;
        }
        .sidebar .dropdown-item {
            color: #fff !important;
            background: none !important;
        }
        .sidebar .dropdown-item:hover {
            color: #ffd700 !important;
        }

        /* Collapsible Menu */
        .sidebar .collapse .nav-link {
            color: #fff;
            padding-left: 2.5rem;
            background: none;
        }
        .sidebar .collapse .nav-link:hover {
            color: #ffd700;
            background: rgba(255,255,255,0.08);
        }
.sidebar .nav-link {
    color: #fff !important;
    background: none !important;
    border-radius: 8px;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    font-weight: 500;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.sidebar .nav-link:hover,
.sidebar .nav-link:focus {
    color: #ffd700 !important;
    background: rgba(255, 215, 0, 0.08) !important;
}
.sidebar .nav-link.active {
    color: #222 !important;
    background: linear-gradient(90deg, #ffd700 60%, #fffbe7 100%) !important;
    box-shadow: 0 2px 8px rgba(255,215,0,0.15);
    font-weight: 600;
}
.sidebar .nav-link i {
    font-size: 1.2em;
    min-width: 1.5em;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .content, .topbar {
        margin-left: 0;
        left: 0;
    }

            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <span class="sidebar-brand-text">LAPORDAH ADMIN</span>
        </div>
        <div class="nav flex-column">
            <!-- Menu Utama -->
            <div class="nav-item">
                <a class="nav-link" href="<?= base_url('admin') ?>">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="<?= base_url('admin/reports') ?>">
                    <i class="bi bi-file-text"></i>
                    Laporan
                </a>
            </div>
          <div class="nav-item">
                <a class="nav-link" href="<?= base_url('admin/laporan-reject') ?>">
                    <i class="bi bi-file-earmark-x"></i>
                    Laporan Rejected
                </a>
            </div>
           <div class="nav-item">
                <a class="nav-link" href="<?= base_url('admin/report-arsip') ?>">
                    <i class="bi bi-archive"></i>
                    Arsip Laporan
                </a>
            </div>
           
            <!-- Dropdown Manajemen User -->
            <?php
                $isUserManajemenActive =
                    url_is('admin/manajemen-user') ||
                    url_is('admin/manajemen-user/tambah') ||
                    url_is('admin/manajemen-user/edit/*');
            ?>
            <div class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center"
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
                                <i class="bi bi-people me-2"></i> Daftar Admin & User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?= url_is('admin/manajemen-user/tambah') ? ' active' : '' ?>" href="<?= base_url('admin/manajemen-user/tambah') ?>">
                                <i class="bi bi-person-plus me-2"></i> Tambah User
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Logout -->
            <div class="nav-item mt-auto">
                <a class="nav-link" href="<?= base_url('logout') ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
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
            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                <?= session()->get('username') ?? 'Admin' ?>
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="content">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar on Mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Highlight active menu
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                    link.style.backgroundColor = 'rgba(255,255,255,0.1)';
                }
            });
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>