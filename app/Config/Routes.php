<?php

/**
 * ========================================
 * ROUTES CONFIGURATION - LaporDAH
 * ========================================
 * 
 * File ini mendefinisikan semua route aplikasi LaporDAH:
 * 
 * ROUTE GROUPS:
 * 
 * 1. PUBLIC ROUTES (Tidak perlu login)
 *    - Landing page (GET /)
 *    - Laporan anonim (tanpa login)
 *    - FAQ, About, Kebijakan Privasi
 *    - Authentication (login, register, forgot/reset password)
 * 
 * 2. USER ROUTES (Role: user)
 *    - Dashboard user
 *    - Profil & edit profil
 *    - Upload foto
 *    - Ubah password
 * 
 * 3. ADMIN ROUTES (Role: admin)
 *    - Dashboard admin
 *    - Manajemen laporan (list, detail, archive, delete)
 *    - Manajemen user (tambah, edit, hapus, reset password)
 *    - Laporan & statistik
 *    - Export & print
 * 
 * 4. ADMIN BIDANG ROUTES (Role: admin_bidang)
 *    - Dashboard admin bidang
 *    - Verifikasi laporan
 *    - Update progress & status
 *    - Arsip laporan
 *    - Rekap & print
 * 
 * FILTER SECURITY:
 * - role:user / role:admin / role:admin_bidang = Role-based access control
 * - [Requires valid session to access protected routes]
 * 
 * @package App\Config
 * @author LaporDAH Team
 */

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================
// PUBLIC ROUTES (No Auth Required)
// =====================
// Halaman publik yang bisa diakses tanpa login
$routes->GET('/', 'Home::index'); // Landing page - menampilkan statistik & info umum
$routes->GET('home/getStatistics', 'Home::getStatistics'); // API untuk statistik (bisa via AJAX)
$routes->GET('reports/search', 'Reports::search'); // Pencarian laporan publik
$routes->GET('reports/check', 'Reports::check'); // Check status laporan publik

// =====================
// FILE/UPLOAD ROUTES (Public)
// =====================
// Route untuk menampilkan file bukti laporan (gambar)
$routes->GET('uploads/reports/(:any)', 'LaporanAnon::showImage/$1');
// Catatan: Route untuk uploads/reports_user sudah dihapus (fitur disabled)

// =====================
// LAPORAN ANONIM ROUTES (Public - No Login Required)
// =====================
// Route untuk mengajukan laporan tanpa perlu login
$routes->MATCH(['GET', 'POST'], 'laporan-anon/submit', 'LaporanAnon::submit'); // Form & proses submit laporan anonim
$routes->GET('laporan-anon/check-status', 'LaporanAnon::checkStatus');         // Cek status laporan anonim
$routes->GET('laporan-anon/show-image/(:any)', 'LaporanAnon::showImage/$1');   // Tampilkan gambar bukti laporan

