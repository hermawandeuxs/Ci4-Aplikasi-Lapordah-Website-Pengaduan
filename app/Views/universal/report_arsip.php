<?php
// Universal list report view for both admin and admin-bidang roles
// Expects: $title, $reports, $userRole, $userName, $filters, $categories, $perPage, $currentPage, $total, etc.
?>
<?= $this->extend('universal/layout') ?>
<?= $this->section('content') ?>
<?php
// ...di bagian atas file, setelah <?= $this->section('content') ?> 
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
                <i class="bi bi-archive"></i> Daftar Arsip Laporan 
            </h1>
            <a href="<?= base_url('admin/report-arsip') ?>" class="btn btn-light btn-sm shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm mr-2"></i>Kembali ke Daftar Laporan Arsip
            </a>
        </div>

        <!-- Filter Card -->
        <div class="d-flex justify-content-center mb-4">
            <div class="card glass-card mb-4" style="min-width:350px;max-width:700px;width:100%;">
                <div class="card-body">
                    <form action="<?= base_url('admin/report-arsip') ?>" method="get" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Urutan</label>
                                    <select name="sort" class="form-select">
                                        <option value="newest" <?= ($filters['sort'] ?? 'newest') == 'newest' ? 'selected' : '' ?>>Terbaru</option>
                                        <option value="oldest" <?= ($filters['sort'] ?? '') == 'oldest' ? 'selected' : '' ?>>Terlama</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tahun</label>
                                    <select name="year" class="form-select">
                                        <option value="all" <?= (!isset($filters['year']) || $filters['year'] === '' || $filters['year'] === 'all') ? 'selected' : '' ?>>Semua Tahun</option>
                                        <?php if (!empty($years)): ?>
                                            <?php foreach ($years as $y): ?>
                                                <option value="<?= $y ?>" <?= (isset($filters['year']) && $filters['year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel-fill me-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
<?php if (isset($userRole) && $userRole === 'admin'): ?>
    <div class="d-flex justify-content-end mb-3">
        <?php $printYear = (isset($filters['year']) && is_numeric($filters['year'])) ? $filters['year'] : date('Y'); ?>
        <a href="<?= base_url('admin/print-all-arsip?year=' . $printYear) ?>" target="_blank" class="btn btn-danger">
            <i class="fas fa-print"></i> Cetak Semua Laporan tahun <?= $printYear ?>
        </a>
    </div>
<?php endif; ?>


        <!-- DataTables Card -->
        <div class="card glass-card shadow-lg mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-table mr-2"></i>Tabel Arsip Laporan Gabungan
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
                    <table class="table table-hover align-middle w-100" id="reportTable" style="min-width: 1400px; table-layout: fixed;">
                        <thead>
                            <tr>
                                <th style="width:48px; text-align:center;">No</th>
                                <th style="width:170px;">Kode Tracking</th>
                                <th style="width:160px;">Nama Pelapor</th>
                                <th style="width:120px; text-align:center;">Status</th>
                                <th style="width:170px;">Kategori</th>
                                <th style="width:200px;">Lokasi</th>
                                <th style="width:120px; text-align:center;">Tanggal</th>
                                <th style="width:110px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="bi bi-info-circle"></i> Laporan yang dicari tidak ada.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php
                                $sort = $filters['sort'] ?? 'newest';
                                if ($sort === 'oldest') {
                                    $no = $total - (($currentPage - 1) * $perPage);
                                } else {
                                    $no = 1 + (($currentPage - 1) * $perPage);
                                }
                                foreach($reports as $report):
                                ?>
                                <tr>
                                    <td class="text-center"><?= $sort === 'oldest' ? $no-- : $no++ ?></td>
                                    <td>
                                        <span class="d-block" style="word-break:break-all;">
                                            <code class="bg-light px-2 py-1"><?= esc($report['tracking_code']) ?></code>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($report['nama_pelapor'])): ?>
                                            <span class="badge bg-info text-dark px-3 py-2"><?= esc($report['nama_pelapor']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary px-3 py-2">Archived</span>
                                    </td>
                                    <td>
                                        <span><?= esc($report['category']) ?></span>
                                        <?php if(!empty($report['other_category'])): ?>
                                            <span class="badge bg-warning ms-1"><?= esc($report['other_category']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="word-break:break-word;"><?= esc($report['location']) ?></span>
                                    </td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($report['created_at'])) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <?php
                                            $backUrl = current_url() . '?' . http_build_query($_GET);
                                            ?>
                                            <?php if ($userRole === 'admin-bidang'): ?>
                                                <a href="<?= base_url('admin-bidang/laporan/' . $report['id']) . '?back=' . urlencode($backUrl) ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('admin/report/' . $report['id']) . '?back=' . urlencode($backUrl) ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>                           
                                        </div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function unarchiveReport(type, id) {
    Swal.fire({
        title: 'Kembalikan Laporan?',
        text: "Laporan akan dikembalikan ke status sebelumnya.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, kembalikan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('admin/unarchive') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    type: type,
                    id: id,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
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
<script>
    document.getElementById('perPageSelect').addEventListener('change', function() {
    const url = new URL(window.location.href);
    url.searchParams.set('perPage', this.value);
    url.searchParams.set('page_reports', 1); // reset ke halaman 1
    window.location.href = url.toString();
});
</script>
<?= $this->endSection() ?>

