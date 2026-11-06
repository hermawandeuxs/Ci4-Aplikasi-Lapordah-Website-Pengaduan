<?= $this->extend('universal/layout') ?>
<?= $this->section('content') ?>
<style>
    body {
        background: url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') no-repeat center center fixed;
        background-size: cover;
    }
    .dashboard-wrapper {
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
    .simple-card {
        cursor: pointer;
        transition: box-shadow .2s;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .simple-card:hover {
        box-shadow: 0 4px 16px rgba(78,115,223,0.15);
        background: #f8f9fc;
    }
    .modal-backdrop {
        z-index: 1040 !important;
    }
    .modal-content {
        border-radius: 14px;
    }


.modal-dialog {
    max-width: 400px;
}
.modal-content {
    border-radius: 14px;
}
#statModal .modal-body {
    min-height: unset;
    max-height: 420px;
    overflow-y: auto;
    padding-bottom: 1.5rem;
}
#modal-pie {
    max-width: 220px;
    margin: 0 auto 1rem auto;
    display: block;
}
</style>
<div class="container-fluid py-4 h-100 d-flex flex-column">
    <div class="dashboard-wrapper w-100">
        <!-- Header: Judul dan Jam -->
        <div class="d-flex align-items-center mb-4 justify-content-between">
            <div>
                <i class="bi bi-speedometer2 fs-2 text-white me-2"></i>
                <h2 class="mb-0 fw-bold text-white d-inline"><?= esc($title) ?></h2>
                <div>
                    <i>
                        Selamat datang,
                        <?php
                            $role = $userRole ?? session()->get('role');
                            $kebidangan = $kebidangan ?? session()->get('kebidangan');
                            if ($role === 'admin_bidang') {
                                echo 'Admin Bidang' . ($kebidangan ? ' ' . esc($kebidangan) : '');
                            } else {
                                echo esc($userName ?? ($role === 'admin' ? 'Admin' : ''));
                            }
                        ?>
                    </i>
                    <?php
                        $filePanduan = '';
                        if ($role === 'admin') {
                            $filePanduan = 'panduan_admin.pdf';
                        } elseif ($role === 'admin_bidang') {
                            $filePanduan = 'panduan_admin_bidang.pdf';
                        }
                    ?>
                    <?php if ($filePanduan): ?>
                        <div class="mt-2">
                            <a href="<?= base_url('assets/' . $filePanduan) ?>" target="_blank"
                                class="btn btn-lg btn-light shadow fw-bold px-4 py-2" style="border-radius: 10px;">
                                <i class="bi bi-journal-text me-1"></i> Lihat Panduan Penggunaan
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
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
     


        <!-- ADMIN: Statistik Per Bidang -->
        <?php if ($role === 'admin' && !empty($bidangStats)): ?>
        <div class="row mb-4">
            <?php foreach ($bidangStats as $bidang => $data): ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="simple-card"
                    data-bidang="<?= esc($bidang) ?>"
                    data-disposisi="<?= (int)($data['disposition'] ?? 0) ?>"
                    data-verified="<?= (int)($data['verified'] ?? 0) ?>"
                    data-rejected="<?= (int)($data['rejected'] ?? 0) ?>"
                    data-archived="<?= (int)($data['archived'] ?? 0) ?>"
                    data-progress="<?= (int)($data['progress'] ?? 0) ?>"
                    data-done="<?= (int)($data['done'] ?? 0) ?>"
                    data-total="<?= (int)($data['total'] ?? 0) ?>"
                >
                    <div class="fs-4 fw-bold mb-2"><?= esc($bidang) ?></div>
                    <div class="fs-5 text-primary mb-1"><?= (int)($data['total'] ?? 0) ?> Laporan</div>
                    <div class="text-muted small">Klik untuk detail</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            function waitForChartAndEl(id, callback) {
                (function poll() {
                    var el = document.getElementById(id);
                    if (typeof Chart !== 'undefined' && el && el.getContext && el.getContext('2d')) {
                        return callback(el);
                    }
                    setTimeout(poll, 50);
                })();
            }
            <?php foreach ($bidangStats as $bidang => $data): ?>
            (function(){
                var canvasId = 'pie-bidang-<?= md5($bidang) ?>';
                waitForChartAndEl(canvasId, function(el){
                    var ctx = el.getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Disposisi', 'Terverifikasi', 'Ditolak', 'Arsip', 'Progress', 'Selesai'],
                            datasets: [{
                                data: [
                                    <?= (int)($data['disposition'] ?? 0) ?>,
                                    <?= (int)($data['verified'] ?? 0) ?>,
                                    <?= (int)($data['rejected'] ?? 0) ?>,
                                    <?= (int)($data['archived'] ?? 0) ?>,
                                    <?= (int)($data['progress'] ?? 0) ?>,
                                    <?= (int)($data['done'] ?? 0) ?>
                                ],
                                backgroundColor: [
                                    '#8e44ad',
                                    '#1cc88a',
                                    '#e74a3b',
                                    '#858796',
                                    '#f6c23e',
                                    '#4e73df'
                                ],
                                hoverBorderColor: '#fff'
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                });
            })();
            <?php endforeach; ?>
        });
        </script>
        <?php endif; ?>

        <!-- ADMIN_BIDANG: Statistik Khusus Bidangnya -->
        <?php if ($role === 'admin_bidang' && !empty($bidangStats) && !empty($kebidangan) && isset($bidangStats[$kebidangan])): ?>
        <div class="row mb-4">
            <div class="col-xl-6 col-md-8 mb-4 mx-auto">
                <div class="card border-left-primary shadow h-100 py-2 w-100 bidang-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3">
                                <i class="bi bi-diagram-3 fs-2 text-primary"></i>
                            </div>
                            <div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><?= esc($kebidangan) ?></div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($bidangStats[$kebidangan]['total'] ?? 0) ?> Laporan</div>
                            </div>
                        </div>
                        <div class="px-2 pb-2">
                            <canvas id="pie-bidang" height="120"></canvas>
                        </div>
                        <div class="small mt-2">
                            <span class="badge bg-info text-white">Total: <?= (int)($bidangStats[$kebidangan]['total'] ?? 0) ?></span>
                            <span class="badge" style="background:#8e44ad;color:#fff;">Disposisi: <?= (int)($bidangStats[$kebidangan]['disposition'] ?? 0) ?></span>
                            <span class="badge bg-success">Terverifikasi: <?= (int)($bidangStats[$kebidangan]['verified'] ?? 0) ?></span>
                            <span class="badge bg-danger">Ditolak: <?= (int)($bidangStats[$kebidangan]['rejected'] ?? 0) ?></span>
                            <span class="badge bg-secondary">Arsip: <?= (int)($bidangStats[$kebidangan]['archived'] ?? 0) ?></span>
                            <span class="badge bg-warning text-dark">Progress: <?= (int)($bidangStats[$kebidangan]['progress'] ?? 0) ?></span>
                            <span class="badge bg-primary">Selesai: <?= (int)($bidangStats[$kebidangan]['done'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            (function waitForChartAndEl(id, cb) {
                (function poll(){
                    var el = document.getElementById(id);
                    if (typeof Chart !== 'undefined' && el && el.getContext && el.getContext('2d')) return cb(el);
                    setTimeout(poll, 50);
                })();
            })('pie-bidang', function(el){
                var ctx = el.getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Disposisi','Terverifikasi', 'Ditolak', 'Arsip', 'Progress', 'Selesai'],
                        datasets: [{
                            data: [
                                <?= (int)($bidangStats[$kebidangan]['disposition'] ?? 0) ?>,
                                <?= (int)($bidangStats[$kebidangan]['verified'] ?? 0) ?>,
                                <?= (int)($bidangStats[$kebidangan]['rejected'] ?? 0) ?>,
                                <?= (int)($bidangStats[$kebidangan]['archived'] ?? 0) ?>,
                                <?= (int)($bidangStats[$kebidangan]['progress'] ?? 0) ?>,
                                <?= (int)($bidangStats[$kebidangan]['done'] ?? 0) ?>
                            ],
                            backgroundColor: ['#8e44ad','#1cc88a', '#e74a3b', '#858796', '#f6c23e', '#4e73df'],
                            hoverBackgroundColor: ['#17a673', '#e67e22', '#6c757d', '#f7d54f', '#5a9bd1'],
                            hoverBorderColor: '#fff'
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            });
        });
        </script>
        <?php endif; ?>

    
    </div>
</div>

<!-- Modal Statistik Bidang -->
<div class="modal fade" id="statModal" tabindex="-1" aria-labelledby="statModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statModalLabel">Statistik Bidang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-2">
            <span class="fw-bold" id="modal-bidang"></span>
            <div class="mb-2 text-primary" id="modal-total"></div>
        </div>
        <canvas id="modal-pie" height="120"></canvas>
        <div class="mt-3 text-center">
            <span class="badge bg-success mb-1" id="modal-verified"></span>
            <span class="badge" style="background:#8e44ad;color:#fff;" id="modal-disposisi"></span>
            <span class="badge bg-danger mb-1" id="modal-rejected"></span>
            <span class="badge bg-secondary mb-1" id="modal-archived"></span>
            <span class="badge" style="background:#f6c23e;color:#222;" id="modal-progress"></span>
            <span class="badge" style="background:#4e73df;color:#fff;" id="modal-done"></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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

$(function() {
  $(".sidebar .nav-link").removeClass("active");
  // Aktifkan menu Dashboard saja
  $(".sidebar .nav-link").filter(function(){
    return $(this).text().trim() === 'Dashboard';
  }).addClass("active");
});

let pieChart;

function renderModalPieChart(verified, disposisi, rejected, archived, progress, done) {
    if (typeof Chart === 'undefined') {
        return;
    }

    const canvas = document.getElementById('modal-pie');
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');
    if (pieChart) {
        pieChart.destroy();
    }

    pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [
                'Terverifikasi',
                'Disposisi',
                'Ditolak',
                'Arsip',
                'Progress',
                'Selesai'
            ],
            datasets: [{
                data: [verified, disposisi, rejected, archived, progress, done],
                backgroundColor: [
                    '#1cc88a',
                    '#8e44ad',
                    '#e74a3b',
                    '#858796',
                    '#f6c23e',
                    '#4e73df'
                ],
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
}

document.querySelectorAll('.simple-card').forEach(card => {
    card.addEventListener('click', function() {
        const bidang = this.dataset.bidang;
        const disposisi = parseInt(this.dataset.disposisi) || 0;
        const verified = parseInt(this.dataset.verified) || 0;
        const rejected = parseInt(this.dataset.rejected) || 0;
        const archived = parseInt(this.dataset.archived) || 0;
        const progress = parseInt(this.dataset.progress) || 0;
        const done = parseInt(this.dataset.done) || 0;
        const total = parseInt(this.dataset.total) || 0;

        document.getElementById('modal-bidang').textContent = bidang;
        document.getElementById('modal-total').textContent = total + ' Laporan';
        document.getElementById('modal-disposisi').textContent = 'Disposisi: ' + disposisi;
        document.getElementById('modal-verified').textContent = 'Terverifikasi: ' + verified;
        document.getElementById('modal-rejected').textContent = 'Ditolak: ' + rejected;
        document.getElementById('modal-archived').textContent = 'Arsip: ' + archived;
        document.getElementById('modal-progress').textContent = 'Progress: ' + progress;
        document.getElementById('modal-done').textContent = 'Selesai: ' + done;

        const modalEl = document.getElementById('statModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        modalEl.addEventListener('shown.bs.modal', function handler() {
            renderModalPieChart(verified, disposisi, rejected, archived, progress, done);
            if (pieChart) {
                pieChart.resize();
            }
            modalEl.removeEventListener('shown.bs.modal', handler);
        });
    });
});
</script>
<?= $this->endSection() ?>
