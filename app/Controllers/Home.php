<?php
/**
 * ========================================
 * HOME CONTROLLER
 * ========================================
 * 
 * Menghandle halaman depan (landing page) aplikasi LaporDAH.
 * Fitur utama:
 * - Menampilkan halaman welcome dengan statistik laporan
 * - Menghitung dan menyimpan data pengunjung
 * - Menampilkan statistik real-time
 * 
 * @package App\Controllers
 * @author LaporDAH Team
 */

namespace App\Controllers;

use App\Models\VisitorModel;

class Home extends BaseController
{
    /**
     * index() - Halaman Utama / Landing Page
     * 
     * Menampilkan halaman welcome dengan:
     * - Total pengunjung (dari database)
     * - Statistik laporan (pending, verified, completed, dll)
     * - Tracking pengunjung berdasarkan IP & user agent
     * 
     * @return string View halaman welcome
     */
    public function index()
    {
        $visitorModel = new VisitorModel();

        // Periksa apakah pengunjung sudah dihitung menggunakan session
        // Session digunakan agar satu IP tidak dihitung berkali-kali dalam satu sesi
        if (!session()->has('visitor_counted')) {
            // Dapatkan IP dan user agent pengunjung
            $ipAddress = $this->request->getIPAddress();
            $userAgent = $this->request->getUserAgent();
            $visitedAt = date('Y-m-d H:i:s');

            // Simpan data pengunjung ke tabel visitor untuk tracking
            $visitorModel->insert([
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'visited_at' => $visitedAt,
            ]);

            // Tandai sesi sebagai "sudah dihitung" agar tidak dicount lagi
            session()->set('visitor_counted', true);
        }

        // Hitung total pengunjung unik dari database
        $totalVisitors = $visitorModel->getTotalVisitors();

        // Ambil statistik laporan (pending, verified, completed, dll)
        $stats = $this->getStatistics();
        
        // Render view welcome_message dengan data statistik
        return view('welcome_message', [
            'totalVisitors' => $totalVisitors,
            'totalReports' => $stats['totalReports'],
            'pendingReports' => $stats['pendingReports'], 
            'verifiedReports' => $stats['verifiedReports'],
            'completedReports' => $stats['completedReports'],
        ]);
    }

    /**
     * getStatistics() - Mengambil Statistik Laporan
     * 
     * Query database untuk mendapatkan jumlah laporan berdasarkan status:
     * - Total laporan (tidak termasuk archived/rejected)
     * - Laporan pending (belum diproses)
     * - Laporan verified (sudah diverifikasi/dalam progress)
     * - Laporan selesai (completed)
     * 
     * @return array|JSON Array statistik jika request biasa, JSON jika AJAX
     */
    public function getStatistics()
    {
        $db = \Config\Database::connect();
        
        // Count reports dari tabel 'reports' saja (fitur reports_user sudah dinonaktifkan)
        $stats = [
            'totalReports' => $db->table('reports')->whereNotIn('status', ['archived', 'rejected'])->countAllResults(),
            'pendingReports' => $db->table('reports')->where('status', 'pending')->countAllResults(),
            // Laporan dalam proses: disposition, verified, dan progress
            'verifiedReports' => $db->table('reports')->whereIn('status', ['disposition','verified', 'progress'])->countAllResults(),
            'completedReports' => $db->table('reports')->where('status', 'completed')->countAllResults(),
        ];
        
        // Jika request dari AJAX, return JSON, jika tidak return array biasa
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($stats);
        }
        
        return $stats;
    }


    /**
     * about() - Halaman Tentang LaporDAH
     * 
     * Menampilkan halaman informasi tentang aplikasi LaporDAH
     * 
     * @return string View halaman about
     */
    // Halaman Tentang LaporDAH
    public function about()
    {
        return view('about');
    }

    /**
     * faqUmum() - Halaman FAQ Umum
     * 
     * Menampilkan pertanyaan yang sering diajukan untuk pengunjung umum
     * 
     * @return string View halaman faqUmum
     */
    // Halaman FAQ Umum
    public function faqUmum()
    {
        return view('faqUmum');
    }

    /**
     * kebijakanPrivasi() - Halaman Kebijakan Privasi
     * 
     * Menampilkan kebijakan privasi dan penggunaan data aplikasi LaporDAH
     * 
     * @return string View halaman kebijakanPrivasi
     */
     // Halaman Kebijakan Privasi
    public function kebijakanPrivasi()
    {
        return view('kebijakanPrivasi');
    }
}
