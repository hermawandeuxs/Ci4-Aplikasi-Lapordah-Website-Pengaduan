<?= $this->extend('universal/layout') ?>

<?= $this->section('content') ?>
<style>
    body {
        background: url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') no-repeat center center fixed;
        background-size: cover;
    }
    .card, .accordion, .accordion-item, .accordion-body, .table {
        background: rgba(255,255,255,0.95) !important;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        backdrop-filter: blur(8px);
    }
    .accordion-button {
        border-radius: 20px !important;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .filter-bar {
        background: rgba(255,255,255,0.85);
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 16px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .filter-bar label {
        margin-bottom: 0;
        font-weight: 500;
    }
</style>
<div class="container py-4">
    <h3 class="mb-4 text-white"><i class="bi bi-clock-history"></i> Histori Perubahan Status Laporan</h3>
    <!-- Filter Perbidang -->
    <form method="get" class="filter-bar mb-3">
        <label for="bidang">Bidang:</label>
        <select name="bidang" id="bidang" class="form-select" style="width:auto;min-width:180px;display:inline-block;">
            <option value="">Semua Bidang</option>
            <?php foreach (($kebidangan ?? []) as $bidang): ?>
                <option value="<?= esc($bidang) ?>" <?= ($selectedBidang ?? '') === $bidang ? 'selected' : '' ?>>
                    <?= esc($bidang) ?>
                </option>
            <?php endforeach ?>
        </select>
        <label for="bulan" class="ms-2">Bulan:</label>
        <select name="bulan" id="bulan" class="form-select" style="width:auto;min-width:120px;display:inline-block;">
            <option value="">Semua Bulan</option>
            <?php
            $bulanList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $bulanDipilih = isset($selectedBulan) ? (int)$selectedBulan : (isset($_GET['bulan']) ? (int)$_GET['bulan'] : 0);
            foreach ($bulanList as $num => $nama):
            ?>
            <option value="<?= $num ?>" <?= $bulanDipilih === $num ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
        </select>
        <label for="tahun" class="ms-2">Tahun:</label>
        <select name="tahun" id="tahun" class="form-select" style="width:auto;min-width:100px;display:inline-block;">
            <option value="">Semua Tahun</option>
            <?php
            $tahunSekarang = date('Y');
            $tahunMulai = $tahunMulai ?? 2022;
            $tahunDipilih = (int)($selectedTahun ?? $_GET['tahun'] ?? $tahunSekarang);
            for ($t = $tahunSekarang; $t >= $tahunMulai; $t--):
            ?>
            <option value="<?= $t ?>" <?= $tahunDipilih === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm ms-2"><i class="bi bi-funnel"></i> Filter</button>
    </form>
    <?php if (empty($logsPerDay)): ?>
        <div class="alert alert-info">Belum ada histori perubahan status laporan<?= ($selectedBidang ?? '') ? ' untuk bidang ini.' : '.' ?></div>
    <?php else: ?>
        <div class="accordion" id="historyAccordion">
            <?php $idx = 0; foreach ($logsPerDay as $date => $logs): ?>
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading<?= $idx ?>">
                        <button class="accordion-button<?= $idx > 0 ? ' collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $idx ?>">
                            <?= date('l, d M Y', strtotime($date)) ?> (<?= count($logs) ?> perubahan)
                        </button>
                    </h2>
                    <div id="collapse<?= $idx ?>" class="accordion-collapse collapse<?= $idx === 0 ? ' show' : '' ?>" aria-labelledby="heading<?= $idx ?>" data-bs-parent="#historyAccordion">
                        <div class="accordion-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Kode Tracking</th>
                                            <th>Bidang</th>
                                            <th>Sumber</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Diupdate Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td><?= date('H:i:s', strtotime($log['updated_at'])) ?></td>
                                                <td><?= esc($log['tracking_code']) ?></td>
                                                <td>
                                                    <?php
                                                        if ($log['status'] === 'disposition' && $log['updated_by'] === 'admin-super') {
                                                            echo 'Super Admin';
                                                        } else {
                                                            echo esc($log['kebidangan'] ?? '-');
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if ($log['report_source'] === 'laporan-umum'): ?>
                                                        <span class="badge bg-info text-dark px-3 py-2">umum</span>
                                                    <?php elseif ($log['report_source'] === 'laporan-user'): ?>
                                                        <span class="badge bg-primary px-3 py-2">user</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($log['status']) ?></td>
                                                <td><?= esc($log['note']) ?></td>
                                                <td><?= esc($log['updated_by']) ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php $idx++; endforeach ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>