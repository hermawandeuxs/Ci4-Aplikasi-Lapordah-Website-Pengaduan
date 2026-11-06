<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Aduan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: #f7fafd;
        }
        
        .timeline {
            border-left: 3px solid #e0e0e0;
            margin-left: 18px;
            padding-left: 18px;
            position: relative;
        }
        .timeline li {
            position: relative;
            margin-bottom: 32px;
        }
        .timeline li:last-child {
            margin-bottom: 0;
        }
        .timeline li::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #0d6efd;
            z-index: 1;
        }
        .timeline .text-purple { color: #8e24aa; }
        .timeline .text-success { color: #43a047; }
        .timeline .text-danger { color: #e53935; }
        .timeline .text-warning { color: #fbc02d; }
        .timeline .text-info { color: #1e88e5; }
        .timeline .text-secondary { color: #757575; }
        .badge-status {
            border-radius: 20px;
            padding: 4px 16px;
            font-size: 1rem;
            margin-left: 4px;
        }
        .badge-finish { background: #e6f4ea; color: #43a047; border: 1px solid #43a047; }
        .badge-public { background: #e3f2fd; color: #1976d2; border: 1px solid #1976d2; }
        .category-badge {
            border: 2px solid #ff9800;
            color: #ff9800;
            background: #fff3e0;
            border-radius: 20px;
            padding: 4px 16px;
            font-size: 1rem;
            display: inline-block;
            margin-top: 8px;
        }
        .img-preview {
            width: 100%;
            height: auto;
            max-height: 260px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 10px;
            cursor: pointer;
            transition: box-shadow 0.2s;
            display: block;
        }
        .img-preview:hover {
            box-shadow: 0 0 0 2px #0d6efd;
        }
        /* Desktop: show thumbnails side-by-side */
        @media (min-width: 992px) {
            .img-preview {
                max-width: 220px;
                max-height: 160px;
                display: inline-block;
                width: auto;
            }
        }
        .card {
            border-radius: 16px;
        }
        .card-header {
            border-radius: 16px 16px 0 0 !important;
        }
        @media (max-width: 991.98px) {
            .navbar-brand { font-size: 1.1rem; }
        }
        @media (max-width: 767px) {
            .img-preview { max-width: 100%; margin-bottom: 10px; }
            .card { margin-bottom: 1rem; }
        }
    </style>
</head>
<body>
<?=$this->include('layouts/header') ?>
<div style="height: 70px"></div>
<div class="container my-4">
    <h4 class="mb-4 fw-bold text-primary">Detail Aduan</h4>
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body pb-3">
                    <!-- Judul Rincian Aduan, Tracking Code & Badge Status -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                        <div>
                            <span class="fw-bold" style="font-size:1.08em;">Rincian Aduan :</span>
                            <span class="fw-bold text-primary text-decoration-none ms-1" style="letter-spacing:1px;">
                                <?= esc($report['tracking_code']) ?>
                            </span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php
                            // Samakan mapping warna badge status dengan index.php
                            $status = strtolower($report['status'] ?? 'pending');
                            $statusMap = [
                                'pending'     => ['warning text-white',   'Pending',     'background:#fffbe7;color:#fbc02d;border:1.5px solid #fbc02d;'],
                                'verified'    => ['success text-white',   'Verified',    'background:#e6f4ea;color:#43a047;border:1.5px solid #43a047;'],
                                'progress'    => ['warning text-white',   'Progress',    'background:#fffbe7;color:#fbc02d;border:1.5px solid #fbc02d;'],
                                'completed'   => ['info text-white',      'Selesai',     'background:#e3f2fd;color:#1e88e5;border:1.5px solid #1e88e5;'],
                                'disposition' => ['purple text-white',    'Disposisi',   'background:#f8eafd;color:#8e24aa;border:1.5px solid #8e24aa;'],
                                // default
                                ''=> ['secondary', ucfirst($status), 'background:#f8f9fa;color:#757575;border:1.5px solid #757575;'],
                            ];
                            $badge = $statusMap[$status] ?? $statusMap[''];
                            ?>
                            <span class="badge rounded-pill" style="font-weight:500;padding:6px 22px;font-size:1em;<?= $badge[2] ?>">
                                <?= $badge[1] ?>
                            </span>
                        </div>
                    </div>
                    <!-- Foto Bukti -->
                    <div class="mb-3">
                        <?php if (!empty($report['evidences'])): ?>
                            <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($report['evidences'] as $img): ?>
                                <img src="<?= base_url('/uploads/reports/' . $img) ?>"
                                     class="img-preview mb-2"
                                     alt="Foto Laporan"
                                     data-bs-toggle="modal"
                                     data-bs-target="#imgModal"
                                     data-img="<?= base_url('/uploads/reports/' . $img) ?>">
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/no-image.png') ?>"
                                 class="img-preview mb-2"
                                 alt="Tidak ada foto">
                        <?php endif; ?>
                    </div>
                   <!-- Lokasi & Tanggal -->
                <div class="mb-2 text-muted d-flex justify-content-between align-items-center" style="font-size: 1.05rem;">
                    <span class="fw-semibold text-uppercase" style="color:#7b7b7b;">
                        <i class="bi bi-geo-alt-fill me-1"></i>
                        <?= esc($report['lokasi'] ?? $report['location'] ?? '-') ?>
                    </span>
                    <span class="ms-2 d-flex align-items-center fw-semibold" style="color:#8a8a8a;font-weight:600">
                        <i class="bi bi-calendar-event me-1"></i>
                    <span><?= strtoupper(strftime('%d %B %Y', strtotime($report['created_at']))) ?></span>
                    </span>
                </div>
                    <!-- Kronologi -->
                    <div class="mb-2" style="font-size:1.07em;">
                        <?= esc($report['chronology'] ?? '-') ?>
                    </div>
                    <!-- Kategori -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                        <?php if (empty($report['category'])): ?>
                            <span class="category-badge" style="border:2px solid #1976d2;color:#1976d2;background:#e3f2fd;font-weight:500;">
                                #LAINNYA
                            </span>
                        <?php else: ?>
                            <span class="category-badge" style="border:2px solid rgb(255, 152, 0);color:#ff9800;background:#fffbe7;font-weight:500;">
                                #<?= strtoupper(esc($report['category'])) ?>
                            </span>
                            <?php if (strtolower($report['category']) === 'other' && !empty($report['other_category'])): ?>
                                <span class="category-badge" style="border:2px solid #1976d2;color:#1976d2;background:#e3f2fd;font-weight:500;">
                                    #LAINNYA
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<div class="col-md-4">
    <!-- Tombol Kembali -->
    <a href="<?= base_url('reports') ?>" class="btn btn-outline-primary mb-3 rounded-pill px-4 fw-semibold shadow-sm">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Laporan
    </a>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
            PROSES
        </div>
        <div class="card-body pb-2" style="max-height:340px; overflow-y:auto;">
            <ul class="list-unstyled mb-0" style="border-left:2px solid #e0e0e0; margin-left:18px; padding-left:18px;">
                <?php
                $statusMap = [
                    'pending'     => ['#757575', 'hourglass-split', 'Pending'],
                    'disposition' => ['#8e24aa', 'send-check', 'Disposisi'],
                    'verified'    => ['#43a047', 'patch-check-fill', 'Verifikasi'],
                    'rejected'    => ['#e53935', 'x-circle-fill', 'Ditolak'],
                    'progress'    => ['#fbc02d', 'arrow-repeat', 'Progress'],
                    'completed'   => ['#1e88e5', 'flag-fill', 'Selesai'],
                ];
                foreach ($statusLogs as $log):
                    $color = $statusMap[$log['status']][0] ?? '#757575';
                    $icon  = $statusMap[$log['status']][1] ?? 'clock';
                    $label = $statusMap[$log['status']][2] ?? ucfirst($log['status']);
                ?>
                <li class="mb-4 position-relative" style="min-height:38px;">
                    <span style="position:absolute;left:-32px;top:2px;display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;border:2.5px solid <?= $color ?>;background:#fff;">
                        <i class="bi <?= $icon ?>" style="font-size:1.1em;color:<?= $color ?>"></i>
                    </span>
                    <div>
                        <span style="font-weight:600;color:<?= $color ?>;font-size:1.08em;"><?= $label ?></span><br>
                        <span class="text-muted small"><?= strftime('%A, %d %B %Y - %H:%M', strtotime($log['updated_at'])) ?> WIB</span>
                        <?php if (!empty($log['updated_by'])): ?>
                            <div class="fw-semibold" style="color:#222;font-size:0.98em;"><?= esc($log['updated_by']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($log['note'])): ?>
                            <div style="font-size:0.97em;"><?= esc($log['note']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($log['file'])): ?>
                            <div class="mt-2">
                                <a href="<?= base_url('uploads/status_log/' . $log['file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-image"></i> Lihat File
                                </a>
                                <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $log['file'])): ?>
                                    <br>
                                    <img src="<?= base_url('uploads/status_log/' . $log['file']) ?>" alt="Bukti Proses" style="max-width:120px;max-height:90px;border-radius:6px;margin-top:6px;">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
</div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-transparent border-0 shadow-none">
      <div class="modal-body p-0 text-center d-flex justify-content-center align-items-center" style="min-height:70vh;">
        <img id="modalImg" src="" alt="Preview" class="img-fluid rounded shadow-lg"
             style="max-width:90vw; max-height:80vh; object-fit:contain; background:#fff;">
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.img-preview').forEach(function(img) {
    img.addEventListener('click', function() {
        document.getElementById('modalImg').src = this.getAttribute('data-img');
    });
});
document.querySelectorAll('.report-image').forEach(img => {
        img.onerror = function() {
            if (this.src !== '<?= base_url('assets/img/no-image.png') ?>') {
                this.src = '<?= base_url('assets/img/no-image.png') ?>';
            }
        };
    });
</script>
</body>
</html>