<?php
namespace App\Controllers;
use Dompdf\Dompdf;
use Dompdf\Options;
class Admin extends BaseController
{
    protected $reportModel;
    protected $visitorModel;
    protected $userModel;
    protected $statusLogModel; 
    // removed reportUserModel usage to avoid dependencies on reports_user table

public function __construct()
{
    $this->reportModel = new \App\Models\ReportModel();
    $this->visitorModel = new \App\Models\VisitorModel();
    $this->userModel = new \App\Models\UserModel();
    $this->statusLogModel = new \App\Models\ReportStatusLogModel();
}

    /**
     * Default admin landing — redirect to report list.
     */
    public function index()
    {
        try {
            $db = \Config\Database::connect();
            $userModel = new \App\Models\UserModel();

            // Visitor count
            $visitorCount = $this->getVisitorCount();

            // Overall stats and monthly stats
            $stats = $this->reportModel->getDashboardStatistics();
            $monthlyStats = $this->reportModel->getMonthlyStats(6);

            // Ambil daftar tahun yang tersedia
            $years = $this->getAvailableYears();
            $currentYear = date('Y');

            // Top categories (last 6 months)
            $date6bulan = date('Y-m-01', strtotime('-5 months'));
            $sqlCategory = "
                SELECT category, SUM(jumlah) as jumlah FROM (
                    SELECT TRIM(category) as category, COUNT(*) as jumlah
                    FROM reports
                    WHERE created_at >= ? AND TRIM(category) != '' AND category IS NOT NULL
                    GROUP BY category
                    UNION ALL
                    SELECT 'Lainnya' as category, COUNT(*) as jumlah
                    FROM reports
                    WHERE created_at >= ? AND TRIM(other_category) != '' AND other_category IS NOT NULL
                ) AS all_reports
                GROUP BY category
                ORDER BY jumlah DESC
                LIMIT 10
            ";
            $topCategories = $db->query($sqlCategory, [$date6bulan, $date6bulan])->getResultArray();

            // Kebidangan list (from users)
            $kebidanganRows = $userModel->select('kebidangan')->distinct()->where('kebidangan IS NOT NULL')->where('kebidangan !=', '')->findAll();
            $kebidanganList = array_column($kebidanganRows, 'kebidangan');
            if (!in_array('Sekretariat', $kebidanganList)) {
                $kebidanganList[] = 'Sekretariat';
            }

            // Build bidangStats: status per bidang, pastikan arsip (archived) tetap terhitung meski note tidak mengandung bidang
            $bidangStats = [];
            foreach ($kebidanganList as $kb) {
                $like = "%$kb%";
                $stat = ['disposition' => 0, 'verified' => 0, 'rejected' => 0, 'archived' => 0, 'progress' => 0, 'done' => 0, 'total' => 0];
                // 1. Hitung semua status (kecuali archived dan deleted) berdasarkan note LIKE bidang
                $sql = "SELECT status, COUNT(*) as jumlah FROM reports WHERE note LIKE ? AND status != 'archived' AND status != 'deleted' GROUP BY status";
                $rows = $db->query($sql, [$like])->getResultArray();
                foreach ($rows as $row) {
                    $status = strtolower($row['status']);
                    $stat['total'] += $row['jumlah'];
                    if ($status === 'completed') $status = 'done';
                    if (isset($stat[$status])) {
                        $stat[$status] = $row['jumlah'];
                    }
                }
                // 2. Hitung laporan archived untuk bidang ini (berdasarkan note LIKE bidang, exclude deleted)
                $sqlArsip = "SELECT COUNT(*) as jumlah FROM reports WHERE status = 'archived' AND note LIKE ? AND status != 'deleted'";
                $archivedCount = $db->query($sqlArsip, [$like])->getRowArray();
                $stat['archived'] = (int)($archivedCount['jumlah'] ?? 0);
                $stat['total'] += $stat['archived'];
                $bidangStats[$kb] = $stat;
            }

            $data = [
                'title' => 'Dashboard',
                'visitorCount' => $visitorCount,
                'bidangStats' => $bidangStats,
                'topCategories' => $topCategories,
                'monthlyStats' => $monthlyStats,
                'stats' => $stats,
                'userRole' => session('role') ?? 'admin',
                'userName' => session('nama_user') ?? session('username') ?? 'Admin',
                'kebidangan' => $kebidanganList,
                'years' => $years,
                'currentYear' => $currentYear,
                'layout' => 'universal/layout'
            ];

            return view('universal/dashboard', $data);
        } catch (\Exception $e) {
            log_message('error', '[Dashboard Error] ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memuat dashboard');
            return redirect()->back();
        }
    }

/**
 * API untuk mengambil data laporan (biasanya untuk AJAX/datatable), mendukung pencarian dan filter.
 */
public function getReports()
{
    $status = $this->request->getGet('status');
    $category = $this->request->getGet('category');
    $sort = $this->request->getGet('sort') ?? 'newest'; // Tambahkan sort

    $builder = $this->reportModel->builder();
    $builder->where('status !=', 'deleted')
            ->where('status !=', 'archived')
            ->where('deleted_at IS NULL');

    // Apply individual filters
    if ($status && $status !== 'all') {
        $builder->where('status', $status);
    }

    if ($category && $category !== 'all') {
        $builder->where('category', $category);
    }

    // Add sorting
    $order = (strtolower($sort) === 'oldest') ? 'ASC' : 'DESC';
    $builder->orderBy('created_at', $order);
    
    $reports = $builder->get()->getResultArray();

    // Process the reports to add additional information
    foreach ($reports as &$report) {
        $report['created_at_formatted'] = date('d M Y H:i', strtotime($report['created_at']));
        $report['status_badge'] = $this->getStatusBadge($report['status']);
    }

    return $this->response->setJSON([
        'success' => true,
        'data' => $reports
    ]);
}

/**
 * Helper untuk mengembalikan HTML badge status laporan.
 */
private function getStatusBadge($status)
{
    $badges = [
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'verified' => '<span class="badge bg-success">Verified</span>',
        'rejected' => '<span class="badge bg-danger">Rejected</span>',
        'archived' => '<span class="badge bg-secondary">Archived</span>',
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

/**
 * Helper untuk mendapatkan path folder bukti laporan di server.
 */
protected function getEvidencePath()
{
    return FCPATH . 'uploads/reports/';
}
    

    

/**
 * Menampilkan detail laporan (mirip dengan detail, kemungkinan untuk tampilan berbeda).
 */

public function detailAnon($id)
{
    $this->statusLogModel = new \App\Models\ReportStatusLogModel();

    $report = $this->reportModel->find($id);
    if (!$report) {
        return redirect()->back()->with('error', 'Laporan anonim tidak ditemukan');
    }
    $report['sumber'] = 'anonim';

    // Evidence
    $evidence_urls = [];
    for ($i = 1; $i <= 3; $i++) {
        $field = 'evidence_' . $i;
        if (!empty($report[$field])) {
            $file_path = FCPATH . 'uploads/reports/' . $report[$field];
            if (file_exists($file_path)) {
                $evidence_urls[$field] = base_url('uploads/reports/' . $report[$field]);
            }
        }
    }

    $statusLogs = $this->statusLogModel
        ->where('report_id', $id)
        ->orderBy('updated_at', 'ASC')
        ->findAll();

    $data = [
        'title' => 'Detail Laporan Anonim',
        'report' => $report,
        'evidence_urls' => $evidence_urls,
        'userRole' => session('role') ?? 'admin',
        'layout' => 'universal/layout',
        'statusLogs' => $statusLogs,
    ];

    return view('universal/report_detail', $data);
}


/**
 * Soft delete laporan (ubah status jadi deleted, isi kolom deleted_at, dsb).
 * Jika laporan anonim, juga menghapus data sensitif dan file bukti.
 */
public function delete($id) 
{
    try {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Cek di laporan anonim
        $report = $this->reportModel->find($id);
        if ($report) {
            $username = session('username') ?? 'admin';

            // Jika laporan sudah tertolak dapat melakukan soft-delete dan log
            if (strtolower($report['status']) === 'rejected') {
                // Hapus file bukti terlebih dahulu
                $this->deleteEvidenceFiles($report);

                $this->reportModel->update($id, [
                    'status' => 'deleted',
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'deleted_by' => $username
                ]);

                $this->statusLogModel->insert([
                    'report_id'     => $id,
                    'tracking_code' => $report['tracking_code'] ?? '',
                    'report_source' => 'laporan-umum',
                    'status'        => 'deleted',
                    'note'          => '(Dihapus)',
                    'file'          => null,
                    'updated_by'    => $username,
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'kebidangan'    => $report['kebidangan'] ?? null
                ]);

                $db->transComplete();
                if ($db->transStatus() === false) {
                    throw new \Exception('Transaksi database gagal');
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Laporan berhasil dihapus (soft delete)'
                ]);
            }

            // Jika bukan rejected -> lakukan hard delete seperti sebelumnya
            $this->deleteEvidenceFiles($report);
            if (!$this->reportModel->delete($id)) {
                throw new \Exception('Gagal menghapus laporan anonim');
            }
            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Laporan anonim berhasil dihapus permanen'
            ]);
        }

        // 2. Jika tidak ditemukan di laporan anonim -> hentikan dan laporkan
        throw new \Exception('Laporan tidak ditemukan di tabel reports');

    } catch (\Exception $e) {
        if (isset($db)) {
            $db->transRollback();
        }
        log_message('error', '[Delete Error] ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus laporan: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}


/**
 * Helper untuk menghapus file bukti laporan dari server berdasarkan data laporan.
 */
private function deleteEvidenceFiles($report)
{
    $evidencePath = FCPATH . 'uploads/reports/';
    
    for ($i = 1; $i <= 3; $i++) {
        $field = "evidence_" . $i;
        if (!empty($report[$field])) {
            $filePath = $evidencePath . $report[$field];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}

/**
 * Mengarsipkan laporan hanya jika status complete.
 */
public function archive($id)
{
    $userRole = session('role') ?? 'admin';
    // Cek di laporan anonim
    $report = $this->reportModel->find($id);
    if ($report) {
        if (strtolower($report['status']) !== 'completed') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Laporan harus komplit dahulu'
            ]);
        }
        $this->reportModel->update($id, [
            'status' => 'archived',
            'note' => 'Diarsipkan oleh ' . $userRole . ' pada ' . date('d/m/Y H:i') . '. Status sebelumnya: ' . $report['status']
        ]);
        $this->statusLogModel->insert([
            'report_id'     => $id,
            'tracking_code' => $report['tracking_code'] ?? '',
            'report_source' => 'laporan-umum',
            'status'        => 'archived',
            'note'          => 'Diarsipkan oleh ' . $userRole . ' pada ' . date('d/m/Y H:i') . '. Status sebelumnya: ' . $report['status'],
            'file'          => null,
            'updated_by'    => session('username') ?? $userRole,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);
        return $this->response->setJSON(['success' => true, 'message' => 'Laporan berhasil diarsipkan.']);
    }

    // Only reports table is supported; user-report-specific operations removed.
    return $this->response->setJSON(['success' => false, 'message' => 'Operasi hanya mendukung tabel reports.']);
}

/**
 * Menampilkan daftar laporan yang diarsipkan.
 */
public function reportArsip()
{
    $filters = [
        'status' => 'archived',
        'category' => $this->request->getGet('category'),
        'sort' => $this->request->getGet('sort') ?? 'newest',
        'year' => $this->request->getGet('year') ?? date('Y')
    ];

    $perPage = 10;
    $builder = $this->reportModel->where('status', 'archived');

    // Filter kategori
    if (!empty($filters['category']) && $filters['category'] !== 'all') {
        if ($filters['category'] === 'Lainnya') {
            $builder = $builder->where('other_category IS NOT NULL')->where('other_category !=', '');
        } else {
            $builder = $builder->where('category', $filters['category']);
        }
    }

    // Filter tahun
    if (!empty($filters['year']) && $filters['year'] !== 'all') {
        $builder = $builder->where('YEAR(created_at)', $filters['year']);
    }

    // Hitung total laporan archived sesuai filter
    $total = $builder->countAllResults(false);

    // Urutan
    $order = ($filters['sort'] === 'oldest') ? 'ASC' : 'DESC';
    $reports = $builder
        ->orderBy('created_at', $order)
        ->paginate($perPage, 'reports');
    $pager = $this->reportModel->pager;

    // Ambil kategori utama
    $categories = $this->reportModel->distinct()->select('category')->where('category IS NOT NULL')->findAll();

    // Ambil daftar other_category unik yang tidak kosong/null
    $otherCategories = $this->reportModel
        ->distinct()
        ->select('other_category')
        ->where('other_category IS NOT NULL')
        ->where('other_category !=', '')
        ->findAll();

    // Ambil daftar tahun untuk filter
    $years = $this->reportModel->distinct()
        ->select('YEAR(created_at) as year')
        ->where('status', 'archived')
        ->orderBy('year', 'DESC')
        ->findAll();

    return view('universal/dashboard', [
        'reports' => $reports,
        'pager' => $pager,
        'categories' => $categories,
        'otherCategories' => $otherCategories,
        'years' => $years,
        'filters' => $filters
    ]);
}

    

/**
 * Helper untuk menghitung total pengunjung dari model visitor.
 */
private function getVisitorCount()
{
    return $this->visitorModel->countAll();
}

public function laporanRejectGabungan()
{
    $perPage = 10;
    $filters = [
        'category' => $this->request->getGet('category'),
        'sort' => $this->request->getGet('sort') ?? 'newest'
    ];
    $builderAnon = $this->reportModel->select("id, tracking_code, NULL as username, status, category, other_category, location, chronology, created_at, 'anonim' as sumber")->where('status', 'rejected');
    if (!empty($filters['category']) && $filters['category'] !== 'all') {
        if ($filters['category'] === 'Lainnya') {
            $builderAnon->where('other_category IS NOT NULL')->where('other_category !=', '');
        } else {
            $builderAnon->where('category', $filters['category']);
        }
    }
    $reports = $builderAnon->findAll();
    $order = ($filters['sort'] === 'oldest') ? 'ASC' : 'DESC';
    usort($reports, function($a, $b) use ($order) {
        return $order === 'ASC'
            ? strtotime($a['created_at']) <=> strtotime($b['created_at'])
            : strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });
    $total = count($reports);
    $currentPage = (int)($this->request->getGet('page_reports') ?? 1);
    $offset = ($currentPage - 1) * $perPage;
    $reports = array_slice($reports, $offset, $perPage);
    $pager = \Config\Services::pager();
    $pager->makeLinks($currentPage, $perPage, $total, 'default_full', 2);
    $categories = $this->reportModel->distinct()->select('category')->where('category IS NOT NULL')->findAll();
    $otherCategories = $this->reportModel->distinct()->select('other_category')->where('other_category IS NOT NULL')->where('other_category !=', '')->findAll();
    $totalPages = ceil($total / $perPage);
    return view('universal/laporan_reject', [
        'title' => 'Laporan Tertolak',
        'reports' => $reports,
        'pager' => $pager,
        'filters' => $filters,
        'categories' => $categories,
        'otherCategories' => $otherCategories,
        'total' => $total,
        'currentPage' => $currentPage,
        'perPage' => $perPage,
        'totalPages' => $totalPages,
        'userRole' => 'admin',
        'layout' => 'universal/layout',
    ]);
}

public function reportArsipGabungan()
{
    $filters = [
        'category' => $this->request->getGet('category'),
        'sort' => $this->request->getGet('sort') ?? 'newest',
        'year' => $this->request->getGet('year') ?? 'all'
    ];
    $perPage = 10;
    $builderAnon = $this->reportModel->select("id, tracking_code, NULL as username, status, category, other_category, location, chronology, created_at, 'anonim' as sumber, NULL as id_pengaduan, nama_pelapor")->where('status', 'archived');
    if (!empty($filters['category']) && $filters['category'] !== 'all') {
        if ($filters['category'] === 'Lainnya') {
            $builderAnon->where('other_category IS NOT NULL')->where('other_category !=', '');
        } else {
            $builderAnon->where('category', $filters['category']);
        }
    }
    if (!empty($filters['year']) && $filters['year'] !== 'all') {
        $builderAnon->where('YEAR(created_at)', $filters['year']);
    }
    $reports = $builderAnon->findAll();
    $order = ($filters['sort'] === 'oldest') ? 'ASC' : 'DESC';
    usort($reports, function($a, $b) use ($order) {
        return $order === 'ASC'
            ? strtotime($a['created_at']) <=> strtotime($b['created_at'])
            : strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });
    $total = count($reports);
    $currentPage = (int)($this->request->getGet('page_reports') ?? 1);
    $offset = ($currentPage - 1) * $perPage;
    $reports = array_slice($reports, $offset, $perPage);
    $pager = \Config\Services::pager();
    $pager->makeLinks($currentPage, $perPage, $total, 'default_full', 2);
    $categories = $this->reportModel->distinct()->select('category')->where('category IS NOT NULL')->findAll();
    $otherCategories = $this->reportModel->distinct()->select('other_category')->where('other_category IS NOT NULL')->where('other_category !=', '')->findAll();
    $yearsAnon = $this->reportModel->distinct()->select('YEAR(created_at) as year')->where('status', 'archived')->findAll();
    $years = array_unique(array_column($yearsAnon, 'year'));
    rsort($years);
    $totalPages = ceil($total / $perPage);
    return view('universal/report_arsip', [
        'title' => 'Arsip Laporan',
        'reports' => $reports,
        'pager' => $pager,
        'filters' => $filters,
        'categories' => $categories,
        'otherCategories' => $otherCategories,
        'years' => $years,
        'total' => $total,
        'currentPage' => $currentPage,
        'perPage' => $perPage,
        'totalPages' => $totalPages,
        'userRole' => 'admin',
        'layout' => 'universal/layout',
    ]);
}

/**
 * Menampilkan daftar laporan yang masuk.
 */
public function reports()
{
    $perPage = (int)($this->request->getGet('perPage') ?? 10);
    $filters = [
        'status' => $this->request->getGet('status') ?? '',
        'category' => $this->request->getGet('category') ?? 'all',
        'sort' => $this->request->getGet('sort') ?? 'newest'
    ];

    $statusList = ['verified', 'pending', 'disposition','progress','completed'];

    // --- Ambil dari tabel reports (anonim) ---
    $builderAnon = $this->reportModel
        ->select("id, tracking_code, status, category, other_category, location, latitude, longitude, chronology, created_at, nama_pelapor, alamat_pelapor, email_pelapor, hp_pelapor, NULL as id_pengaduan")
        ->whereIn('status', $statusList);

    // Only take from `reports` table (no reports_user table)
    // Filter status jika dipilih
    if (!empty($filters['status'])) {
        $builderAnon->where('status', $filters['status']);
    }

    // Filter kategori jika dipilih
    if (!empty($filters['category']) && $filters['category'] !== 'all') {
        if ($filters['category'] === 'Lainnya') {
            $builderAnon->where('other_category IS NOT NULL')->where('other_category !=', '');
        } else {
            $builderAnon->where('category', $filters['category']);
        }
    }

    // Sorting
    $order = ($filters['sort'] === 'oldest') ? 'ASC' : 'DESC';
    $builderAnon->orderBy('created_at', $order);

    // Ambil data
    $reports = $builderAnon->findAll();
    usort($reports, function($a, $b) use ($order) {
        return $order === 'ASC'
            ? strtotime($a['created_at']) <=> strtotime($b['created_at'])
            : strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });

    // Pagination manual
    $total = count($reports);
    $currentPage = (int)($this->request->getGet('page_reports') ?? 1);
    $offset = ($currentPage - 1) * $perPage;
    $reports = array_slice($reports, $offset, $perPage);
    $totalPages = ceil($total / $perPage);

    // Kategori
    $categories = $this->reportModel->distinct()->select('category')->where('category IS NOT NULL')->findAll();
    $otherCategories = $this->reportModel->distinct()->select('other_category')->where('other_category IS NOT NULL')->where('other_category !=', '')->findAll();

    $data = [
        'title' => 'Daftar Laporan Masuk',
        'reports' => $reports,
        'filters' => $filters,
        'categories' => $categories,
        'otherCategories' => $otherCategories,
        'total' => $total,
        'currentPage' => $currentPage,
        'perPage' => $perPage,
        'totalPages' => $totalPages,
        'userRole' => 'admin',
        'layout' => 'universal/layout',
    ];
    return view('universal/list_report', $data);
}

    
public function manajemenUser()
{
    $perPage = 10;
    $filters = [
        'role' => $this->request->getGet('role') ?? '',
        'kebidangan' => $this->request->getGet('kebidangan') ?? '',
        'status' => $this->request->getGet('status') ?? ''
    ];

    $userModel = new \App\Models\UserModel();
    $builder = $userModel;

    // Filter role
    if ($filters['role'] !== '') {
        $builder = $builder->where('role', $filters['role']);
    }
    // Filter kebidangan
    if ($filters['kebidangan'] !== '') {
        $builder = $builder->where('kebidangan', $filters['kebidangan']);
    }
    // Filter status
    if ($filters['status'] !== '') {
        $builder = $builder->where('status', $filters['status']);
    }

    // Pagination group 'users'
    $users = $builder->orderBy('username', 'ASC')->paginate($perPage, 'users');
    $pager = $userModel->pager;

    $data = [
        'title' => 'Manajemen User',
        'users' => $users,
        'filters' => $filters,
        'pager' => $pager,
        'userRole' => session('role'), // Tambahkan baris ini!
    ];

    return view('admin/userManajemen/manajement_user', $data);
}

public function tambahUser()
{
    $userModel = new \App\Models\UserModel();
    if ($this->request->getMethod() === 'POST') {
        $data = [
            'nama_user'     => $this->request->getPost('nama_user'),
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => $this->request->getPost('role'),
            'kebidangan'    => $this->request->getPost('kebidangan'), // Hanya untuk admin_bidang
            'status'        => (int)$this->request->getPost('status'),
        ];
        // Simpan kategori jika admin_bidang
        if ($this->request->getPost('role') === 'admin_bidang') {
            $data['category'] = $this->request->getPost('kategori');
        } else {
            $data['category'] = null;
        }
        if (!$userModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }
        return redirect()->to('/admin/manajemen-user')->with('success', 'User berhasil ditambahkan!');
    }
    return view('admin/userManajemen/tambah_user');
}


public function hapusUser($id)
{
    $userModel = new \App\Models\UserModel();
    if ($userModel->delete($id)) {
        return redirect()->to('/admin/manajemen-user')->with('success', 'User berhasil dihapus!');
    }
    return redirect()->to('/admin/manajemen-user')->with('error', 'Gagal menghapus user');
}

public function resetPassword($id)
{
    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($id);
    if (!$user) {
        return redirect()->to('/admin/manajemen-user')->with('error', 'User tidak ditemukan');
    }
    if ($user['role'] == 'admin') {
        return redirect()->to('/admin/manajemen-user')->with('error', 'Tidak diizinkan');
    }
    $newPass = 'user12345';
    $userModel->update($id, ['password_hash' => password_hash($newPass, PASSWORD_DEFAULT)]);
    return redirect()->to('/admin/manajemen-user')->with('success', 'Password baru: <b>'.$newPass.'</b>');
}



public function editUser($id)
{
    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($id);
    if (!$user) {
        return redirect()->to('/admin/manajemen-user')->with('error', 'User tidak ditemukan');
    }
    if ($this->request->getMethod() === 'POST') {
        $data = [
            'nama_user' => $this->request->getPost('nama_user'),
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'role'     => $this->request->getPost('role'),
            'status'   => (int)$this->request->getPost('status')
        ];
        if ($this->request->getPost('password')) {
            $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }
        // Simpan kategori jika admin_bidang, kosongkan jika bukan
        if ($this->request->getPost('role') === 'admin_bidang') {
            $data['category'] = $this->request->getPost('category');
        } else {
            $data['category'] = null;
        }
        $userModel->skipValidation(true); // Optional: jika ingin validasi manual
        if (!$userModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }
        return redirect()->to('/admin/manajemen-user')->with('success', 'User berhasil diupdate!');
    }
    return view('admin/userManajemen/edit_user', ['user' => $user]);
}

public function disposisi($sumber, $id)
{
    // Only support reports table
    $report = $this->reportModel->find($id);
    $report_source = 'laporan-umum';
    if (!$report) {
        $msg = 'Laporan tidak ditemukan';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }
        return redirect()->back()->with('error', $msg);
    }
    if ($report['status'] !== 'pending') {
        $msg = 'Laporan sudah didisposisi/diproses';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }
        return redirect()->back()->with('error', $msg);
    }
    $kebidangan = $this->request->getPost('kebidangan');
    if (!$kebidangan) {
        $msg = 'Kebidangan belum dipilih';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }
        return redirect()->back()->with('error', $msg);
    }
    // Update on reports table only
    $this->reportModel->update($id, [
        'status' => 'disposition',
        'kebidangan' => $kebidangan,
        'note' => 'Didisposisi ke ' . $kebidangan . ' oleh admin pada ' . date('d/m/Y H:i')
    ]);

