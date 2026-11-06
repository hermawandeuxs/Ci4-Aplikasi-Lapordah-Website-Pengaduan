<?= $this->extend('universal/layout') ?>
<?= $this->section('content') ?>

<style>
body {
    background: url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.rekap-card {
    background: rgba(255,255,255,0.25) !important;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    margin-top: 32px;
    padding: 48px 40px; /* lebih besar */
    transition: all 0.3s ease;
    max-width: 1100px !important; /* lebih lebar */
    width: 100%;
}
.rekap-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.rekap-title {
    font-weight: 700;
    color: #23803c;
    letter-spacing: 1px;
    font-size: 2.2rem; /* lebih besar */
}
.rekap-filter label {
    font-weight: 500;
    color: #23803c;
}
.rekap-filter .form-select:focus {
    border-color: #23803c;
    box-shadow: 0 0 0 0.2rem rgba(35, 128, 60, 0.25);
}
.table {
    background: rgba(255,255,255,0.98);
    border-radius: 15px;
    overflow: hidden;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
    font-size: 1.15rem; /* lebih besar */
}
.table thead th, .table-success {
    background: linear-gradient(145deg, #f8f9fc 0%, #edf2f7 100%) !important;
    color: #23803c;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(227,230,240,0.7);
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}
.table td, .table th {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
    padding: 1.25rem 1.5rem; /* lebih besar */
    border-bottom: 1px solid rgba(227,230,240,0.4);
    color: #222;
}
.table tbody tr {
    transition: all 0.2s ease;
}
.table tbody tr:hover {
    background: linear-gradient(145deg, #f7fafc 0%, #ebf4ff 100%);
    transform: scale(1.002);
}
@media (max-width: 768px) {
    .rekap-card { padding: 1rem; }
    .card-body { padding: 1rem; }
}
@media (max-width: 1200px) {
    .rekap-card { max-width: 98vw !important; padding: 1.5rem; }
}
@media print {
    .rekap-card { background: white !important; backdrop-filter: none; border: none; box-shadow: none; }
    .btn, .rekap-filter, .rekap-subtitle { display: none !important; }
}
</style>

<div class="container py-4 h-100 d-flex flex-column align-items-center">
    <div class="rekap-card mx-auto" style="max-width:800px;">
        <h2 class="rekap-title mb-2 text-center">
            <i class="bi bi-clipboard-data"></i> Rekap Laporan
        </h2>

        <?php if (!empty($bulanDipilih) || !empty($tahunDipilih)): ?>
        <p class="text-center text-muted rekap-subtitle mb-4">
            Menampilkan data untuk:
            <strong>
                <?= $bulanDipilih ? $bulanList[$bulanDipilih] : 'Semua Bulan' ?>
                <?= $tahunDipilih ? $tahunDipilih : 'Semua Tahun' ?>
            </strong>
        </p>
        <?php endif; ?>

        <form method="get" class="rekap-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        <?php
                        $bulanList = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        $bulanDipilih = (!empty($bulan) && is_numeric($bulan)) ? (int)$bulan : null;
                        foreach ($bulanList as $num => $nama):
                        ?>
                        <option value="<?= $num ?>" <?= $bulanDipilih === $num ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="tahun" class="form-label">Tahun</label>
<?php
$tahunSekarang = date('Y');
$tahunMulai = $tahunMulai ?? 2022;
$tahunDipilih = (isset($tahun) && is_numeric($tahun)) ? (int)$tahun : (int)$tahunSekarang;
?>
<select name="tahun" id="tahun" class="form-select">
    <?php for ($i = $tahunSekarang; $i >= $tahunMulai; $i--): ?>
        <option value="<?= $i ?>" <?= $tahunDipilih === $i ? 'selected' : '' ?>><?= $i ?></option>
    <?php endfor; ?>
    <option value="" <?= !isset($tahun) || $tahun === '' ? 'selected' : '' ?>>Semua Tahun</option>
</select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-success" aria-label="Filter data laporan">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </div>
        </form>
<?php
$isAdmin = session('role') === 'admin'; // atau sesuaikan dengan sistem Anda
?>
        <?php if (isset($rekap) && is_array($rekap)): ?>
            <?php if (!empty($rekap) && $isAdmin): ?>
            <div class="d-flex justify-content-end mb-3">
                <a href="<?= base_url("admin/rekap/export?bulan={$bulanDipilih}&tahun={$tahunDipilih}") ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-printer"></i> Cetak PDF
                </a>
            </div>
            

            <div class="mt-2 text-end">
                <b>Total Semua Laporan: <?= array_sum($rekap) ?></b>
            </div>
            <?php endif; ?>

            <?php if ($bulanDipilih && $tahunDipilih): ?>
                <?php if ($jumlahBulanLalu == 0): ?>
                    <div class="alert alert-info mt-2 text-center">
                        Tidak ada data bulan sebelumnya.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mt-2 text-center">
                        <b>Peningkatan dibanding bulan lalu:</b> <?= number_format($persen, 2) ?>%
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mt-3">
                    <thead class="table-success">
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>Kategori</th>
                            <th class="text-center">Jumlah Laporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($rekap as $kategori => $jumlah): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= esc($kategori) ?></td>
                            <td class="text-center"><?= esc($jumlah) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($rekap)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada data rekap untuk filter ini.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div> <!-- penutup .table-responsive -->

        <?php else: ?>
            <div class="alert alert-info mt-4 text-center">
                Silakan pilih filter bulan dan tahun untuk melihat rekap laporan.
            </div>
        <?php endif; ?>

        <div class="text-end mb-2">
            <small>Update: <?= date('d-m-Y H:i') ?></small>
        </div>

        <?php if (!empty($chartData['labels'])): ?>
            <div class="mt-5 mb-3">
                <h5 class="text-center mb-3" style="color:#23803c;font-weight:600;">Grafik Tren Jumlah Laporan per Bulan</h5>
                <div style="background:rgba(255,255,255,0.7);border-radius:12px;padding:24px;">
                    <canvas id="chartRekap" height="80"></canvas>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            new Chart(document.getElementById('chartRekap'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chartData['labels']) ?>,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: <?= json_encode($chartData['data']) ?>,
                        backgroundColor: '#4e73df'
                    }]
                }
            });
            </script>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