// =====================
// PROTECTED ROUTES (Auth Required)
// =====================
$routes->group('/', function($routes) {

    // ---------------------
    // USER ROUTES (User Biasa)
    // ---------------------
    $routes->group('user', ['filter' => 'role:user'], function($routes) {
        $routes->GET('dashboard', 'User::dashboard'); // Dashboard user
        $routes->GET('laporan', 'User::laporan'); // Daftar laporan (user-feature limited)
        $routes->GET('laporan/(:num)', 'User::detail/$1');
        // Removed LaporanUser routes: create/submit/detail/edit/delete (reports_user disabled)
        $routes->GET('profile', 'User::profile'); // Profil user
        $routes->GET('edit_profil', 'User::editProfile'); // Form edit profil user
        $routes->POST('profile/update', 'User::updateProfile'); // Update profil user
        $routes->GET('faq','User::faq'); // Halaman FAQ/Panduan
        $routes->GET('logout', 'Auth::logout'); // Logout user
        $routes->GET('password', 'User::edit_password'); // Form ubah password user
        $routes->POST('password', 'User::update_password');// Proses ubah password user
        $routes->POST('upload_foto', 'User::upload_foto');// Proses upload foto profil user
    });

    // ---------------------
    // ADMIN ROUTES
    // ---------------------
    $routes->group('admin', ['filter' => 'role:admin'], function($routes) {
        // Dashboard & laporan utama
        $routes->GET('/', 'Admin::index'); // Dashboard admin
        $routes->GET('report', 'Admin::listLaporan'); // Daftar laporan anonim
        // report/user routes removed (user-report feature disabled)
        $routes->GET('report/(:num)', 'Admin::detailAnon/$1'); // Detail laporan (umum)
        $routes->POST('delete/(:num)', 'Admin::delete/$1'); // Hapus laporan anonim
        $routes->GET('reports', 'Admin::reports'); // Daftar laporan anonim
        $routes->GET('reports/get-data', 'Admin::getReports'); // AJAX data laporan anonim
        $routes->POST('reports/archive/(:num)', 'Admin::archive/$1'); // Arsipkan laporan anonim (duplikat, bisa dirapikan)
        $routes->GET('history-laporan', 'Admin::historyLaporan'); // Histori perubahan status laporan
        $routes->POST('archive-bulk', 'Admin::archive_bulk'); // Arsipkan laporan admin bidang secara massal
        $routes->GET('rekap-laporan', 'Admin::rekapLaporan'); // Rekap laporan admin
        $routes->get('export-arsip-word', 'Admin::exportArsipWord');
        // Laporan Reject
        $routes->GET('laporan-reject', 'Admin::laporanRejectGabungan'); // Daftar laporan gabungan reject

        //laporan print
        $routes->GET('print-pdf/(:num)', 'Admin::printPdf/$1'); // Print laporan
        $routes->GET('print-all-arsip', 'Admin::printAllArsip'); // Print semua arsip laporan
        $routes->GET('rekap/export', 'Admin::exportRekapLaporan'); // Print semua laporan rekap

        
        // Arsip
        $routes->GET('report-arsip', 'Admin::reportArsipGabungan');
        $routes->POST('archive/(:any)', 'Admin::archive/$1'); // Arsipkan laporan semua
    

        // Manajemen User (Admin & Admin Bidang)
        $routes->GET('manajemen-user', 'Admin::manajemenUser'); // Daftar user
        $routes->GET('manajemen-user/tambah', 'Admin::tambahUser'); // Form tambah user (GET)
        $routes->POST('manajemen-user/tambah', 'Admin::tambahUser'); // Proses tambah user (POST)
        $routes->GET('manajemen-user/edit/(:num)', 'Admin::editUser/$1'); // Form edit user (GET)
        $routes->POST('manajemen-user/edit/(:num)', 'Admin::editUser/$1'); // Proses edit user (POST)
        $routes->POST('manajemen-user/hapus/(:num)', 'Admin::hapusUser/$1'); // Hapus user (POST)
        $routes->POST('manajemen-user/reset-password/(:num)', 'Admin::resetPassword/$1'); // Reset password (POST)


    // Disposisi
        $routes->POST('disposisi/(:segment)/(:num)', 'Admin::disposisi/$1/$2');
    });

    // ---------------------
    // ADMIN BIDANG ROUTES
    // ---------------------
    $routes->group('admin-bidang',['filter' => 'role:admin_bidang'], function($routes) {
        $routes->GET('/', 'AdminBidang::dashboard'); // Dashboard admin bidang
        $routes->GET('laporan', 'AdminBidang::listLaporan'); // <-- ini untuk daftar laporan
        $routes->GET('laporan/(:num)', 'AdminBidang::detailAnonim/$1'); // Detail laporan untuk admin bidang
        // laporan/user route removed (user-report feature disabled)
        $routes->get('laporan-reject', 'AdminBidang::laporanRejectGabungan');
        $routes->POST('verify/(:segment)/(:num)', 'AdminBidang::verify/$1/$2');
        $routes->POST('delete/(:num)', 'AdminBidang::delete/$1'); // Hapus laporan anonim
        $routes->POST('update_progress/(:num)', 'AdminBidang::update_progress/$1');// Update progress laporan
        $routes->POST('update_completed/(:num)', 'AdminBidang::update_completed/$1'); // Update status laporan selesai
        $routes->get('report-arsip', 'AdminBidang::reportArsipGabungan'); // Daftar laporan arsip admin bidang
        $routes->POST('archive/(:any)', 'AdminBidang::archive/$1'); // Arsipkan laporan admin bidang
        //$routes->POST('unarchive', 'AdminBidang::unarchiveUniversal'); // Kembalikan laporan dari arsip admin bidang
        $routes->POST('archive-bulk', 'AdminBidang::archive_bulk'); // Arsipkan laporan admin bidang secara massal
        $routes->GET('rekap-laporan', 'AdminBidang::rekapLaporan'); // Rekap laporan admin bidang
        $routes->GET('print-pdf/(:num)', 'AdminBidang::printPdf/$1'); // Print laporan admin bidang
    });

    $routes->get('login', 'Auth::login');
    $routes->POST('auth/attemptLogin', 'Auth::attemptLogin');
    $routes->GET('logout', 'Auth::logout');
});

// ---------------------
// REPORTS ROUTES (Public/Umum?)
// ---------------------
$routes->group('reports', function($routes) {
    $routes->GET('/', 'Reports::index');
    $routes->GET('(:num)', 'Reports::index/$1');
    $routes->GET('search', 'Reports::search');
    $routes->GET('detail/(:num)', 'Reports::detail/$1'); // Detail laporan umum
    // Untuk file proses/status (akses file langsung, jika perlu route khusus download)
    $routes->GET('uploads/status_log/(:any)', 'File::statusLog/$1');
});

// ---------------------
// UNIVERSAL ROUTES (Umum, tidak perlu login)
// ---------------------
$routes->GET('faqUmum', 'Home::faqUmum');// Halaman FAQ Umum
$routes->GET('about', 'Home::about'); // Halaman tentang LaporDAH
$routes->GET('kebijakanPrivasi','Home::kebijakanPrivasi');//Halaman Kebjakan privasi


// =====================
// AUTH ROUTES (Login Only)
// =====================
// Route login & password reset (umum - public)
$routes->GET('login', 'Auth::login');
$routes->POST('auth/attemptLogin', 'Auth::attemptLogin');
// DISABLED: Register routes (hanya admin yang bisa tambah user via manajemen-user)
// $routes->GET('register', 'Auth::register');
// $routes->POST('register/process', 'Auth::processRegister');
$routes->get('auth/forgot', 'Auth::forgot');           // Tampilkan form lupa password
$routes->post('auth/forgot', 'Auth::forgot');          // Proses kirim email reset password
$routes->get('auth/reset', 'Auth::reset');             // Tampilkan form reset password (via token)
$routes->post('auth/reset', 'Auth::reset');            // Proses reset password (via token)






