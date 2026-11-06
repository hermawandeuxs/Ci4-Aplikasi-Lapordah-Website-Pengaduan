<?= $this->include('layouts/header') ?>

<!-- Flash Messages -->
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


<!-- Home Section -->
<section id="home" class="hero-section">
    <div class="container text-center">
        <h1 class="display-6 fw-bold mb-3" style="color:white;">LaporDAH</h1>
        <p class="lead fw-normal mb-4" style="color: white;">Portal Laporan Pengaduan Online Dinas Lingkungan Hidup Pekalongan</p>
        
    <div class="container text-center mt-4"> <!-- Search box remains below -->
        <div class="search-box mt-3" style="max-width: 700px;">
            <form id="searchForm" onsubmit="searchReport(event)" autocomplete="off">
                <p class="lead fw-normal mb-2" style="color: black; font-size: 0.9rem; text-align: left;">
                    Cari laporan berdasarkan kode, lokasi, kategori, atau kronologi.
                </p>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchTrackingCode"
                           name="q"
                           placeholder="Cari laporan (kode, lokasi, kategori, kronologi)..."
                           style="border-radius: 8px;">
                    <button type="submit" class="btn btn-danger" style="border-radius: 8px;">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

     <!-- Total Visitors Section -->
     <div class="total-visitors-box mt-4">
            <div class="rectangle-card" style="
                background: rgba(255, 255, 255, 0.9);
                border-radius: 10px;
                padding: 15px 20px;
                width: 300px;
                margin: 0 auto;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                text-align: center;">
                <h5 style="
                    font-size: 1.2rem;
                    color: #28a745;
                    margin-bottom: 10px;">Total Pengunjung</h5>
                <div class="visitor-info" style="
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-top: 10px;">
                    <img src="<?= base_url('assets/icons/user.png') ?>" alt="Visitors Icon" style="
                        height: 25px;
                        margin-right: 8px;">
                    <span style="
                        font-size: 1.5rem;
                        font-weight: bold;
                        color: #007bff;"><?= $totalVisitors ?></span>
                </div>
                <p style="
                    font-size: 0.85rem;
                    margin-top: 10px;
                    color: #6c757d;">Jumlah pengunjung yang telah mengakses portal ini.</p>
            </div>
        </div>
    </div>
</section>

<?php
// Menambahkan statistik laporan
$totalReports = $totalReports ?? 0;
$pendingReports = $pendingReports ?? 0;
$processedReports = $processedReports ?? 0;
?>