    // Log status
    $statusLogModel = new \App\Models\ReportStatusLogModel();
    $statusLogModel->insert([
        'report_id'     => $id,
        'tracking_code' => $report['tracking_code'],
        'report_source' => $report_source,
        'status'        => 'disposition',
        'note'          => 'Didisposisi ke ' . $kebidangan . ' oleh admin',
        'file'          => null,
        'updated_by'    => session('username') ?? 'admin',
        'updated_at'    => date('Y-m-d H:i:s'),
        'kebidangan'    => $kebidangan
    ]);

    $msg = 'Laporan berhasil didisposisi';
    if ($this->request->isAJAX()) {
        return $this->response->setJSON(['success' => true, 'message' => $msg]);
    }
    $back = $this->request->getPost('back');
    if ($back) {
        return redirect()->to($back)->with('success', $msg);
    } else {
        return redirect()->back()->with('success', $msg);
    }
}


public function historyLaporan()
{
    $statusLogModel = new \App\Models\ReportStatusLogModel();

    $selectedBidang = $this->request->getGet('bidang');
    $selectedBulan = $this->request->getGet('bulan');
    $selectedTahun = $this->request->getGet('tahun');

    // Query dasar
    $builder = $statusLogModel->orderBy('updated_at', 'DESC');

    // Filter bidang
    if ($selectedBidang) {
        $builder->where('kebidangan', $selectedBidang);
    }
    // Filter bulan
    if ($selectedBulan) {
        $builder->where('MONTH(updated_at)', $selectedBulan);
    }
    // Filter tahun
    if ($selectedTahun) {
        $builder->where('YEAR(updated_at)', $selectedTahun);
    }

    $logs = $builder->findAll();

    // Kelompokkan per hari
    $logsPerDay = [];
    foreach ($logs as $log) {
        $date = date('Y-m-d', strtotime($log['updated_at']));
        if (!isset($logsPerDay[$date])) {
            $logsPerDay[$date] = [];
        }
        $logsPerDay[$date][] = $log;
    }

    // Ambil daftar kebidangan
    $userModel = new \App\Models\UserModel();
    $kebidangan = $userModel->select('kebidangan')
        ->distinct()
        ->where('kebidangan IS NOT NULL')
        ->where('kebidangan !=', '')
        ->findAll();
    $kebidanganList = array_column($kebidangan, 'kebidangan');
    if (!in_array('Sekretariat', $kebidanganList)) {
        $kebidanganList[] = 'Sekretariat';
    }

    // Ambil daftar tahun dari status log
    $db = \Config\Database::connect();
    $years = $db->table('report_status_log')
        ->select('YEAR(updated_at) as year')
        ->distinct()
        ->orderBy('year', 'DESC')
        ->get()->getResultArray();
    $years = array_column($years, 'year');
    $tahunMulai = min($years) ?: date('Y');

    $data = [
        'title' => 'Histori Perubahan Status Laporan',
        'logsPerDay' => $logsPerDay,
        'userRole' => 'admin',
        'layout' => 'universal/layout',
        'kebidangan' => $kebidanganList,
        'selectedBidang' => $selectedBidang,
        'selectedBulan' => $selectedBulan,
        'selectedTahun' => $selectedTahun,
        'years' => $years,
        'tahunMulai' => $tahunMulai,
    ];
    return view('admin/historyLaporan', $data);
}



