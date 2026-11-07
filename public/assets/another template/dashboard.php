<?= $this->extend('admin/layouts') ?>

<?= $this->section('content') ?>
<style>
    body {
        background: url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') no-repeat center center fixed;
        background-size: cover;
    }
    .container-fluid {
        background: rgba(255, 255, 255, 0.18);
        border-radius: 16px;
        padding: 2rem;
        margin-top: 2rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    }
    #clock-container {
        background: #111;
        border-radius: 10px;
        padding: 0.7rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.18);
        display: inline-block;
        min-width: 270px;
    }
    .digital-date, .digital-clock {
        font-family: 'Courier New', Courier, monospace;
        color:rgb(255, 255, 255);
        letter-spacing: 2px;
        text-shadow:
            0 0 6px #ff2a2a,
            0 0 12px #ff2a2a,
            0 0 20px #ff2a2a;
        font-weight: bold;
        background: transparent;
        margin: 0;
        padding: 0;
        user-select: none;
    }
    .digital-date {
        font-size: 1.2rem;
    }
    .digital-clock {
        font-size: 2rem;
        letter-spacing: 4px;
    }
</style>
<div class="container-fluid">
    <!-- Header: Judul dan Jam -->
    <div class="d-flex align-items-center mb-4 justify-content-between">
        <div>
            <i class="bi bi-speedometer2 fs-2 text-white me-2"></i>
            <h2 class="mb-0 fw-bold text-white d-inline">Dashboard Admin</h2>
            <div><i>Selamat datang, Admin</i></div>
        </div>
        
        <!-- Jam Digital dengan Hari & Tanggal -->
        <div id="clock-container" class="text-center">
            <div id="digital-date" class="digital-date mb-1"></div>
            <div id="digital-clock" class="digital-clock"></div>
        </div>
    </div>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards: Satu Baris Banyak Kolom -->
    <div class="row mb-4">
        <!-- Total Reports -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clipboard-data fs-2 text-primary"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Laporan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['pending']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Verified -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-patch-check fs-2 text-success"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Verified</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['verified']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Disposisi -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-share fs-2 text-danger"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Disposisi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['disposition']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Anonim -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-incognito fs-2 text-info"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Anonim</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['anonymous']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Rejected -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-x-octagon fs-2 text-danger"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejected</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['rejected']) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Bulanan & Kategori Terpopuler (Stacked on Mobile, Side by Side on Desktop) -->
    <div class="row">
        <!-- Statistik 6 Bulan Terakhir (Bar Chart) -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik 6 Bulan Terakhir</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end h-100">
                    <div class="chart-area mt-auto">
                        <canvas id="monthlyReportsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Popular Categories Chart -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kategori Terpopuler</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-2 pb-2">
                        <canvas id="popularCategoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Statistik 6 Bulan Terakhir (Bar Chart)
    new Chart(document.getElementById('monthlyReportsChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($charts['monthly']['labels']) ?>,
            datasets: [{
                label: 'Jumlah Laporan',
                data: <?= json_encode($charts['monthly']['data']) ?>,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Popular Categories Chart (tetap)
    new Chart(document.getElementById('popularCategoriesChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($charts['categories']['labels']) ?>,
            datasets: [{
                label: 'Kategori Terpopuler',
                data: <?= json_encode($charts['categories']['data']) ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#5a9bd1', '#17a673', '#2c9faf', '#f7d54f', '#e67e22'],
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

// Jam Digital
function updateClock() {
    const now = new Date();
    const pad = n => n.toString().padStart(2, '0');
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const day = days[now.getDay()];
    const date = pad(now.getDate()) + '.' + pad(now.getMonth()+1) + '.' + now.getFullYear();
    const time = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    document.getElementById('digital-date').textContent = day + ', ' + date;
    document.getElementById('digital-clock').textContent = time;
}
setInterval(updateClock, 1000);
updateClock();
</script>
<?= $this->endSection() ?>