<
<!-- filepath: app/Views/welcome_message.php -->
<section id="statistics" class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="mb-4">Statistik Laporan</h2>
        <div class="row justify-content-center">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Laporan</h5>
                        <p class="card-text display-4 text-primary" id="totalReports"><?= $totalReports ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Laporan Pending</h5>
                        <p class="card-text display-4 text-warning" id="pendingReports"><?= $pendingReports ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Laporan Diproses</h5>
                        <p class="card-text display-4 text-success" id="verifiedReports"><?= $verifiedReports ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Laporan Selesai</h5>
                        <p class="card-text display-4 text-success" id="completedReports"><?= $completedReports ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="complaint" class="complaint-form bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Buat Pengaduan</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="step-indicator mb-4 text-center">
                            <span class="badge bg-primary" id="step1-badge">Ketentuan</span> 
                            <span class="mx-2">→</span>
                            <span class="badge bg-secondary" id="step2-badge">Data Diri</span> 
                            <span class="mx-2">→</span>
                            <span class="badge bg-secondary" id="step3-badge">Form Laporan</span>
                        </div>
                        <!-- Step 1: Ketentuan -->
                        <div id="step1">
                            <h5 class="mb-3">Ketentuan Pengiriman Laporan</h5>
                            <ul>
                                <li>Laporan harus berisi informasi yang benar dan dapat dipertanggungjawabkan.</li>
                                <li>Foto bukti maksimal 3 file, format JPG/PNG, maksimal 2MB per file.</li>
                                <li>Data pribadi Anda akan dijaga kerahasiaannya.</li>
                                <li>Laporan yang mengandung unsur SARA, hoaks, atau fitnah tidak akan diproses.</li>
                            </ul>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="setujuKetentuan">
                                <label class="form-check-label" for="setujuKetentuan">
                                    Saya telah membaca dan menyetujui ketentuan di atas.
                                </label>
                            </div>
                            <button type="button" class="btn btn-success mt-3" id="toStep2" disabled>Lanjut</button>
                        </div>
                        <!-- Step 2: Data Diri -->
                        <div id="step2" style="display:none;">
                            <h5 class="mb-3">Data Diri Pelapor</h5>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="namaPelapor" placeholder="Nama Anda">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="alamatPelapor" placeholder="Alamat Anda">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="emailPelapor" placeholder="Email aktif">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="hpPelapor" placeholder="Nomor HP aktif">
                            </div>
                            <button type="button" class="btn btn-secondary" id="backToStep1">Kembali</button>
                            <button type="button" class="btn btn-success" id="toStep3">Lanjut</button>
                        </div>
                        <!-- Step 3: Form Laporan -->
                        <div id="step3" style="display:none;">
                            <form id="laporanForm" method="post" enctype="multipart/form-data" action="<?= base_url('laporan-anon/submit') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Kategori Pengaduan <span class="text-danger">*</span></label>
            <select class="form-select" name="category" id="category" required>
                <option value="" disabled selected>Pilih kategori...</option>
                <option value="Pencemaran">Pencemaran Lingkungan</option>
                <option value="Sampah">Sampah</option>
                <option value="Perapihan pohon">Perapihan pohon</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div class="mb-3" id="kategoriLainnya" style="display: none;">
            <label class="form-label">Kategori Lainnya <span class="text-danger">*</span></label>
            <textarea class="form-control" name="other_category" rows="2" placeholder="Masukkan kategori lainnya"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Lokasi Kejadian <span class="text-danger">*</span></label>
            <textarea class="form-control" name="location" rows="3" placeholder="Masukkan lokasi kejadian" required></textarea>
            <div class="form-text">Contoh: Jl. Mawar No. 10, Pekalongan</div>
            <button type="button" class="btn btn-outline-success btn-sm mt-2" id="ambilLokasi">
        <i class="bi bi-geo-alt"></i> Ambil Lokasi Otomatis
    </button>
    <button type="button" class="btn btn-outline-danger btn-sm mt-2 ms-2" id="resetLokasi">
    <i class="bi bi-x-circle"></i> Reset Lokasi
    </button>
    <div id="lokasiStatus" class="form-text text-success"></div>
    <!-- Hidden input untuk koordinat -->
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
        </div>
        <div class="mb-3">
            <label class="form-label">Kronologi Kejadian <span class="text-danger">*</span></label>
            <textarea class="form-control" name="chronology" rows="5" placeholder="Jelaskan kronologi kejadian secara rinci" required></textarea>
            <div id="chronology-char-info" class="form-text text-danger">Tuliskan waktu, pelaku, dan detail kejadian jika ada.</div>
        </div>
        <div class="mb-3">
            <label for="evidence" class="form-label">Foto Bukti (Maksimal 3 foto)</label>
            <input type="file" class="form-control" name="evidence[]" id="evidence" accept=".jpg,.jpeg,.png" multiple>
            <div class="form-text">Format: JPG, JPEG, PNG. Maksimal 2MB per file</div>
            <div class="file-count">0 files</div>
            <div class="image-preview-wrapper"></div>
        </div>
        <button type="submit" class="btn btn-success w-100">Kirim Pengaduan</button>
    </form>
                     </div>
                </div>
            </div>                    
        </div>
    </div>
    </div>
    </div>
</section>

<?= $this->include('layouts/footer') ?>
<style>
     .floating-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #28a745;
            color: white;
            border: white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .floating-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
</style>
<button class="floating-button scroll-top" id="btnHome" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; background-color:rgb(0, 255, 21); border: none; border-radius: 50%; padding: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); cursor: pointer;">
    <img src="/assets/icons/home.png" alt="Scroll to Top" style="height: 20px;">
</button>

