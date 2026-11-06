<?php
// Universal rejected reports view for admin & admin-bidang
// Expects: $reports, $filters, $categories, $userRole, $title, $pager, etc.
?>
<?= $this->extend('universal/layout') ?>
<?= $this->section('content') ?>
<style>
body {
    background: url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', Arial, sans-serif;
}
.report-wrapper {
    background: linear-gradient(135deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.5) 100%),
                url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') center/cover fixed;
    padding: 2rem;
    border-radius: 15px;
    min-height: calc(100vh - 100px);
}
.glass-card {
    background: rgba(255,255,255,0.25) !important;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.glass-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.glass-card .card-header {
    background: rgba(255,255,255,0.18);
    border-bottom: 1px solid rgba(255,255,255,0.25);
    border-radius: 20px 20px 0 0;
    padding: 1.25rem;
}
.table {
    background: rgba(255,255,255,0.98);
    border-radius: 15px;
    overflow: hidden;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
}
#reportTable th, #reportTable td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
}
#reportTable th {
    background: linear-gradient(145deg, #f8f9fc 0%, #edf2f7 100%);
    color: #2d3748;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(227,230,240,0.7);
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}
#reportTable td {
    padding: 1rem;
    border-bottom: 1px solid rgba(227,230,240,0.4);
    color: #222;
}
#reportTable tbody tr {
    transition: all 0.2s ease;
}
#reportTable tbody tr:hover {
    background: linear-gradient(145deg, #f7fafc 0%, #ebf4ff 100%);
    transform: scale(1.005);
}
.badge {
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 0.6em 1.2em;
    border-radius: 8px;
    font-size: 0.85em;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.bg-success {
    background: linear-gradient(145deg, #48bb78 0%, #38a169 100%) !important;
    color: #fff !important;
}
.bg-warning {
    background: linear-gradient(145deg, #ecc94b 0%, #d69e2e 100%) !important;
    color: #fff !important;
}
.bg-danger {
    background: linear-gradient(145deg, #f56565 0%, #e53e3e 100%) !important;
    color: #fff !important;
}
.badge.bg-secondary {
    background: linear-gradient(145deg, #718096 0%, #4a5568 100%) !important;
    color: #fff !important;
    font-size: 0.75em;
    padding: 0.3em 0.6em;
}
.btn-group {
    display: flex !important;
    gap: 5px;
    justify-content: center;
}
.btn-group .btn {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 4px !important;
}
.btn-group .btn i {
    font-size: 14px;
    line-height: 1;
}
.btn-info {
    background: #4299e1;
    border-color: #4299e1;
    color: white !important;
}
.btn-warning {
    background: #ed8936;
    border-color: #ed8936;
    color: white !important;
}
.btn-danger {
    background: #f56565;
    border-color: #f56565;
    color: white !important;
}
.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.fas, .bi {
    display: inline-block !important;
    width: auto !important;
    height: auto !important;
    vertical-align: middle !important;
}
.table-responsive {
    overflow-x: auto;
}
@media (max-width: 768px) {
    .report-wrapper { padding: 1rem; }
    .card-body { padding: 1rem; }
}
@media print {
    .report-wrapper { background: none; padding: 0; }
    .glass-card { background: white !important; backdrop-filter: none; border: none; }
    .btn-group, .dropdown, .dataTables_length, .dataTables_filter, .dataTables_paginate { display: none !important; }
}
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="container-fluid py-4 h-100 d-flex flex-column">
    <div class="report-wrapper w-100">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-white">
                <i class="bi bi-x-octagon">Daftar Laporan Tertolak</i> 
            </h1>
            <a href="<?= base_url('admin/reports') ?>" class="btn btn-light btn-sm shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm mr-2"></i>Kembali ke Daftar Laporan
            </a>
        </div>

        <!-- Filter Card (lihat contoh di atas) -->
        <div class="d-flex justify-content-center mb-4">
            <div class="card glass-card mb-4" style="min-width:350px;max-width:700px;width:100%;">
                <div class="card-body">
                    <form action="<?= base_url('admin/laporan-reject') ?>" method="get" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="category" class="form-select">
                                        <option value="all" <?= (!isset($filters['category']) || $filters['category'] === '' || $filters['category'] === 'all') ? 'selected' : '' ?>>Semua Kategori</option>
                                        <?php
                                        $mainCategories = ['Pencemaran', 'Sampah', 'Perapihan pohon'];
                                        foreach ($mainCategories as $mainCat): ?>
                                            <option value="<?= $mainCat ?>" <?= ($filters['category'] ?? '') == $mainCat ? 'selected' : '' ?>><?= $mainCat ?></option>
                                        <?php endforeach; ?>
                                        <?php if (!empty($otherCategories)): ?>
                                            <option value="Lainnya" <?= ($filters['category'] ?? '') == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                        <?php endif; ?>
                                        <?php
                                        if (!empty($categories)) {
                                            foreach ($categories as $cat) {
                                                if (!in_array($cat['category'], $mainCategories) && $cat['category'] !== null && $cat['category'] !== ''): ?>
                                                    <option value="<?= $cat['category'] ?>" <?= ($filters['category'] ?? '') == $cat['category'] ? 'selected' : '' ?>><?= $cat['category'] ?></option>
                                        <?php endif;
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Urutan</label>
                                    <select name="sort" class="form-select">
                                        <option value="newest" <?= ($filters['sort'] ?? 'newest') == 'newest' ? 'selected' : '' ?>>Terbaru</option>
                                        <option value="oldest" <?= ($filters['sort'] ?? '') == 'oldest' ? 'selected' : '' ?>>Terlama</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center" style="font-weight:600;font-size:1.1em;">
                                    <i class="bi bi-funnel-fill me-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- DataTables Card -->
        <div class="card glass-card shadow-lg mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-table mr-2"></i>Tabel Laporan Tertolak
                </h6>
                <div class="d-flex align-items-center">
                    <label for="perPageSelect" class="me-2 mb-0 text-white">Tampilkan</label>
                    <select id="perPageSelect" name="perPage" class="form-select form-select-sm" style="width:auto; display:inline-block;">
                        <?php foreach ([10, 25, 50, 100] as $num): ?>
                            <option value="<?= $num ?>" <?= $perPage == $num ? 'selected' : '' ?>><?= $num ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="ms-2 text-white">data</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="reportTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Sumber</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="bi bi-info-circle"></i> Tidak ada laporan tertolak.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reports as $i => $report): ?>
                                    <tr>
                                        <td class="text-center"><?= (($currentPage - 1) * $perPage) + $i + 1 ?></td>
                                        <td>
                                            <?= esc($report['category']) ?>
                                            <?php if (!empty($report['other_category'])): ?>
                                                <span class="badge bg-secondary ms-1">
                                                    <?= esc($report['other_category']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($report['location']) ?></td>
                                        <td class="text-center"><?= date('d/m/Y', strtotime($report['created_at'])) ?></td>
                                        <td class="text-center"><span class="badge bg-danger">Tertolak</span></td>
                                        <td class="text-center">
                                            <?php if ($report['sumber'] === 'user'): ?>
                                                <span class="badge bg-primary">User</span>
                                            <?php elseif ($report['sumber'] === 'anonim'): ?>
                                                <span class="badge bg-secondary">Umum</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Tidak diketahui</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $backUrl = current_url() . '?' . http_build_query($_GET);
                                            ?>
                                            <?php if ($userRole === 'admin-bidang'): ?>
                                                <?php if ($report['sumber'] === 'user'): ?>
                                                    <a href="<?= base_url('admin-bidang/laporan/user/' . $report['id']) . '?back=' . urlencode($backUrl) ?>" class="btn btn-info btn-sm" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('admin-bidang/laporan/' . $report['id']) . '?back=' . urlencode($backUrl) ?>" class="btn btn-info btn-sm" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if ($report['sumber'] === 'user'): ?>
                                                    <a href="<?= base_url('admin/report/user/' . $report['id']) . '?back=' . urlencode($backUrl) ?>" class="btn btn-info btn-sm" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('admin/report/' . $report['id']) . '?back=' . urlencode($backUrl) ?>" class="btn btn-info btn-sm" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <button onclick="deleteReport('<?= $report['sumber'] ?>', <?= $report['id'] ?>)"
                                                    class="btn btn-danger btn-sm"
                                                    title="Hapus Laporan">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
$baseUrl = current_url();
$queryArr = $_GET;
unset($queryArr['page_reports']);
$baseQuery = http_build_query($queryArr);
$baseUrlWithQuery = $baseUrl . ($baseQuery ? '?' . $baseQuery . '&' : '?');
?>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                Menampilkan <?= count($reports) ?> dari <?= $total ?> laporan
            </div>
            <div>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage > 1 ? $baseUrlWithQuery . 'page_reports=' . ($currentPage - 1) : '#' ?>" aria-label="Previous">
                            <span aria-hidden="true">&lt;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrlWithQuery . 'page_reports=' . $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage < $totalPages ? $baseUrlWithQuery . 'page_reports=' . ($currentPage + 1) : '#' ?>" aria-label="Next">
                            <span aria-hidden="true">&gt;</span>
                        </a>
                    </li>
                </ul>
            </nav>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Ambil baseUrl dari PHP, fallback ke '/' jika tidak ada
    var baseUrl = "<?= base_url() ?: '/' ?>";
</script>
<script>
function deleteReport(sumber, id) {
    let url = '';
    <?php if ($userRole === 'admin-bidang'): ?>
        if (sumber === 'user') {
            url = baseUrl + 'admin-bidang/delete-user/' + id;
        } else {
            url = baseUrl + 'admin-bidang/delete-anonim/' + id;
        }
    <?php else: ?>
        url = baseUrl + 'admin/delete/' + id;
    <?php endif; ?>

    let title = sumber === 'anonim' ? 'Hapus Laporan Anonim?' : 'Hapus Laporan User?';

    Swal.fire({
        title: title,
        text: "Laporan yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus permanent!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server.',
                        icon: 'error'
                    });
                }
            });
        }
    });
}
</script>

<?= $this->endSection() ?>
