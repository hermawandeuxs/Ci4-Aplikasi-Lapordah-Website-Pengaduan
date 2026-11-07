<?= $this->extend('admin/layouts') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i> Detail Laporan <span class="text-warning">#<?= esc($report['tracking_code']) ?></span>
            </h5>
            <a href="<?= base_url('admin/reports') . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light btn-sm shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body bg-light">
            <div class="row g-4">
                <!-- Kiri: Detail Laporan -->
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded shadow-sm mb-3">
                        <div class="mb-2">
                            <span class="fw-bold text-secondary"><i class="fas fa-hashtag me-2"></i>ID Complaint</span>
                            <div class="form-control-plaintext ps-4"><?= esc($report['tracking_code']) ?></div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-bold text-secondary"><i class="fas fa-calendar-alt me-2"></i>Tanggal Laporan</span>
                            <div class="form-control-plaintext ps-4"><?= date('d M Y H:i', strtotime($report['created_at'])) ?></div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-bold text-secondary"><i class="fas fa-user me-2"></i>Nama Pelapor</span>
                            <div class="ps-4">
                                <?php if (!empty($report['username'])): ?>
                                    <span class="badge bg-info text-dark px-3 py-2"><?= esc($report['username']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-3 py-2">Anonim</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-bold text-secondary"><i class="fas fa-tags me-2"></i>Kategori</span>
                            <div class="ps-4">
                                <span class="badge bg-primary px-3 py-2"><?= esc($report['category']) ?></span>
                                <?php if($report['other_category']): ?>
                                    <span class="badge bg-secondary ms-1 px-3 py-2"><?= esc($report['other_category']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-bold text-secondary"><i class="fas fa-map-marker-alt me-2"></i>Lokasi</span>
                            <div class="form-control-plaintext ps-4"><?= esc($report['location']) ?></div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-bold text-secondary"><i class="fas fa-info-circle me-2"></i>Status</span>
                            <div class="ps-4">
                                <span class="badge px-3 py-2 bg-<?= 
                                    $report['status'] === 'pending' ? 'warning text-dark' : 
                                    ($report['status'] === 'verified' ? 'success' : 
                                    ($report['status'] === 'rejected' ? 'danger' : 'secondary')) ?>">
                                    <?= ucfirst($report['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary"><i class="fas fa-align-left me-2"></i>Rincian Aduan</label>
                        <div class="alert alert-secondary shadow-sm" style="min-height: 100px;">
                            <?= nl2br(esc($report['chronology'])) ?>
                        </div>
                    </div>
                    <?php if($report['status'] === 'pending'): ?>
                    <form id="disposisiForm" action="<?= base_url('admin/disposisi/' . $report['id']) ?>" method="post" class="mt-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="disposition">
                        <button type="submit" class="btn btn-gradient-primary w-100 shadow-sm" id="btnSubmit">
                            <i class="fas fa-share-square"></i> Disposisi
                        </button>
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

            <style>
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
                    box-shadow: 0 4px 16px rgba(0,0,0,0.18);
                }
                #modalImage {
                    max-height: 80vh;
                }
                .alert-secondary {
                    background: #f8f9fa;
                    color: #333;
                    border: 1px solid #e2e3e5;
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
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('disposisiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "<?= base_url('admin/reports') ?>";
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan server.'
        });
    });
});
</script>
<?= $this->endSection() ?>