<div id="search-loading" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:rgba(255,255,255,0.7);align-items:center;justify-content:center;">
    <div class="spinner-border text-success" style="width:3rem;height:3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   
   document.getElementById('btnHome').addEventListener('click', function() {
    // Scroll ke atas (section Home)
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Reset highlight nav-link "Buat Aduan"
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
});

window.addEventListener('scroll', function() {
    const btn = document.getElementById('btnHome');
    if (window.scrollY > 100) {
        btn.style.display = 'flex';
    } else {
        btn.style.display = 'none';
    }
});

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            
            if (targetId === '#page-top') {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                return;
            }
            
            const targetElement = document.querySelector(targetId);
            const navbarHeight = document.querySelector('.navbar').offsetHeight;
            const targetPosition = targetElement.offsetTop - navbarHeight;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        });
    });
// Step navigation handling
function getLocationWithOptions(successCallback, errorCallback, options = {}) {
    if (!navigator.geolocation) {
        if (typeof errorCallback === 'function') {
            errorCallback({ code: 0, message: 'Geolocation tidak didukung di browser ini.' });
        }
        return;
    }
    const defaultOptions = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    };
    navigator.geolocation.getCurrentPosition(
        successCallback,
        errorCallback,
        Object.assign(defaultOptions, options)
    );
}