public function archive_bulk()
{
    $items = $this->request->getPost('items');
    if (!is_array($items) || empty($items)) {
        return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada laporan yang dipilih.']);
    }
    $archived = 0;
    $statusLogModel = new \App\Models\ReportStatusLogModel();
    $userRole = session('role') ?? 'admin';

    foreach ($items as $item) {
        $id = $item['id'] ?? null;
        // Only consider reports from the main `reports` table
        $report = $this->reportModel->find($id);
        $reportSource = 'laporan-umum';

        if ($report && strtolower($report['status']) === 'completed') {
            $this->reportModel->update($id, [
                'status' => 'archived',
                'note' => 'Diarsipkan oleh ' . $userRole . ' pada ' . date('d/m/Y H:i') . '. Status sebelumnya: ' . $report['status']
            ]);
            $statusLogModel->insert([
                'report_id'     => $id,
                'tracking_code' => $report['tracking_code'] ?? '',
                'report_source' => $reportSource,
                'status'        => 'archived',
                'note'          => 'Diarsipkan oleh ' . $userRole . ' pada ' . date('d/m/Y H:i') . '. Status sebelumnya: ' . $report['status'],
                'file'          => null,
                'updated_by'    => session('username') ?? $userRole,
                'updated_at'    => date('Y-m-d H:i:s'),
                'kebidangan'    => $report['kebidangan'] ?? null
            ]);
            $archived++;
        }
    }

    if ($archived === 0) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Laporan tidak ditemukan.'
        ]);
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => "$archived laporan berhasil diarsipkan."
    ]);
}


    // detailUser removed: application now exclusively uses `reports` table

