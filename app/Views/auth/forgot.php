<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-card">
    <h4 class="mb-4">Reset Password</h4>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" aria-label="Notifikasi sukses">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup notifikasi"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-label="Notifikasi error">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup notifikasi"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/forgot') ?>" method="post" aria-label="Form reset password">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="email" aria-label="Email Akun">Email Akun</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="Masukkan email Anda" aria-label="Masukkan email Anda">
        </div>
        <button type="submit" class="btn-submit" aria-label="Kirim Link Reset">Kirim Link Reset</button>
    </form>
    <p class="mt-3 text-center">
        <a href="<?= base_url('/login') ?>" aria-label="Kembali ke Login">Kembali ke Login</a>
    </p>
</div>
<?= $this->endSection() ?>