document.getElementById('ambilLokasi').addEventListener('click', function() {
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const lokasiStatus = document.getElementById('lokasiStatus');
    const lokasiTextarea = document.getElementById('lokasiTextarea');

    lokasiStatus.innerHTML = '<span class="text-info">Mengambil lokasi... Mohon tunggu, pastikan GPS aktif dan sinyal bagus.</span>';

    getLocationWithOptions(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;
            latInput.value = lat;
            lonInput.value = lng;
            if (lokasiTextarea) lokasiTextarea.value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;

            // Cek wilayah Pekalongan
            const inPekalongan = (lat > -6.91 && lat < -6.80 && lng > 109.60 && lng < 109.75);
            let info = `Koordinat: (${lat.toFixed(5)}, ${lng.toFixed(5)}) | Akurasi: ±${Math.round(accuracy)} meter
                <a href="https://maps.google.com/?q=${lat},${lng}&z=18" target="_blank" class="badge bg-danger mt-1" style="font-size:0.95em;">
                    Lihat di Google Maps
                </a>`;
            if (!inPekalongan) {
                info += `<br><span class="badge bg-danger mt-1" style="font-size:0.95em;">Di luar jangkauan Kota Pekalongan</span>`;
            }
            lokasiStatus.innerHTML = info;

            if (accuracy > 1000) {
                Swal.fire({
                    title: 'Akurasi Lokasi Sangat Rendah',
                    text: 'Perangkat Anda tidak bisa mendapatkan lokasi akurat. Coba aktifkan GPS dan pindah ke area terbuka.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                latInput.value = '';
                lonInput.value = '';
                if (lokasiTextarea) lokasiTextarea.value = '';
            } else if (accuracy > 30) {
                Swal.fire({
                    title: 'Akurasi Lokasi Rendah',
                    text: 'Sinyal GPS kurang baik. Silakan pindah ke area terbuka untuk hasil lebih akurat.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        },
        function(error) {
            let message = '';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    message = 'Izin lokasi ditolak. Aktifkan izin lokasi di browser Anda.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Informasi lokasi tidak tersedia.';
                    break;
                case error.TIMEOUT:
                    message = 'Permintaan lokasi melebihi batas waktu.';
                    break;
                default:
                    message = 'Terjadi kesalahan saat mengambil lokasi.';
            }
            lokasiStatus.innerHTML = `<span class="text-danger">${message}</span>`;
            latInput.value = '';
            lonInput.value = '';
            if (lokasiTextarea) lokasiTextarea.value = '';
        }
    );
});

document.getElementById('resetLokasi').addEventListener('click', function() {
    document.getElementById('latitude').value = '';
    document.getElementById('longitude').value = '';
    const lokasiTextarea = document.getElementById('lokasiTextarea');
    if (lokasiTextarea) lokasiTextarea.value = '';
    document.getElementById('lokasiStatus').innerHTML = '<span class="text-muted">Lokasi telah direset. Silakan ambil lokasi baru.</span>';
});
   
const chronologyTextarea = document.querySelector('textarea[name="chronology"]');
const charInfo = document.getElementById('chronology-char-info');

chronologyTextarea.addEventListener('input', function() {
    const len = this.value.trim().length;
    if (len < 50) {
        charInfo.textContent = `Minimal 50 karakter (${len}/50)`;
        charInfo.classList.add('text-danger');
        charInfo.classList.remove('text-success');
    } else {
        charInfo.textContent = `Jumlah karakter mencukupi (${len} karakter)`;
        charInfo.classList.remove('text-danger');
        charInfo.classList.add('text-success');
    }
});
    // Category selection handling
    const kategoriSelect = document.getElementById('category');
    const kategoriLainnya = document.getElementById('kategoriLainnya');
    const otherCategoryInput = document.querySelector('[name="other_category"]');

    kategoriSelect.addEventListener('change', function() {
        if (this.value === 'Lainnya') {
            kategoriLainnya.style.display = 'block';
            otherCategoryInput.setAttribute('required', 'required');
        } else {
            kategoriLainnya.style.display = 'none';
            otherCategoryInput.removeAttribute('required');
            otherCategoryInput.value = '';
        }
    });

    // File upload handling
    const input = document.querySelector('input[name="evidence[]"]');
    const previewWrapper = document.querySelector('.image-preview-wrapper');
    const fileCount = document.querySelector('.file-count');

    input.addEventListener('change', function() {
        previewWrapper.innerHTML = '';
        fileCount.textContent = this.files.length + ' files';
        
        if (this.files.length > 3) {
            alert('Maksimal 3 foto yang dapat diunggah');
            this.value = '';
            fileCount.textContent = '0 files';
            return;
        }

        Array.from(this.files).forEach((file, index) => {
            if (!file.type.match('image.*')) {
                alert(`File ${file.name} bukan gambar`);
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                alert(`File ${file.name} melebihi 2MB`);
                return;
            }

            const reader = new FileReader();
            const preview = document.createElement('div');
            preview.className = 'preview-box';
            
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="preview-content">
                        <img src="${e.target.result}" class="img-preview" alt="Preview ${index + 1}">
                        <span class="preview-number">Foto ${index + 1}</span>
                        <button type="button" class="preview-close" onclick="removeFile(${index})">&times;</button>
                    </div>
                `;
            };
            
            reader.readAsDataURL(file);
            previewWrapper.appendChild(preview);
        });
    });

    document.getElementById('laporanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('nama_pelapor', document.getElementById('namaPelapor').value);
    formData.append('email_pelapor', document.getElementById('emailPelapor').value);
    formData.append('hp_pelapor', document.getElementById('hpPelapor').value);
    formData.append('alamat_pelapor', document.getElementById('alamatPelapor').value);
    const files = document.querySelector('input[name="evidence[]"]').files;
    
    // Validasi jumlah file
    if (files.length > 3) {
        Swal.fire({
            title: 'Error!',
            text: 'Maksimal 3 file yang dapat diunggah',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Validasi ukuran dan tipe file
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
            Swal.fire({
                title: 'Error!',
                text: 'File harus berupa JPG, JPEG, atau PNG',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        if (file.size > 2048000) { // 2MB = 2048000 bytes
            Swal.fire({
                title: 'Error!',
                text: 'Ukuran file maksimal 2MB',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
    }

    // Append files ke FormData
    Array.from(files).forEach((file, index) => {
        formData.append(`evidence[${index}]`, file);
    });

    // Tampilkan loading
    Swal.fire({
        title: 'Mengirim laporan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('<?= base_url('laporan-anon/submit') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: `${data.message}\nKode tracking: ${data.tracking_code}`,
                icon: 'success',
                confirmButtonText: 'OK'
            });
            // Reset form dan preview
            document.getElementById('laporanForm').reset();
            previewWrapper.innerHTML = '';
            fileCount.textContent = '0 files';
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengirim laporan',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

// File removal handler
function removeFile(index) {
    const input = document.querySelector('input[name="evidence[]"]');
    const previewWrapper = document.querySelector('.image-preview-wrapper');
    const fileCount = document.querySelector('.file-count');
    
    
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.splice(index, 1);
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
    
    previewWrapper.innerHTML = '';
    files.forEach((file, i) => {
        const preview = document.createElement('div');
        preview.className = 'preview-box';
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="preview-content">
                    <img src="${e.target.result}" class="img-preview" alt="Preview ${i + 1}">
                    <span class="preview-number">Foto ${i + 1}</span>
                    <button type="button" class="preview-close" onclick="removeFile(${i})">&times;</button>
                </div>
            `;
        };
        
        reader.readAsDataURL(file);
        previewWrapper.appendChild(preview);
    });
    
    fileCount.textContent = input.files.length + ' files';
}

// Add this to the existing script section
function updateWithAnimation(elementId, newValue) {
    const element = $(`#${elementId}`);
    const oldValue = parseInt(element.text());
    
    if (oldValue !== newValue) {
        element.addClass('highlight');
        element.text(newValue);
        
        setTimeout(() => {
            element.removeClass('highlight');
        }, 1000);
    }
}

function updateStatistics() {
    $.ajax({
        url: '<?= base_url('home/getStatistics') ?>',
        method: 'GET',
        success: function(response) {
            updateWithAnimation('totalReports', response.totalReports);
            updateWithAnimation('pendingReports', response.pendingReports);
            updateWithAnimation('verifiedReports', response.verifiedReports);
        }
    });
}

 // Mobile menu handling
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    document.addEventListener('click', (e) => {
        if (!navbarCollapse.contains(e.target) && !navbarToggler.contains(e.target)) {
            navbarCollapse.classList.remove('show');
        }
    });

    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navbarCollapse.classList.remove('show');
        });
    });