public function rekapLaporan()
{
    $bulan = $this->request->getGet('bulan');
    $tahun = $this->request->getGet('tahun');

    // Default tahun sekarang jika kosong
    $tahunSekarang = date('Y');
    $tahun = (isset($tahun) && is_numeric($tahun)) ? (int)$tahun : (int)$tahunSekarang;
    $bulan = (!empty($bulan) && is_numeric($bulan)) ? (int)$bulan : null;

    // Query gabungan dari reports & reports_user
    $db = \Config\Database::connect();
    $whereBulan = $bulan ? "MONTH(created_at) = $bulan" : "1=1";
    $whereTahun = $tahun ? "YEAR(created_at) = $tahun" : "1=1";

    // Rekap kategori dari tabel reports
    $sql = "
        SELECT category, COUNT(*) as jumlah FROM (
            SELECT category, created_at FROM reports WHERE $whereBulan AND $whereTahun AND status != 'deleted'
        ) AS all_reports
        GROUP BY category
        ORDER BY jumlah DESC
    ";
    $rekap = [];
    foreach ($db->query($sql)->getResultArray() as $row) {
        $rekap[$row['category'] ?: 'Kategori Lain'] = $row['jumlah'];
    }

    // Untuk dropdown tahun (ambil dari kedua tabel)
    $years1 = $db->table('reports')->select('YEAR(created_at) as year')->distinct()->orderBy('year', 'DESC')->get()->getResultArray();
    $years2 = $db->table('reports')->select('YEAR(created_at) as year')->distinct()->orderBy('year', 'DESC')->get()->getResultArray();
    $years = array_unique(array_merge(array_column($years1, 'year'), array_column($years2, 'year')));
    rsort($years);

    // Hitung persentase perubahan jumlah laporan bulan ini dibanding bulan lalu
    $jumlahBulanIni = 0;
    $jumlahBulanLalu = 0;
    $persen = 0;
    if ($bulan) {
        $jumlahBulanIni = array_sum($rekap); // total laporan bulan ini
        // Hitung bulan lalu
        $bulanLalu = $bulan - 1;
        if ($bulanLalu > 0) {
            $whereBulanLalu = "MONTH(created_at) = $bulanLalu";
            $sqlLalu = "
                SELECT COUNT(*) as jumlah FROM (
                    SELECT created_at FROM reports WHERE $whereBulanLalu AND $whereTahun AND status != 'deleted'
                ) AS all_reports
            ";
            $jumlahBulanLalu = $db->query($sqlLalu)->getRow('jumlah') ?? 0;
        }
        if ($jumlahBulanLalu > 0) {
            $persen = (($jumlahBulanIni - $jumlahBulanLalu) / $jumlahBulanLalu) * 100;
        }
    }

    // Ambil data jumlah laporan per bulan dalam tahun yang dipilih
    $chartData = [
        'labels' => [],
        'data' => []
    ];

    if ($tahun) {
        // Query gabungan laporan per bulan
        $sqlChart = "
            SELECT bulan, SUM(jumlah) as total FROM (
                SELECT MONTH(created_at) as bulan, COUNT(*) as jumlah
                FROM reports
                WHERE YEAR(created_at) = $tahun AND status != 'deleted'
                GROUP BY MONTH(created_at)
            ) AS all_reports
            GROUP BY bulan
            ORDER BY bulan
        ";
        $result = $db->query($sqlChart)->getResultArray();

        // Siapkan array bulan (1-12)
        $bulanList = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        foreach ($bulanList as $num => $nama) {
            $chartData['labels'][] = $nama;
            // Cari data bulan ini di $result
            $found = array_filter($result, fn($row) => (int)$row['bulan'] === $num);
            $chartData['data'][] = $found ? array_values($found)[0]['total'] : 0;
        }
    }

    return view('universal/rekap_laporan', [
        'title' => 'Rekap Laporan',
        'rekap' => $rekap,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'persen' => $persen,
        'tahunMulai' => min($years) ?: date('Y'),
        'years' => $years,
        'chartData' => $chartData,
        'jumlahBulanLalu' => $jumlahBulanLalu,
        'userRole' => session('role') ?? 'admin',
        'layout' => 'universal/layout',
    ]);
}



