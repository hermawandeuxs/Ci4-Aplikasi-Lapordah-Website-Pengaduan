<?php

/**
 * ========================================
 * BASE CONTROLLER
 * ========================================
 * 
 * Class induk (parent) untuk semua controller di aplikasi LaporDAH.
 * 
 * Fitur:
 * - Auto-load helpers & libraries
 * - Helper methods untuk stats & dashboard
 * - Sharing data ke semua controller
 * 
 * Semua controller harus extends class ini!
 * Contoh: class Home extends BaseController { ... }
 * 
 * @package App\Controllers
 * @author LaporDAH Team
 */

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     * Digunakan untuk mengakses HTTP request data
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Helpers yang auto-load untuk semua controller
     * 
     * Available helpers:
     * - 'url' : URL helper (base_url(), site_url(), dll)
     * - 'form' : Form helper (form_open, form_input, dll)
     * - 'report' : Custom report helper
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'report'];

    /**
     * Session instance
     * Digunakan untuk mengakses & manipulasi session data
     * 
     * @var \CodeIgniter\Session\Session
     */
     protected $session;

    /**
     * initController() - Initialize Controller
     * 
     * Method otomatis dipanggil saat controller di-instantiate.
     * Di sini kita load helpers, services, dll yang dibutuhkan semua controller
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload helpers untuk semua controller
        helper(['form', 'url', 'report']);

        // E.g.: $this->session = service('session');
    }

    /**
     * checkSession() - Debug Session Data
     * 
     * Helper method untuk debug & log session data
     * Biasanya digunakan saat development
     * 
     * @return array Session data
     */
    protected function checkSession()
    {
        $sessionData = [
            'logged_in' => session()->get('logged_in'),
            'user_id' => session()->get('user_id'),
            'username' => session()->get('username'),
            'role' => session()->get('role')
        ];

        log_message('debug', 'Current session: ' . json_encode($sessionData));
        return $sessionData;
    }

    /**
     * mapMonthlyStats() - Format Data Statistik Bulanan
     * 
     * Mengubah data statistik bulanan menjadi format untuk chart (labels & data)
     * Digunakan untuk membuat graph/chart di dashboard
     * 
     * @param array $monthlyStats Data statistik bulanan
     * @param int $months Jumlah bulan (default: 6)
     * @return array ['labels' => [...], 'data' => [...]]
     */
    protected function mapMonthlyStats(array $monthlyStats, int $months = 6)
    {
        $monthlyMap = [];
        // Build map dengan key "bulan" atau "month"
        foreach ($monthlyStats as $row) {
            $key = $row['bulan'] ?? $row['month'] ?? null;
            if ($key) {
                $monthlyMap[$key] = $row['total'];
            }
        }
        
        // Build chart labels & data untuk N bulan terakhir
        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = date('M Y', strtotime("-$i months"));
            $monthlyLabels[] = $month;
            $monthlyData[] = (int)($monthlyMap[$month] ?? 0);
        }
        
        return [
            'labels' => $monthlyLabels,
            'data' => $monthlyData
        ];
    }

    /**
     * mapCategoryStats() - Format Data Statistik Kategori
     * 
     * Mengubah data kategori menjadi format untuk chart
     * Top kategori ditampilkan, sisanya di-group ke "Lainnya"
     * 
     * @param array $categoryStatsRaw Data kategori raw
     * @param int $limit Limit kategori (default: 5)
     * @return array ['labels' => [...], 'data' => [...]]
     */
    // Helper untuk mapping kategori terpopuler
    protected function mapCategoryStats(array $categoryStatsRaw, int $limit = 5)
    {
        $categoryStats = [];
        $lainnyaTotal = 0;
        
        // Build kategori stats, group "Lainnya" terpisah
        foreach ($categoryStatsRaw as $cat) {
            if (empty($cat['category']) && !empty($cat['other_category'])) {
                $lainnyaTotal += $cat['total'];
            } else {
                $label = !empty($cat['category']) ? $cat['category'] : '-';
                if (!isset($categoryStats[$label])) {
                    $categoryStats[$label] = [
                        'category' => $label,
                        'total' => 0
                    ];
                }
                $categoryStats[$label]['total'] += $cat['total'];
            }
        }
        
        // Tambah "Lainnya" ke kategori stats
        if ($lainnyaTotal > 0) {
            $categoryStats['Lainnya'] = [
                'category' => 'Lainnya',
                'total' => $lainnyaTotal
            ];
        }
        
        // Sort descending by total
        usort($categoryStats, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        // Limit ke N kategori top
        $categoryStats = array_slice($categoryStats, 0, $limit);

        return [
            'labels' => array_column($categoryStats, 'category'),
            'data' => array_map('intval', array_column($categoryStats, 'total'))
        ];
    }

    /**
     * getSidebarNotif() - Get Sidebar Notification Data
     * 
     * Mengambil data untuk sidebar notification:
     * - Count semua status laporan
     * - Count laporan hari ini
     * 
     * Digunakan untuk menampilkan badge/counter di sidebar
     * 
     * @return array Notification counts
     */
    protected function getSidebarNotif()
    {
        $reportModel = new \App\Models\ReportModel();
        $today = date('Y-m-d');

        return [
            // Total semua status (tanpa filter tanggal)
            'countReports' => $reportModel->whereIn('status', ['pending', 'disposition', 'verified', 'progress'])->countAllResults(),
            'countRejected' => $reportModel->where('status', 'rejected')->countAllResults(),
            'countArsip' => $reportModel->where('status', 'archived')->countAllResults(),

            // Hanya laporan hari ini
            'countReportsToday' => $reportModel
                ->whereIn('status', ['pending', 'disposition', 'verified', 'progress'])
                ->where('DATE(created_at)', $today)
                ->countAllResults(),
            'countRejectedToday' => $reportModel
                ->where('status', 'rejected')
                ->where('DATE(created_at)', $today)
                ->countAllResults(),
            'countArsipToday' => $reportModel
                ->where('status', 'archived')
                ->where('DATE(created_at)', $today)
                ->countAllResults(),
        ];
    }

}
