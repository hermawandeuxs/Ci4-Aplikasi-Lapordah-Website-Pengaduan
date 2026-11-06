<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="brand-title">
        <img src="<?= base_url('assets/icons/maskot.png') ?>" alt="Logo" class="brand-logo"> LaporDAH!
    </div>
    
    <h4 class="mb-4">DAFTAR AKUN BARU</h4>
    
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach(session('errors') as $error): ?>
                <?= esc($error) ?><br>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <form action="<?= base_url('register/process') ?>" method="post" id="registerForm">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" 
                   placeholder="Masukkan username" required value="<?= old('username') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" 
                   placeholder="Masukkan email valid" required value="<?= old('email') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" 
                   placeholder="Minimal 6 karakter" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" name="confirm_password" 
                   placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="btn-submit">DAFTAR</button>
    </form>

    <div class="auth-links">
        <p class="mb-0 mt-3">
            Sudah punya akun? <a href="<?= base_url('login') ?>">Masuk</a>
        </p>
    </div>
</div>
<style>
    .brand-title {
    text-align: center;
    margin-bottom: 20px;
}

.brand-logo {
    height: 60px;
    width: auto;
    display: block;
    margin: 0 auto 8px auto;
}

.brand-text {
    font-size: 24px;
    font-weight: bold;
    color: #00cc44; /* hijau cerah */
    margin: 0;
}
</style>

<?= $this->endSection() ?>