public function printPdf($id)
{
    $kop_surat = base_url('assets/img/kop_surat.png');
    $report = $this->reportModel->find($id);

    // Ambil status log completed terbaru
    $completed_note = '-';
    $completedLog = $this->statusLogModel
        ->where('report_id', $id)
        ->where('status', 'completed')
        ->orderBy('updated_at', 'DESC')
        ->first();
    if ($completedLog && !empty($completedLog['note'])) {
        $completed_note = $completedLog['note'];
    }

    if ($report) {
        return view('universal/print_template', [
            'report' => $report,
            'sumber' => 'anonim',
            'completed_note' => $completed_note,
            'kop_surat' => $kop_surat
        ]);
    }
    // No fallback to reports_user; if not found in reports, show 404
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
}




public function printAllArsip()
{
    $tahun = $this->request->getGet('year') ?? date('Y');

    // Gunakan rentang tanggal, bukan YEAR()
    $start = $tahun . '-01-01';
    $end = $tahun . '-12-31';

    // Ambil data arsip berdasarkan rentang waktu
    $reports = $this->reportModel
        ->where('status', 'archived')
        ->where('created_at >=', $start)
        ->where('created_at <=', $end)
        ->findAll();

    // Debugging sementara
    if (empty($reports)) {
        echo "Tidak ada data arsip tahun $tahun.";
        return;
    }

    $html = view('admin/arsip_pdf', [
        'tahun' => $tahun,
        'reports' => $reports,
    ]);

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('arsip_laporan_' . $tahun . '.pdf', ['Attachment' => false]);
    exit;
}



