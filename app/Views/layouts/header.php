<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaduan Masyarakat LaporDAH</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap, Icons, Poppins -->
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body, .navbar, .nav-link, .navbar-brand {
            font-family: 'Poppins', sans-serif !important;
        }
        .navbar {
            background-color: #28a745 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 0.7rem 0;
            border-bottom: none !important; /* pastikan tidak ada garis putih */
        }
        .navbar-brand {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 700;
            color: #fff !important;
            font-size: 1.5rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            height: 50px;
            margin-right: 10px;
        }
        .nav-link, .btn-nav {
            color: #fff !important;
            font-weight: 500;
            font-size: 1.1rem;
            padding: 8px 18px !important;
            border-radius: 4px;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-link.active, .btn-nav.active {
            background: rgba(255,255,255,0.13);
            color: #ffe066 !important;
        }
        .nav-link:hover, .btn-nav:hover {
            background: rgba(255,255,255,0.13);
            color: #fff !important;
        }
        .login-btn {
            background-color: #0d6efd !important;
            color: #fff !important;
            border-radius: 6px;
            margin-left: 10px;
            padding: 8px 18px !important;
            font-weight: 600;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background-color: #0b5ed7 !important;
        }
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: #28a745 !important;
            }
            .navbar-nav {
                gap: 10px;
                padding: 15px 0;
            }
            .btn-nav {
                margin: 5px 0;
            }
        }
        
        * {
            scroll-behavior: smooth;
            font-family: 'Poppins', sans-serif;
        }
        body {
            padding-top: 10px;
            background-color:rgb(255, 255, 255);
        }
        .hero-section {
            background: url('/assets/wallpaper/wallpaper-lapordah.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
            color: white;
            position: relative;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .hero-section,#home {
            padding-top:100px;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }
        .hero-section p {
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }
        .search-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            z-index: 2;
        }
        .search-box .input-group {
            border-radius: 8px;
            overflow: hidden;
        }
        .search-box .form-control {
            border: none;
            padding: 15px;
            font-size: 1.1rem;
        }
        .search-box .btn {
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            border: none;
            font-weight: 500;
        }

        .complaint-form {
            padding: 50px 0;
            background-color: #f8f9fa;
        }
        .complaint-form .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .complaint-form .card-body {
            padding: 40px;
        }
        .complaint-form .form-control,
        .complaint-form .form-select {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .complaint-form button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            padding: 12px;
            border-radius: 8px;
            border: none;
            transition: background-color 0.3s;
        }
        .complaint-form button:hover {
            background-color: #218838;
        }
       
        
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
            .complaint-form .card-body {
                padding: 20px;
            }
        }

        .image-preview-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .preview-box {
            width: 150px;
            height: 150px;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .preview-content {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .img-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-number {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }

        .preview-close {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220,53,69,0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .preview-close:hover {
            background: rgba(220,53,69,1);
        }

        .file-count {
            margin-left: 10px;
            font-size: 14px;
            color: #6c757d;
        }

        .navbar {
            background-color: #28a745 !important;
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
            background-color: rgba(255, 255, 255, 0.1);
        }

        .login-btn {
            background-color: #0d6efd;
            color: white !important;
        }

        .login-btn:hover {
            background-color: #0b5ed7;
        }

        .bi {
            font-size: 1.1rem;
        }

        @media (max-width: 991.98px) {
            .navbar-nav {
                gap: 10px;
                padding: 15px 0;
            }
            
            .btn-nav {
                margin: 5px 0;
            }
        }

        .card-text {
            transition: color 0.3s ease;
        }

        .card-text.highlight {
            color: #28a745;
        }
        .offcanvas {
    background-color: #28a745 !important;
    color: #fff;
}
.offcanvas .nav-link {
    color: #fff !important;
}
    </style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">
            <img src="<?= base_url('assets/icons/maskot.png') ?>" alt="Logo"> LAPORDAH
        </a>
        <!-- Burger button hanya di mobile -->
        <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainMenu" aria-controls="mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Offcanvas menu hanya di mobile -->
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mainMenu" aria-labelledby="mainMenuLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mainMenuLabel">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a id="aduan-nav" class="nav-link btn-nav<?= (current_url() == base_url('/') ? ' active' : '') ?>"
                           href="<?= base_url('/') ?>#complaint">
                            <i class="bi bi-pencil-square"></i> Buat Aduan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-nav<?= (strpos(current_url(), 'reports') !== false) ? ' active' : '' ?>" href="<?= base_url('reports') ?>">
                            <i class="bi bi-journal-text"></i> Jelajah Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-nav<?= (strpos(current_url(), 'about') !== false) ? ' active' : '' ?>" href="<?= base_url('about') ?>">
                            <i class="bi bi-info-circle"></i> Tentang Lapordah
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('login') ?>" class="nav-link btn-nav login-btn">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Desktop menu -->
        <div class="d-none d-lg-flex ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a id="aduan-nav-desktop" class="nav-link btn-nav<?= (current_url() == base_url('/') ? ' active' : '') ?>"
                       href="<?= base_url('/') ?>#complaint">
                        <i class="bi bi-pencil-square"></i> Buat Aduan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-nav<?= (strpos(current_url(), 'reports') !== false) ? ' active' : '' ?>" href="<?= base_url('reports') ?>">
                        <i class="bi bi-journal-text"></i> Jelajah Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-nav<?= (strpos(current_url(), 'about') !== false) ? ' active' : '' ?>" href="<?= base_url('about') ?>">
                        <i class="bi bi-info-circle"></i> Tentang Lapordah
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('login') ?>" class="nav-link btn-nav login-btn">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function setActiveMenu() {
        // Reset semua
        document.querySelectorAll('.btn-nav').forEach(el => el.classList.remove('active'));
        // Buat Aduan (home + #complaint)
        if (
            window.location.pathname === '<?= base_url('/') ?>'.replace(window.location.origin, '') ||
            window.location.hash === '#complaint'
        ) {
            var aduanNav = document.getElementById('aduan-nav');
            var aduanNavDesktop = document.getElementById('aduan-nav-desktop');
            if (aduanNav) aduanNav.classList.add('active');
            if (aduanNavDesktop) aduanNavDesktop.classList.add('active');
        }
        // Jelajah Laporan
        if (window.location.pathname.includes('/reports')) {
            document.querySelectorAll('a[href*="reports"]').forEach(el => el.classList.add('active'));
        }
        // Tentang Lapordah
        if (window.location.pathname.includes('/about')) {
            document.querySelectorAll('a[href*="about"]').forEach(el => el.classList.add('active'));
        }
        // Login
        if (window.location.pathname.includes('/login')) {
            document.querySelectorAll('a[href*="login"]').forEach(el => el.classList.add('active'));
        }
    }
    setActiveMenu();
    window.addEventListener('hashchange', setActiveMenu);
});
</script>