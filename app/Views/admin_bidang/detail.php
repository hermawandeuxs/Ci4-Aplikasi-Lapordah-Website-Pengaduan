
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">Detail Laporan #<?= esc($report['tracking_code']) ?></h5>

            <!-- Report Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="140">Status</td>
                            <td>: <span class="badge bg-<?= $report['status'] === 'verified' ? 'success' : 'warning' ?>"><?= ucfirst($report['status']) ?></span></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>: <?= esc($report['category']) ?></td>
                        </tr>
                        <tr>
                            <td>Lokasi</td>
                            <td>: <?= esc($report['location']) ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Laporan</td>
                            <td>: <?= date('d M Y H:i', strtotime($report['created_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Chronology -->
            <div class="mb-4">
                <h6 class="fw-bold">Kronologi:</h6>
                <p><?= esc($report['chronology']) ?></p>
            </div>

            <!-- Evidence Images -->
            <div class="mb-4">
                <h6 class="fw-bold">Bukti:</h6>
                <div class="row">
                    <?php if($report['evidence_1']): ?>
                    <div class="col-md-4">
                        <img src="<?= base_url('uploads/'.$report['evidence_1']) ?>" class="img-fluid rounded">
                    </div>
                    <?php endif; ?>
                    <!-- Add similar blocks for evidence_2 and evidence_3 -->
                </div>
            </div>

            <!-- Progress Update Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Update Progress</h6>
                    <form action="<?= base_url('admin_bidang/update_progress/'.$report['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Status Progress</label>
                            <select name="progress_status" class="form-select" required>
                                <option value="started">Mulai Diproses</option>
                                <option value="ongoing">Sedang Dikerjakan</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan Progress</label>
                            <textarea name="progress_note" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Dokumen (PDF)</label>
                            <input type="file" name="progress_file" class="form-control" accept=".pdf">
                            <small class="text-muted">Max: 2MB, Format: PDF</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Progress
                        </button>
                    </form>
                </div>
            </div>

            <!-- Forward to Admin Form -->
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Teruskan ke Admin</h6>
                    <form action="<?= base_url('admin_bidang/forward_to_admin/'.$report['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan untuk Admin</label>
                            <textarea name="forward_note" class="form-control" rows="3" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-share"></i> Teruskan ke Admin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>