public function exportRekapLaporan()
{
    $bulan = $this->request->getGet('bulan');
    $tahun = $this->request->getGet('tahun');

    // Pastikan integer atau null
    $bulan = (!empty($bulan) && is_numeric($bulan)) ? (int)$bulan : null;
    $tahun = (isset($tahun) && is_numeric($tahun)) ? (int)$tahun : (int)date('Y');

    $bulanDipilih = $bulan;
    $tahunDipilih = $tahun;

    $db = \Config\Database::connect();
    $whereBulan = $bulan ? "MONTH(created_at) = $bulan" : "1=1";
    $whereTahun = $tahun ? "YEAR(created_at) = $tahun" : "1=1";

    // 1. Rekap kategori
    $sql = "
        SELECT category, COUNT(*) as jumlah FROM (
            SELECT category, created_at FROM reports WHERE $whereBulan AND $whereTahun AND status != 'deleted'
        ) AS all_reports
        GROUP BY category
        ORDER BY jumlah DESC
    ";
    $rekap = [];
    foreach ($db->query($sql)->getResultArray() as $row) {
        $rekap[$row['category'] ?: 'Kategori Lain'] = $row['jumlah'];
    }

    // 2. Rekap bulanan (jumlah laporan per bulan dalam tahun yang dipilih)
    $rekapBulanan = [];
    $bulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $sqlBulanan = "
        SELECT bulan, SUM(jumlah) as total FROM (
            SELECT MONTH(created_at) as bulan, COUNT(*) as jumlah
            FROM reports
            WHERE YEAR(created_at) = $tahun AND status != 'deleted'
            GROUP BY MONTH(created_at)
        ) AS all_reports
        GROUP BY bulan
        ORDER BY bulan
    ";
    $resultBulanan = $db->query($sqlBulanan)->getResultArray();
    $rekapBulanan = [];
    foreach ($bulanList as $num => $nama) {
        $rekapBulanan[$num] = 0; // inisialisasi semua bulan
    }
    foreach ($resultBulanan as $row) {
        $rekapBulanan[(int)$row['bulan']] = (int)$row['total'];
    }

    // 3. Rekap status laporan (opsional)
    $rekapStatus = [];
    $sqlStatus = "
        SELECT status, COUNT(*) as jumlah FROM (
            SELECT status, created_at FROM reports WHERE $whereBulan AND $whereTahun AND status != 'deleted'
        ) AS all_reports
        GROUP BY status
    ";
    foreach ($db->query($sqlStatus)->getResultArray() as $row) {
        $rekapStatus[$row['status'] ?: 'Lainnya'] = $row['jumlah'];
    }

    // 4. Persentase perubahan bulan ini vs bulan lalu
    $jumlahBulanIni = 0;
    $jumlahBulanLalu = 0;
    $persen = 0;

if ($bulan) {
    $jumlahBulanIni = array_sum($rekap);
    $bulanLalu = $bulan - 1;
    if ($bulanLalu > 0) {
        $whereBulanLalu = "MONTH(created_at) = $bulanLalu";
        $sqlLalu = "
            SELECT COUNT(*) as jumlah FROM (
                SELECT created_at FROM reports WHERE $whereBulanLalu AND $whereTahun AND status != 'deleted'
            ) AS all_reports
        ";
        $jumlahBulanLalu = $db->query($sqlLalu)->getRow('jumlah') ?? 0;
    }
    if ($jumlahBulanLalu > 0) {
        $persen = (($jumlahBulanIni - $jumlahBulanLalu) / $jumlahBulanLalu) * 100;
    } elseif ($jumlahBulanLalu == 0 && $jumlahBulanIni > 0) {
        $persen = 100;
    } else {
        $persen = 0;
    }
}

    // 5. Render view ke HTML
    $html = view('admin/rekap_laporan_pdf', [
        'rekap' => $rekap,
        'rekapBulanan' => $rekapBulanan,
        'rekapStatus' => $rekapStatus,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'persen' => $persen,
        'jumlahBulanLalu' => $jumlahBulanLalu,
        'bulanDipilih' => $bulanDipilih,
        'tahunDipilih' => $tahunDipilih,
        'bulanList' => $bulanList,
        
    ]);

    // 6. Proses PDF dengan Dompdf
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 7. Output PDF ke browser
    $dompdf->stream('rekap_laporan_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => false]);
    exit;
}
public function exportArsipWord()
{
    $tahun = $this->request->getGet('year') ?? date('Y');
    $start = $tahun . '-01-01';
    $end = $tahun . '-12-31';

    $reports = $this->reportModel
        ->where('status', 'archived')
        ->where('created_at >=', $start)
        ->where('created_at <=', $end)
        ->findAll();

    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $section = $phpWord->addSection([
        'orientation' => 'landscape',
        'size' => 'Folio',
        'marginLeft' => 800,
        'marginRight' => 800,
        'marginTop' => 600,
        'marginBottom' => 600,
    ]);

    // Judul
    $section->addText(
        "LAPORAN PENGELOLAAN PENGADUAN PELAYANAN PUBLIK",
        ['bold' => true, 'size' => 14],
        ['alignment' => 'center']
    );
    $section->addText(
        "DINAS LINGKUNGAN HIDUP KOTA PEKALONGAN",
        ['bold' => true, 'size' => 14],
        ['alignment' => 'center']
    );
    $section->addText(
        "TAHUN $tahun",
        ['bold' => true, 'size' => 14],
        ['alignment' => 'center', 'spaceAfter' => 200]
    );

    // Header Tabel
    $headers = [
        'No', 'Tanggal Aduan', 'Nama Pelapor', 'Alamat Pelapor', 'Kanal Aduan', 'Penerima Aduan',
        'Unit Pelayanan Yang Diadukan', 'Isi Aduan', 'Tindak Lanjut Penyelesaian Aduan',
        'Tanggal Tindak Lanjut', 'Status', 'Keterangan'
    ];

    $table = $section->addTable([
        'borderSize' => 6,
        'borderColor' => '000000',
        'cellMargin' => 80
    ]);

    // Row Header
    $table->addRow();
    foreach ($headers as $header) {
        $table->addCell(2000, ['bgColor' => 'cccccc'])->addText($header, ['bold' => true, 'size' => 10], ['alignment' => 'center']);
    }

    // Row Nomor Kolom (1)-(12)
    $table->addRow();
    foreach (range(1, count($headers)) as $i) {
        $table->addCell(2000)->addText("($i)", ['size' => 9], ['alignment' => 'center']);
    }

    // Isi Data
    foreach ($reports as $i => $r) {
        $table->addRow();
        $table->addCell(400)->addText($i + 1, [], ['alignment' => 'center']);
        $table->addCell(1500)->addText(date('d/m/Y', strtotime($r['created_at'] ?? '-')));
        $table->addCell(1800)->addText($r['nama_pelapor'] ?? '-');
        $table->addCell(2000)->addText($r['alamat_pelapor'] ?? '-');
        $table->addCell(1200)->addText('Aplikasi LaporDAH');
        $table->addCell(1500)->addText($r['nama_user'] ?? '-');
        $table->addCell(2000)->addText($r['kebidangan'] ?? '-');
        $table->addCell(3000)->addText($r['chronology'] ?? '-');

        // Tindak Lanjut & Tanggal
        $notes = '-';
        $last_log_date = '-';
        if (isset($r['id'])) {
            $logs = model('App\Models\ReportStatusLogModel')
                ->where('report_id', $r['id'])
                ->where('status', 'completed')
                ->orderBy('updated_at', 'ASC')
                ->findAll();

            $noteArr = [];
            foreach ($logs as $log) {
                if (!empty($log['note'])) $noteArr[] = $log['note'];
                $last_log_date = $log['updated_at'];
            }
            $notes = $noteArr ? implode(" | ", $noteArr) : '-';
        }

        $table->addCell(3000)->addText($notes);
        $table->addCell(1500)->addText($last_log_date ? date('d/m/Y', strtotime($last_log_date)) : '-');

        // Status dan Keterangan
        $status_note = strtolower($r['note'] ?? '');
        $status = (strpos($status_note, 'completed') !== false) ? 'Selesai' : ($r['status'] ?? '-');
        $table->addCell(1200)->addText($status);
        $table->addCell(2000)->addText($r['keterangan'] ?? '-');
    }

    // Footer TTD
    $section->addTextBreak(2);
    $section->addText(
        "Pekalongan, " . date('d F Y') . "\nKepala Dinas Lingkungan Hidup\nKota Pekalongan\n\n\n\nDr. SRI BUDI SANTOSO, M.Si\nPembina Utama Muda\nNIP. 19701214 199003 1 004",
        ['size' => 11],
        ['alignment' => 'right']
    );

    // Output
    $filename = 'arsip_laporan_' . $tahun . '.docx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('php://output');
    exit;
}

/**
 * AJAX endpoint untuk mendapatkan data chart dengan filter tahun
 */
public function getChartData()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $type = $this->request->getPost('type') ?? $this->request->getJSON('type');
    $year = $this->request->getPost('year') ?? $this->request->getJSON('year');

    if ($type === 'categories') {
        return $this->getChartCategories($year);
    } elseif ($type === 'monthly') {
        return $this->getChartMonthly($year);
    }

    return $this->response->setJSON(['success' => false, 'message' => 'Unknown type']);
}

