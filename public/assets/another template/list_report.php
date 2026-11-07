<?= $this->extend('admin/layouts') ?>

<?= $this->section('content') ?>
<!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="report-wrapper">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-white">
            <i class="bi bi-file-text">Daftar Semua Laporan</i>
        </h1>
        <div>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-light btn-sm shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm mr-2"></i>Kembali ke Dashboard
            </a>
            <button class="btn btn-primary btn-sm shadow-sm" onclick="window.print()">
                <i class="fas fa-download fa-sm mr-2"></i>Generate Report
            </button>
        </div>
    </div>

    
    <!-- Filter Card -->
    <div class="card glass-card mb-4" style="width:100%;">
        <div class="card-body">
            <form action="" method="get" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" <?= ($filters['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="verified" <?= ($filters['status'] ?? '') == 'verified' ? 'selected' : '' ?>>Disetujui</option>
                                <option value="disposition" <?= ($filters['status'] ?? '') == 'disposition' ? 'selected' : '' ?>>Disposisi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category" class="form-select">
                                <option value="all" <?= (!isset($filters['category']) || $filters['category'] === '' || $filters['category'] === 'all') ? 'selected' : '' ?>>Semua Kategori</option>
                                <?php
                                $mainCategories = ['Infrastruktur', 'Pelayanan publik', 'Lingkungan'];
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
                                <?php   endif;
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
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100" style="height:38px;">
                                <i class="bi bi-funnel-fill"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTables Card -->
    <div class="card glass-card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">
                Tabel Laporan
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
                <table class="table table-hover" id="reportTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Tracking</th>
                            <th>Nama Pelapor</th>
                            <th class="text-center">Status</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Kronologi</th> <!-- Tambahkan ini -->
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">
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
                                <td><?= $sort === 'oldest' ? $no-- : $no++ ?></td>
                                <td><code class="bg-light px-2 py-1"><?= esc($report['tracking_code']) ?></code></td>
                                <td>
                                    <?php if (!empty($report['username'])): ?>
                                        <?= esc($report['username']) ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Anonim</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusClass = match(strtolower($report['status'])) {
                                        'verified' => 'success',
                                        'rejected' => 'danger',
                                        'pending' => 'warning',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?> px-3 py-2">
                                        <?= ucfirst($report['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= esc($report['category']) ?>
                                    <?php if($report['other_category']): ?>
                                        <span class="badge bg-secondary ms-1">
                                            <?= esc($report['other_category']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($report['location']) ?></td>
                                <td><?= esc($report['chronology']) ?></td> <!-- Tambahkan ini -->
                                <td class="text-center"><?= date('d/m/Y', strtotime($report['created_at'])) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url('admin/report/' . ($report['id_pengaduan'] ?? $report['id'])) . '?' . $_SERVER['QUERY_STRING'] ?>" class="btn btn-info btn-sm" data-toggle="tooltip" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="archiveReport('<?= $report['id'] ?>')" data-toggle="tooltip" title="Arsipkan">
                                            <i class="fas fa-archive"></i>
                                        </button>
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

    <!-- Pagination -->
    <?php
        // Ambil variabel dari controller, bukan dari $pager
        $currentPage = $currentPage ?? 1;
        $perPage = $perPage ?? 10;
        $total = $total ?? 0;
        $totalPages = $totalPages ?? 1;

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

<style>
body {
    background: url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') no-repeat center center fixed;
    background-size: cover;
}
.report-wrapper {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0) 0%, rgba(255,255,255,0.5) 100%),
                url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') center/cover fixed;
    padding: 2rem;
    border-radius: 15px;
    min-height: calc(100vh - 100px);
}

/* Enhanced Glass Card */
.glass-card {
    background: rgba(255, 255, 255, 0.25) !important;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.glass-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.glass-card .card-header {
    background: rgba(255, 255, 255, 0.18);
    border-bottom: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 20px 20px 0 0;
    padding: 1.25rem;
}

/* Enhanced Table Styling */
.table {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 15px;
    overflow: hidden;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
}

#reportTable thead th {
    background: linear-gradient(145deg, #f8f9fc 0%, #edf2f7 100%);
    color: #2d3748;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1rem;
    border-bottom: 2px solid rgba(227, 230, 240, 0.7);
}

#reportTable tbody tr {
    transition: all 0.2s ease;
}

#reportTable tbody tr:hover {
    background: linear-gradient(145deg, #f7fafc 0%, #ebf4ff 100%);
    transform: scale(1.005);
}

#reportTable td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid rgba(227, 230, 240, 0.4);
    color: #222; /* Lebih gelap untuk keterbacaan */
}

/* Status Badge Enhancements */
.badge {
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 0.6em 1.2em;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    font-size: 0.85em;
}

.badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
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

/* Add to your existing style section */
.badge.bg-secondary {
    background: linear-gradient(145deg, #718096 0%, #4a5568 100%) !important;
    color: #fff !important;
    font-size: 0.75em;
    padding: 0.3em 0.6em;

}

/* Action Buttons Enhancement */
.btn-group {
    display: flex !important; /* Force display flex */
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

/* Hover effects */
.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Make sure icons are visible */
.fas, .bi {
    display: inline-block !important;
    width: auto !important;
    height: auto !important;
    vertical-align: middle !important;
}

/* DataTables Horizontal Layout Enhancement */
.dataTables_wrapper {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border-radius: 10px;
    margin: 1rem 0;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin: 0 0 1rem 0;
}

/* Horizontal Controls Container */
.dataTables_wrapper .dt-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

/* Length Control */
.dataTables_wrapper .dataTables_length {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dataTables_wrapper .dataTables_length label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    white-space: nowrap;
}

.dataTables_wrapper .dataTables_length select {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    padding: 0.4rem 2rem 0.4rem 0.8rem;
    width: auto;
}

/* Search Control */
.dataTables_wrapper .dataTables_filter {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dataTables_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.dataTables_wrapper .dataTables_filter input {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    padding: 0.4rem 0.8rem;
    width: 200px;
    margin-left: 0.5rem;
}

/* Pagination Buttons */
.dataTables_wrapper .dataTables_paginate {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.dataTables_wrapper .paginate_button {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #333 !important;
    border-radius: 6px;
    padding: 0.4rem 0.8rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.dataTables_wrapper .paginate_button.current {
    background: rgba(255, 255, 255, 0.9);
    color: #4a5568 !important;
    font-weight: bold;
}

.dataTables_wrapper .paginate_button:hover:not(.current) {
    background: rgba(255, 255, 255, 0.2);
    color: #000 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dataTables_wrapper .dt-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        width: 100%;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .report-wrapper {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

@media print {
    .report-wrapper {
        background: none;
        padding: 0;
    }
    
    .glass-card {
        background: white !important;
        backdrop-filter: none;
        border: none;
    }
    
    .btn-group, .dropdown, .dataTables_length, .dataTables_filter, .dataTables_paginate {
        display: none !important;
    }
}
</style>

<!-- Tambahkan di bagian head untuk memastikan Font Awesome dan Bootstrap Icons dimuat -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>

function archiveReport(id) {
    Swal.fire({
        title: 'Arsipkan Laporan?',
        text: "Laporan yang diarsipkan tidak akan ditampilkan di daftar laporan aktif.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, arsipkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('admin/archive/') ?>' + id,
                type: 'POST',
                dataType: 'json',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            title: 'Terarsipkan!',
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
// Clear filters
$('.clear-filter').click(function(e) {
    e.preventDefault();
    window.location.href = '<?= base_url('admin/reports') ?>';
});

document.getElementById('perPageSelect').addEventListener('change', function() {
    const url = new URL(window.location.href);
    url.searchParams.set('perPage', this.value);
    url.searchParams.set('page_reports', 1); // reset ke halaman 1
    window.location.href = url.toString();
});
</script>

<?= $this->endSection() ?>