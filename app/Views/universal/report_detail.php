<?php
// Universal rejected reports view for admin & admin-bidang
// Expects: $report, $userRole, etc.
$reportId = ($report['id_pengaduan'] ?? ($report['id'] ?? ''));
?>
<?= $this->extend('universal/layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4 h-100 d-flex flex-column">
    <div class="card shadow-lg border-0 w-100 my-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i> Detail Laporan <span class="text-warning">#<?= esc($report['tracking_code']) ?></span>
            </h5>
        
        <a href="<?= esc($_GET['back'] ?? ($userRole === 'admin' ? base_url('admin/reports') : base_url('admin-bidang/laporan'))) ?>" class="btn btn-light btn-sm shadow-sm">
        <i class="fas fa-arrow-left"></i> Kembali
        </a>
        </div>
        <div class="card-body bg-light">
            <?php if (session()->has('notif')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= session('notif') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
            <div class="row g-4">
                <!-- Kiri: Detail Laporan -->
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded shadow-sm mb-3">
                        <table class="table table-striped table-bordered mb-0 shadow-sm detail-table w-100">
                            <tbody>
                                <tr>
                                    <th class="col-4 fw-bold text-primary align-middle"><i class="fas fa-hashtag me-2"></i>ID Complaint</th>
                                    <td class="col-8 align-middle"><?= esc($report['tracking_code']) ?></td>
                                </tr>
                                <tr>
                                    <th class="fw-bold text-primary align-middle"><i class="fas fa-calendar-alt me-2"></i>Tanggal Laporan</th>
                                    <td class="align-middle"><?= date('d M Y H:i', strtotime($report['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th class="fw-bold text-primary align-middle"><i class="fas fa-user me-2"></i>Nama Pelapor</th>
                                    <td class="align-middle">
                                        <?php if (!empty($report['nama_pelapor'])): ?>
                                            <span class="badge bg-info text-dark px-3 py-2"><?= esc($report['nama_pelapor']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                            <th class="fw-bold text-primary align-middle"><i class="fas fa-user me-2"></i>Alamat Pelapor</th>
                            <td class="align-middle"><?= esc($report['alamat_pelapor'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th class="fw-bold text-primary align-middle"><i class="fas fa-envelope me-2"></i>Email Aktif</th>
                                    <td class="align-middle">
                                        <?php if (!empty($report['email_pelapor'])): ?>
                                            <span class="badge bg-primary text-light px-3 py-2"><?= esc($report['email_pelapor']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="fw-bold text-primary align-middle"><i class="fas fa-phone me-2"></i>No. HP Aktif</th>
                                    <td class="align-middle">
                                        <?php if (!empty($report['hp_pelapor'])): ?>
                                            <span class="badge bg-success text-light px-3 py-2"><?= esc($report['hp_pelapor']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="fw-bold text-primary align-middle"><i class="fas fa-tags me-2"></i>Kategori</th>
                                    <td class="align-middle">
                                        <span class="badge bg-primary px-3 py-2"><?= esc($report['category']) ?></span>
                                        <?php if($report['other_category']): ?>
                                            <span class="badge bg-secondary ms-1 px-3 py-2"><?= esc($report['other_category']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="fw-bold text-primary align-middle"><i class="fas fa-map-marker-alt me-2"></i>Lokasi</th>
                                    <td class="align-middle"><?= esc($report['location']) ?></td>
                                </tr>
                                <tr>
                                    <th class="fw-bold text-primary align-middle"><i class="fas fa-info-circle me-2"></i>Status</th>
                                    <td class="align-middle">
                                        <span class="badge px-3 py-2 bg-<?= 
                                            $report['status'] === 'pending' ? 'warning text-dark' : 
                                            ($report['status'] === 'verified' ? 'success' : 
                                            ($report['status'] === 'rejected' ? 'danger' : 
                                            ($report['status'] === 'progress' ? 'warning' : 
                                            ($report['status'] === 'completed' ? 'info' : 'secondary')))) ?>">
                                            <?= ucfirst($report['status']) ?>
                                        </span>
                                    </td>
                                </tr>
<?php
// Kumpulkan semua log status yang punya catatan (note)
$notesLog = [];
if (!empty($statusLogs)) {
    foreach ($statusLogs as $log) {
        if (!empty($log['note'])) {
            $notesLog[] = $log;
        }
    }
}
?>
<?php if (!empty($report['note']) || !empty($notesLog)): ?>
<tr>
    <th class="fw-bold text-primary align-middle"><i class="fas fa-sticky-note me-2"></i>Catatan</th>
    <td class="align-middle">

        <?php foreach ($notesLog as $log): ?>
            <?php
                // Label dan warna sesuai status log
                $label = '';
                $color = '';
                if ($log['status'] === 'verified') {
                    $label = 'Catatan Verifikasi';
                    $color = 'success';
                } elseif ($log['status'] === 'rejected') {
                    $label = 'Catatan Penolakan';
                    $color = 'danger';
                } elseif ($log['status'] === 'progress') {
                    $label = 'Catatan Progress';
                    $color = 'warning';
                } elseif ($log['status'] === 'completed') {
                    $label = 'Catatan Selesai';
                    $color = 'info';
                } elseif ($log['status'] === 'disposition') {
                    $label = 'Catatan Disposisi';
                    $color = 'secondary';
                } else {
                    $label = 'Catatan';
                    $color = 'secondary';
                }
            ?>
            <div class="mb-2 p-2 rounded bg-light border border-<?= $color ?>">
                <span class="fw-bold text-<?= $color ?>">
                    <?= $label ?>:
                </span><br>
                <?= esc($log['note']) ?>
                <?php if ($log['status'] === 'completed' && !empty($log['file'])): ?>
                    <br>
                    <?php
                        $fileUrl = base_url('uploads/status_log/' . $log['file']);
                        $ext = strtolower(pathinfo($log['file'], PATHINFO_EXTENSION));
                    ?>
                    <?php if (in_array($ext, ['jpg', 'jpeg', 'png'])): ?>
                        <a href="<?= $fileUrl ?>" target="_blank" class="badge bg-primary mt-2">
                            <i class="bi bi-image"></i> Lihat Foto Lampiran
                        </a>
                    <?php elseif ($ext === 'pdf'): ?>
                        <a href="<?= $fileUrl ?>" target="_blank" class="badge bg-danger mt-2">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat PDF Lampiran
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <br>
                <small class="text-muted">
                    oleh <?= esc($log['updated_by']) ?> pada <?= date('d M Y H:i', strtotime($log['updated_at'])) ?>
                </small>
            </div>
        <?php endforeach; ?>

        <?php if (empty($report['note']) && empty($notesLog)): ?>
            <span class="text-muted">Tidak ada catatan.</span>
        <?php endif; ?>
    </td>
</tr>
<?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary"><i class="fas fa-align-left me-2"></i>Rincian Aduan</label>
                        <div class="alert alert-secondary shadow-sm" style="min-height: 100px;">
                            <?= nl2br(esc($report['chronology'])) ?>
                        </div>
                    </div>
                    <?php if($userRole === 'admin' && $report['status'] === 'pending'): ?>
<form id="disposisiForm" action="<?= base_url('admin/disposisi/' . $report['sumber'] . '/' . $reportId) ?>" method="post" class="mt-3">
    <?= csrf_field() ?>
    <input type="hidden" name="back" value="<?= esc($_GET['back'] ?? '') ?>">
    <div class="mb-2">
        <label for="kebidangan" class="form-label fw-bold">Pilih Kebidangan Tujuan</label>
        <select name="kebidangan" id="kebidangan" class="form-select" required>
            <option value="">-- Pilih Kebidangan --</option>
            <option value="Sekretariat">Sekretariat</option>
            <option value="PPKL">PPKL</option>
            <option value="TALING">TALING</option>
            <option value="Kebersihan">Kebersihan</option>
        </select>
    </div>
    <button type="submit" class="btn btn-gradient-primary w-100 shadow-sm" id="btnSubmit">
        <i class="fas fa-share-square"></i> Disposisi
    </button>
</form>
<?php endif; ?>
                    <?php
// Cek status terakhir dari statusLogs
$lastStatus = $report['status'];
if (!empty($statusLogs)) {
    $lastStatus = end($statusLogs)['status'];
}
$reportSource = isset($report['id_pengaduan']) ? 'user' : 'anonim';
$reportId = isset($report['id_pengaduan']) ? $report['id_pengaduan'] : $report['id'];
$backUrl = esc($_GET['back'] ?? base_url('admin-bidang/laporan'));
?>

<!-- Form Verifikasi (hanya jika status disposition) -->
<?php if ($userRole === 'admin-bidang' && $report['status'] === 'disposition'): ?>
    <form id="verifyForm" action="<?= base_url('admin-bidang/verify/' . $reportSource . '/' . $reportId) ?>" method="post" class="mt-3">
        <?= csrf_field() ?>
        <input type="hidden" name="back" value="<?= $backUrl ?>">
        <div class="mb-2">
            <label for="status" class="form-label fw-bold">Verifikasi Laporan</label>
            <select name="status" id="status" class="form-select" required>
                <option value="">-- Pilih Status --</option>
                <option value="verified">Setujui</option>
                <option value="rejected">Tolak</option>
            </select>
        </div>
        <div class="mb-2">
            <label for="note2" class="form-label fw-bold">Catatan (Wajib)</label>
            <textarea name="note" id="note2" class="form-control" rows="10" required></textarea>
        </div>
        <button type="submit" class="btn btn-success w-100">Verifikasi</button>
    </form>
<?php endif; ?>

<!-- Form Progress (hanya jika status terakhir verified) -->
<?php if ($userRole === 'admin-bidang' && $lastStatus === 'verified'): ?>
    <form id="updateProgressForm" action="<?= base_url('admin-bidang/update_progress/' . $reportId) ?>" method="post" class="mt-3">
        <?= csrf_field() ?>
        <input type="hidden" name="back" value="<?= $backUrl ?>">
        <div class="mb-2">
            <label for="progress_note" class="form-label fw-bold">Catatan Progress</label>
            <textarea name="progress_note" id="progress_note" class="form-control" rows="10" required></textarea>
        </div>
        <button type="submit" class="btn btn-warning w-100">Set Progress</button>
    </form>
<?php endif; ?>
                    <?php if ($userRole === 'admin-bidang' && $lastStatus === 'progress'): ?>
    <form id="updateCompletedForm" action="<?= base_url('admin-bidang/update_completed/' . $reportId) ?>" method="post" class="mt-3" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="back" value="<?= $backUrl ?>">
        <div class="mb-2">
            <label for="completed_note" class="form-label fw-bold">Catatan Selesai</label>
            <textarea name="completed_note" id="completed_note" class="form-control" rows="10" required></textarea>
        </div>
        <div class="mb-2">
            <label for="completed_file" class="form-label fw-bold">Lampiran (PDF/JPG/JPEG, opsional)</label>
            <input type="file" name="completed_file" id="completed_file" class="form-control" accept=".pdf,.jpg,.jpeg">
            <small class="text-muted">Maksimal 2MB.</small>
        </div>
        <button type="submit" class="btn btn-info w-100">Set Completed</button>
    </form>
<?php endif; ?>
                </div>
                <!-- Kanan: Attachment -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header fw-bold bg-white border-0"><i class="fas fa-paperclip me-2"></i>Attachment</div>
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center" style="background:#444; min-height:320px; border-radius:0 0 0.5rem 0.5rem;">
                            <?php if (!empty($evidence_urls)): ?>
                                <?php foreach ($evidence_urls as $url): ?>
                                    <img src="<?= $url ?>" class="img-fluid mb-3 report-image shadow" style="max-height:300px;object-fit:contain;cursor:pointer;border-radius:8px;" onclick="showImageModal(this.src)">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-white-50">No Attachment</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Modal -->
            <div class="modal fade" id="imageModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Foto Bukti</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="" id="modalImage" class="img-fluid rounded shadow">
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body {
                    background: url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') no-repeat center center fixed;
                    background-size: cover;
                }
                .btn-gradient-primary {
                    background: linear-gradient(90deg,rgb(35, 86, 238) 0%, #224abe 100%);
                    color: #fff;
                    border: none;
                }
                .btn-gradient-primary:hover {
                    background: linear-gradient(90deg, #224abe 0%, #4e73df 100%);
                    color: #fff;
                }
                .report-image {
                    max-width: 100%;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.13);
                    transition: transform 0.2s;
                }
                .report-image:hover {
                    transform: scale(1.04);
                    box-shadow: 0 4px 16px rgba(34, 167, 255, 0.89);
                }
                #modalImage {
                    max-height: 80vh;
                }
                .alert-secondary {
                    background: #f8f9fa;
                    color: #333;
                    border: 1px solid #e2e3e5;
                }
                .card-header.bg-primary {
                    background:rgb(0, 30, 255);
                    color:  rgba(255, 255, 255, 0.33);
                }
                .detail-table th {
                    background:rgba(189, 188, 188, 0.65);      /* abu-abu muda */
                    color: #222 !important;   /* hitam */
                    border-right: 2px solid #e3e6f0;
                    font-size: 1.05em;
                    width: 32%;
                    vertical-align: middle;
                    font-weight: 600;
                }
                .detail-table td {
                    background: #fff;
                    color: #222;
                    font-size: 1.05em;
                    vertical-align: middle;
                    border-left: 2px solid #e3e6f0;
                }
                .detail-table tr {
                    border-bottom: 2px solid #e3e6f0;
                }
                .detail-table {
                    border-radius: 10px;
                    overflow: hidden;
                }
            </style>
            <script>
                function showImageModal(src) {
                    document.getElementById('modalImage').src = src;
                    new bootstrap.Modal(document.getElementById('imageModal')).show();
                }
            </script>
        </div>
    </div>
</div>
<?php if (!empty($report['latitude']) && !empty($report['longitude'])): ?>
    <div class="mb-3">
        <label class="fw-bold text-primary"><i class="fas fa-map-marker-alt me-2"></i>Lokasi di Peta</label>
        <div style="border-radius:12px;overflow:hidden;max-width:100%;height:300px;">
            <iframe
                width="100%"
                height="300"
                frameborder="0"
                style="border:0"
                src="https://www.google.com/maps?q=<?= esc($report['latitude']) ?>,<?= esc($report['longitude']) ?>&hl=id&z=16&output=embed"
                allowfullscreen>
            </iframe>
        </div>
        <div class="mt-2">
            <a href="https://www.google.com/maps?q=<?= esc($report['latitude']) ?>,<?= esc($report['longitude']) ?>" target="_blank" class="btn btn-sm btn-success">
                Lihat di Google Maps
            </a>
            <span class="ms-2 text-muted">(<?= esc($report['latitude']) ?>, <?= esc($report['longitude']) ?>)</span>
        </div>
    </div>
<?php endif; ?>
<script>
// Fungsi untuk menangani form AJAX
function handleAjaxForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Berhasil' : 'Gagal',
                    text: data.message
                }).then(() => {
                    if (data.success) window.location.reload();
                });
            })
            .catch(() => {
                Swal.fire('Error', 'Kesalahan server. Silakan coba lagi nanti.', 'error');
            });
        });
    }
}

// Panggil untuk setiap form
handleAjaxForm('verifyForm');
handleAjaxForm('updateProgressForm');
handleAjaxForm('updateCompletedForm');
handleAjaxForm('disposisiForm'); // jika ada form disposisi
</script>

<?= $this->endSection() ?>