/**
 * Ambil data kategori dengan filter tahun
 */
private function getChartCategories($year = '')
{
    $db = \Config\Database::connect();

    if ($year && $year !== 'all' && $year !== '') {
        // Jika tahun dipilih, ambil data hanya dari tahun tersebut
        $sqlCategory = "
            SELECT category, SUM(jumlah) as jumlah FROM (
                SELECT TRIM(category) as category, COUNT(*) as jumlah
                FROM reports
                WHERE YEAR(created_at) = ? AND TRIM(category) != '' AND category IS NOT NULL
                GROUP BY category
                UNION ALL
                SELECT 'Lainnya' as category, COUNT(*) as jumlah
                FROM reports
                WHERE YEAR(created_at) = ? AND TRIM(other_category) != '' AND other_category IS NOT NULL
            ) AS all_reports
            GROUP BY category
            ORDER BY jumlah DESC
            LIMIT 10
        ";
        $topCategories = $db->query($sqlCategory, [(int)$year, (int)$year])->getResultArray();
    } else {
        // Jika "Semua Tahun", ambil semua data
        $sqlCategory = "
            SELECT category, SUM(jumlah) as jumlah FROM (
                SELECT TRIM(category) as category, COUNT(*) as jumlah
                FROM reports
                WHERE TRIM(category) != '' AND category IS NOT NULL
                GROUP BY category
                UNION ALL
                SELECT 'Lainnya' as category, COUNT(*) as jumlah
                FROM reports
                WHERE TRIM(other_category) != '' AND other_category IS NOT NULL
            ) AS all_reports
            GROUP BY category
            ORDER BY jumlah DESC
            LIMIT 10
        ";
        $topCategories = $db->query($sqlCategory)->getResultArray();
    }

    return $this->response->setJSON([
        'success' => true,
        'categories' => $topCategories
    ]);
}

