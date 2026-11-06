<?php 
/**
 * ========================================
 * REPORT MODEL
 * ========================================
 * 
 * Model untuk mengelola data laporan pengaduan aplikasi LaporDAH.
 * 
 * Tabel: reports
 * Fitur:
 * - CRUD laporan (Create, Read, Update, Delete)
 * - Status tracking: pending, verified, disposition, progress, completed, archived, rejected
 * - Evidence management (bukti laporan hingga 3 file)
 * - Timestamp tracking (created_at, updated_at, verified_at)
 * - Soft delete (deleted_at)
 * 
 * @package App\Models
 * @author LaporDAH Team
 */

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    // ============ DATABASE CONFIGURATION ============
    protected $table = 'reports';               // Nama tabel di database
    protected $primaryKey = 'id';               // Primary key
    protected $returnType = 'array';            // Return type: array
    
    protected $dateFormat = 'datetime';         // Format date/time di database
    protected $deletedField  = 'deleted_at';    // Field untuk soft delete
    
    // Field yang boleh di-insert/update
    protected $allowedFields = [
        'tracking_code',        // Kode unik tracking laporan (format: LAPORDAN-xxx)
        'category',             // Kategori laporan (korupsi, gratifikasi, dll)
        'other_category',       // Kategori lain jika tidak ada di list
        'location',             // Lokasi kejadian
        'latitude',             // Koordinat latitude
        'longitude',            // Koordinat longitude
        'chronology',           // Kronologi/deskripsi kejadian
        'nama_pelapor',         // Nama pelapor
        'alamat_pelapor',       // Alamat pelapor
        'email_pelapor',        // Email pelapor
        'hp_pelapor',           // No. HP pelapor
        'evidence_1',           // File bukti 1 (nama file)
        'evidence_2',           // File bukti 2 (nama file)
        'evidence_3',           // File bukti 3 (nama file)
        'status',               // Status laporan (pending, verified, dll)
        'note',                 // Catatan/disposisi
        'verified_by',          // ID user yang memverifikasi
        'verified_at',          // Waktu verifikasi
    ];

    // ============ TIMESTAMPS ============
    protected $useTimestamps = true;
    protected $createdField = 'created_at';     // Created timestamp otomatis
    protected $updatedField = 'updated_at';     // Updated timestamp otomatis

    // ============ VALIDASI ============
    protected $validationRules = [
        'category' => 'required',               // Kategori wajib
        'location' => 'required',               // Lokasi wajib
        'chronology' => 'required',             // Kronologi wajib
        'deleted_by' => 'permit_empty|is_not_unique[users.id]'  // Penghapus harus user valid
    ];


    /**
     * insert() - Insert data laporan (dengan logging)
     * 
     * Override parent insert untuk debugging & logging
     * 
     * @param array $data Data laporan
     * @param bool $returnID Return ID atau tidak
     * @return int|bool ID jika returnID true, bool jika false
     */
    // Override insert untuk debugging
    public function insert($data = null, bool $returnID = true)
    {
        try {
            // Log info untuk debugging
            log_message('info', 'Attempting to insert data: ' . json_encode($data));
            $result = parent::insert($data, $returnID);
            log_message('info', 'Insert result: ' . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            // Log error jika ada exception
            log_message('error', 'Insert error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * getTotalReports() - Hitung Total Laporan
     * 
     * Menghitung jumlah total laporan di tabel reports
     * 
     * @return int Total laporan
     */
    // Tambahkan method untuk menghitung total laporan
    public function getTotalReports()
    {
        try {
            // Count all results dari query builder
            $total = $this->countAllResults();
            // Memastikan return integer, default 0 jika null
            return (int) $total ?: 0;
        } catch (\Exception $e) {
            log_message('error', 'Error getting total reports: ' . $e->getMessage());
            return 0; // Return 0 jika terjadi error
        }
    }

    /**
     * getTotal() - Hitung Total Laporan (Dengan Group Parameter)
     * 
     * Alias dari getTotalReports() untuk backward compatibility
     * 
     * @param string $group Group identifier (default: 'default')
     * @return int Total laporan
     */
    public function getTotal($group = 'default')
    {
        try {
            // Count all results
            $total = $this->countAllResults();
            return (int) $total ?: 0;
        } catch (\Exception $e) {
            log_message('error', 'Error in getTotal: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * getReportsByStatus() - Ambil Laporan Berdasarkan Status
     * 
     * Query laporan dengan filter status tertentu
     * 
     * @param string $status Status laporan (pending, verified, completed, dll)
     * @return array Array laporan dengan status yang sesuai
     */
    public function getReportsByStatus($status)
    {
        // Query laporan dengan status filter
        return $this->where('status', $status)->findAll();
    }

    /**
     * getReports() - Ambil Laporan Dengan Filter & Sorting
     * 
     * Query laporan dengan filtering:
     * - Category filter (all atau kategori spesifik)
     * - Sorting (newest atau oldest)
     * - Pagination dengan per page limit
     * 
     * @param string $category Category filter ('all' untuk semua)
     * @param string $sort Urutan ('newest' atau 'oldest')
     * @param int $perPage Jumlah per halaman
     * @return array Array laporan
     */
    public function getReports($category = 'all', $sort = 'newest', $perPage = 10)
    {
        $this->select('id, tracking_code, location, chronology, evidence_1, status, created_at');
        
        if ($category !== 'all') {
            $this->where('category', $category);
        }
        
        $this->orderBy('created_at', ($sort === 'oldest') ? 'ASC' : 'DESC');
        
        return $this->paginate($perPage);
    }

    public function getDashboardStatistics()
    {
        try {
            return [
                'total' => (int)$this->countAll(),
                'pending' => (int)$this->where('status', 'pending')->countAllResults(),
                'verified' => (int)$this->where('status', 'verified')->countAllResults(),
                'rejected' => (int)$this->where('status', 'rejected')->countAllResults(),
                'disposition' => (int)$this->where('status', 'disposition')->countAllResults()
            ];
        } catch (\Exception $e) {
            log_message('error', '[getDashboardStatistics] Error: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'verified' => 0,
                'rejected' => 0,
                'disposition' => 0,
            ];
        }
    }

   

   

    public function getMonthlyStats($months = 6)
    {
        return $this->select("DATE_FORMAT(created_at, '%M %Y') as month, COUNT(*) as total")
            ->where('status !=', 'archived')
            ->where('status !=', 'deleted')
            ->where('created_at >=', date('Y-m-01', strtotime('-' . ($months - 1) . ' months')))
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy("MIN(created_at)", 'ASC')
            ->findAll();
    }

    public function getFilteredReports($filters, $perPage = 10, $offset = 0, $isAnonymous = false) 
    {
        $this->select('*');
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $this->where('status', $filters['status']);
        }
        
        // Apply category filter
        if (!empty($filters['category'])) {
            $this->where('category', $filters['category']);
        }
        

        // Set order
        $this->orderBy('created_at', 'DESC');

        // Get paginated results
        return [
            'data' => $this->paginate($perPage, 'reports'),
            'pager' => $this->pager,
            'total' => $this->countAllResults()
        ];
    }
}
