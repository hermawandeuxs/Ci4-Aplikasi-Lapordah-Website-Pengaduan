<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="auth-card" >
    <div class="brand-title">
        <img src="<?= base_url('assets/icons/logo lapordah.png') ?>" alt="Logo" class="brand-logo"> LaporDAH!
    </div>
    
    <h4 class="mb-4">MASUK KE AKUN</h4>
    
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif ?>
    
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php 
            $error = session('error');
            if (is_array($error)) {
                foreach ($error as $err) {
                    echo esc($err) . '<br>';
                }
            } else {
                echo esc($error);
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif ?>

    <form action="<?= base_url('auth/attemptLogin') ?>" method="post" id="loginForm">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Username atau Email</label>
            <input type="text" class="form-control" name="login_id" placeholder="Username atau Email" required value="<?= old('login_id') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn-submit">MASUK</button>
    </form>

    <div class="auth-links">
        <!-- DISABLED: Register link (Pengguna baru hanya bisa ditambahkan oleh admin) -->
        <!-- <p class="mb-0 mt-3">
            Belum punya akun? <a href="<?= base_url('register') ?>">Daftar</a>
        </p> -->
        <p class="mt-2">
            <a href="<?= base_url('/') ?>"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
        </p>
        <p class="mt-2 text-center">
            <a href="<?= base_url('auth/forgot') ?>">Lupa Password?</a>
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