/**
 * Ambil data statistik bulanan dengan filter tahun
 */
private function getChartMonthly($year = '')
{
    $db = \Config\Database::connect();

    if ($year && $year !== 'all' && $year !== '') {
        // Jika tahun dipilih, ambil data hanya dari tahun tersebut (semua bulan dalam tahun itu)
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '%M %Y') as bulan,
                COUNT(*) as total
            FROM reports
            WHERE YEAR(created_at) = ?
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY MIN(created_at) ASC
        ";
        $monthlyStats = $db->query($sql, [(int)$year])->getResultArray();
    } else {
        // Jika "Semua Tahun", ambil semua data (semua status, semua tahun)
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '%M %Y') as bulan,
                COUNT(*) as total
            FROM reports
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY MIN(created_at) ASC
        ";
        $monthlyStats = $db->query($sql)->getResultArray();
    }

    return $this->response->setJSON([
        'success' => true,
        'monthly' => $monthlyStats
    ]);
}

/**
 * Ambil daftar tahun yang tersedia
 */
public function getAvailableYears()
{
    $db = \Config\Database::connect();
    // Return all years present in reports regardless of status
    $years = $db->table('reports')
        ->select('YEAR(created_at) as year')
        ->distinct()
        ->orderBy('year', 'DESC')
        ->get()
        ->getResultArray();
    
    return array_column($years, 'year');
}
}