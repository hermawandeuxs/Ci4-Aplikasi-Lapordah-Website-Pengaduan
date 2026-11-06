<?= $this->extend('universal/layout') ?>
<?= $this->section('content') ?>
<style>
    body {
        background: url('<?= base_url('assets/wallpaper/report-bg.jpg') ?>') no-repeat center center fixed;
        background-size: cover;
    }
    .container-fluid {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .user-manajemen-wrapper {
        background: rgba(255,255,255,0.18);
        border-radius: 16px;
        padding: 2rem;
        margin-top: 2rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    }
    .card {
        background: rgba(255,255,255,0.95) !important;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        backdrop-filter: blur(8px);
    }
    .btn-primary, .btn-warning, .btn-danger, .btn-secondary {
        border-radius: 8px !important;
        font-weight: 500;
    }
    .table thead th {
        background: linear-gradient(145deg, #f8f9fc 0%, #edf2f7 100%);
        color: #2d3748;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid rgba(227, 230, 240, 0.7);
    }
    .table tbody tr:hover {
        background: linear-gradient(145deg, #f7fafc 0%, #ebf4ff 100%);
        transform: scale(1.005);
    }
</style>
<div class="container-fluid px-0">
    <div class="user-manajemen-wrapper w-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-white"><i class="bi bi-person-gear"></i> Manajemen User</h3>
            <a href="<?= base_url('admin/manajemen-user/tambah') ?>" class="btn btn-primary" id="btnTambahUser">
                <i class="bi bi-plus"></i> Tambah User
            </a>
        </div>
        <div class="card w-100">
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="notif-alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="notif-alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="get" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label for="role" class="form-label mb-0">Role</label>
                            <select name="role" id="role" class="form-select">
                                <option value="">Semua Role</option>
                                <option value="admin" <?= ($filters['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="admin_bidang" <?= ($filters['role'] ?? '') == 'admin_bidang' ? 'selected' : '' ?>>Admin Bidang</option>
                                <option value="user" <?= ($filters['role'] ?? '') == 'user' ? 'selected' : '' ?>>User</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="kebidangan" class="form-label mb-0">Kebidangan</label>
                            <input type="text" name="kebidangan" id="kebidangan" class="form-control" value="<?= esc($filters['kebidangan'] ?? '') ?>" placeholder="Semua Kebidangan" />
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label mb-0">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>NAMA PENGGUNA</th>
                                <th>USERNAME</th>
                                <th>EMAIL</th>
                                <th>ROLE</th>
                                <th>KEBIDANGAN</th>
                                <th>STATUS</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= esc($user['nama_user'] ?? '') ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <?php
                                    $roleLabels = [
                                        'admin' => 'Admin',
                                        'admin_bidang' => 'Admin Bidang',
                                        'user' => 'User'
                                    ];
                                    ?>
                                    <span class="badge bg-info text-dark">
                                        <?= esc($roleLabels[$user['role']] ?? $user['role']) ?>
                                    </span>
                                </td>
                                <td><?= esc($user['kebidangan'] ?? 'Admin Super') ?></td>
                                <td>
                                    <span class="badge <?= $user['status'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $user['status'] == 1 ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/manajemen-user/edit/'.$user['id']) ?>" class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="<?= base_url('admin/manajemen-user/hapus/'.$user['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                               
                                    <form action="<?= base_url('admin/manajemen-user/reset-password/'.$user['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Reset password user ini ke default?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Reset Password"><i class="bi bi-key"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    $pagerGroup = 'users';
                    $currentPage = (int)($pager->getCurrentPage($pagerGroup) ?? 1);
                    $perPage = $pager->getPerPage($pagerGroup) ?? 10;
                    $total = $pager->getTotal($pagerGroup) ?? 0;
                    $totalPages = $pager->getPageCount($pagerGroup) ?? 1;
                    $baseUrl = current_url();
                    $queryArr = $_GET;
                    unset($queryArr['page_users']);
                    $baseQuery = http_build_query($queryArr);
                    $baseUrlWithQuery = $baseUrl . ($baseQuery ? '?' . $baseQuery . '&' : '?');
                ?>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Menampilkan <?= count($users) ?> dari <?= $total ?> user
                    </div>
                    <div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $currentPage > 1 ? $baseUrlWithQuery . 'page_users=' . ($currentPage - 1) : '#' ?>" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= $baseUrlWithQuery . 'page_users=' . $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $currentPage < $totalPages ? $baseUrlWithQuery . 'page_users=' . ($currentPage + 1) : '#' ?>" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
setTimeout(function() {
        var notif = document.getElementById('notif-alert');
        if (notif) {
            // Bootstrap 5 dismiss
            var alert = bootstrap.Alert.getOrCreateInstance(notif);
            alert.close();
        }
    }, 10000); // 10 detik

// Highlight sidebar menu
$(function() {
  $(".sidebar .nav-link").removeClass("active");
  $(".sidebar .nav-link:contains('Daftar Admin & User')").addClass("active");
});
</script>
<?= $this->endSection() ?>