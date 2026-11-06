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
</style>
<div class="container-fluid px-0">
    <div class="user-manajemen-wrapper w-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-white"><i class="bi bi-person-plus"></i> Tambah User</h3>
            <a href="<?= base_url('admin/manajemen-user') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
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
                <form action="<?= base_url('admin/manajemen-user/tambah') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_user" class="form-control" required value="<?= old('nama_user') ?>">
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required value="<?= old('username') ?>">
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="role">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="admin_bidang" <?= old('role') == 'admin_bidang' ? 'selected' : '' ?>>Admin Bidang</option>
                        </select>
                    </div>
                    <div class="mb-3" id="kebidangan-row" style="display: none;">
                        <label for="kebidangan">Kebidangan</label>
                        <select name="kebidangan" id="kebidangan" class="form-select">
                            <option value="">Pilih Kebidangan</option>
                            <option value="Sekretariat" <?= old('kebidangan') == 'Sekretariat' ? 'selected' : '' ?>>Sekretariat</option>
                            <option value="PPKL" <?= old('kebidangan') == 'PPKL' ? 'selected' : '' ?>>PPKL</option>
                            <option value="TALING" <?= old('kebidangan') == 'TALING' ? 'selected' : '' ?>>TALING</option>
                            <option value="Kebersihan" <?= old('kebidangan') == 'Kebersihan' ? 'selected' : '' ?>>Kebersihan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="1" <?= old('status') == '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= old('status', '0') == '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    setTimeout(function() {
        var notif = document.getElementById('notif-alert');
        if (notif) {
            var alert = bootstrap.Alert.getOrCreateInstance(notif);
            alert.close();
        }
    }, 10000);

    document.addEventListener('DOMContentLoaded', function() {
        var roleSelect = document.getElementById('role');
        var kebidanganRow = document.getElementById('kebidangan-row');
        function toggleKebidangan() {
            if (roleSelect.value === 'admin_bidang') {
                kebidanganRow.style.display = '';
            } else {
                kebidanganRow.style.display = 'none';
                document.getElementById('kebidangan').value = '';
            }
        }
        roleSelect.addEventListener('change', toggleKebidangan);
        toggleKebidangan(); // initial state
    });

    // Highlight sidebar menu
    $(function() {
      $(".sidebar .nav-link").removeClass("active");
      $(".sidebar .nav-link:contains('Tambah User')").addClass("active");
    });
</script>
<?= $this->endSection() ?>