// Update every 5 seconds
$(document).ready(function() {
    setInterval(updateStatistics, 5000);
});
</script>
<script>

document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const q = document.getElementById('searchTrackingCode').value.trim();
    if (!q) {
        Swal.fire({
            title: 'Error!',
            text: 'Mohon masukkan kata kunci pencarian.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
    // Redirect ke halaman index laporan dengan parameter q
    window.location.href = '<?= base_url('/reports') ?>?q=' + encodeURIComponent(q);
});
</script>
<script>
// Multi-step wizard logic
const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const step3 = document.getElementById('step3');
const step1Badge = document.getElementById('step1-badge');
const step2Badge = document.getElementById('step2-badge');
const step3Badge = document.getElementById('step3-badge');

document.getElementById('setujuKetentuan').addEventListener('change', function() {
    document.getElementById('toStep2').disabled = !this.checked;
});

document.getElementById('toStep2').addEventListener('click', function() {
    step1.style.display = 'none';
    step2.style.display = 'block';
    step1Badge.classList.remove('bg-primary');
    step1Badge.classList.add('bg-secondary');
    step2Badge.classList.remove('bg-secondary');
    step2Badge.classList.add('bg-primary');
});

document.getElementById('backToStep1').addEventListener('click', function() {
    step2.style.display = 'none';
    step1.style.display = 'block';
    step2Badge.classList.remove('bg-primary');
    step2Badge.classList.add('bg-secondary');
    step1Badge.classList.remove('bg-secondary');
    step1Badge.classList.add('bg-primary');
});

document.getElementById('toStep3').addEventListener('click', function() {
    // Validasi data diri (opsional, bisa tambahkan validasi lebih detail)
    // Hanya nama yang wajib. Email dan No. HP bersifat opsional.
    if (!document.getElementById('namaPelapor').value.trim()) {
        alert('Mohon lengkapi nama Anda.');
        return;
    }
    step2.style.display = 'none';
    step3.style.display = 'block';
    step2Badge.classList.remove('bg-primary');
    step2Badge.classList.add('bg-secondary');
    step3Badge.classList.remove('bg-secondary');
    step3Badge.classList.add('bg-primary');
});
</script>
</script>
