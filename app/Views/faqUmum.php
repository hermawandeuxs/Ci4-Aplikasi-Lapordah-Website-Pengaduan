<?= $this->include('layouts/header') ?>

<body>
<div class="container py-4" style="margin-top: 80px;">
    <div class="card shadow-lg border-0 w-100 my-4">
        <div class="card-header bg-primary text-white rounded-top">
            <h5 class="mb-0"><i class="bi bi-question-circle me-2"></i> Panduan & FAQ LaporDAH</h5>
        </div>
        <div class="card-body bg-light">

            <!-- Area FAQ scrollable -->
            <div style="max-height: 400px; overflow-y: auto;">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                Bagaimana cara membuat laporan pengaduan?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Klik menu <b>Buat Aduan</b> di atas kemudian menyetujui ketentuan pengiriman laporan, isi form data diri, dan isi form pengaduan dengan lengkap, lampirkan bukti jika ada, lalu klik <b>Kirim</b>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                Apakah identitas saya aman dan dirahasiakan?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, identitas pelapor dijamin kerahasiaannya dan hanya dapat diakses oleh admin utama. Anda juga dapat memilih untuk mengirim laporan secara anonim.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                Bagaimana cara memantau status laporan saya?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Masuk ke menu <b>Jelajah Laporan</b> atau login sebagai user untuk melihat daftar dan status laporan yang pernah Anda buat. Anda juga dapat melihat detail dan riwayat status setiap laporan.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                Apa saja bukti yang dapat dilampirkan?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Anda dapat melampirkan maksimal 3 file gambar (JPG/PNG, maksimal 2MB per file) sebagai bukti pendukung laporan.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                Bagaimana jika saya lupa tracking code laporan?
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Jika Anda membuat laporan sebagai user terdaftar, Anda dapat melihat semua tracking code di menu <b>Jelajah Laporan</b>. Jika Anda membuat laporan anonim, tracking code hanya diberikan sekali setelah laporan dikirim.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq6">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                Siapa saja yang dapat melihat laporan saya?
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="faq6" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Laporan hanya dapat diakses oleh admin dan admin bidang terkait. Identitas pelapor tidak akan dibagikan tanpa persetujuan Anda.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- akhir pembungkus scroll -->

            <div class="mt-4">
                <h6>Bantuan Lainnya</h6>
                <p>Jika Anda mengalami kendala, silakan hubungi admin melalui email <a href="mailto:admin@lapordah.com">admin@lapordah.com</a> atau gunakan menu <b>Kontak</b> jika tersedia.</p>
            </div>
        </div>
    </div>
</div>
</body>
<!-- Bootstrap Bundle JS (sudah termasuk Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?= $this->include('layouts/footer') ?>