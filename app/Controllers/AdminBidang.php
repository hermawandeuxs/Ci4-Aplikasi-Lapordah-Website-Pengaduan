<?php
namespace App\Controllers;
use Dompdf\Dompdf;
use Dompdf\Options;

use App\Models\ReportModel;
use App\Models\VerificationModel;
use CodeIgniter\Controller;

class AdminBidang extends BaseController
{
    protected $reportModel;
    protected $statusLogModel;

    // ENUM kebidangan yang valid, samakan dengan definisi ENUM di DB!
    private $enumKebidangan = ['Sekretariat', 'PPKL', 'TALING','Kebersihan'];

    public function __construct()
    {
        $this->reportModel = new ReportModel();
        // $this->reportUserModel = new ReportUserModel();
        $this->statusLogModel = new \App\Models\ReportStatusLogModel();
    }

    public function dashboard()
    {
        try {
            $userModel = new \App\Models\UserModel();
            $username = session('username');
            $user = $userModel->where('username', $username)->first();
            $kebidangan = $user['kebidangan'] ?? null;
            $like = "%$kebidangan%";
            $db = \Config\Database::connect();

            $countReports = $db->table('reports')->where("note LIKE '$like'")->whereIn('status', ['pending', 'disposition', 'verified', 'progress'])->countAllResults();
            $countRejected = $db->table('reports')->where("note LIKE '$like'")->where('status', 'rejected')->countAllResults();
            $countArsip = $db->table('reports')->where("note LIKE '$like'")->where('status', 'archived')->countAllResults();
            
            // Statistik status
            $statuses = [ 'disposition', 'verified', 'rejected', 'archived', 'progress', 'done'];
            $stat = array_fill_keys($statuses, 0);
            $stat['total'] = 0;

            $sql = "
                SELECT status, COUNT(*) as jumlah FROM (
                    SELECT status FROM reports WHERE note LIKE ?
                ) AS all_reports
                GROUP BY status
            ";
            $rows = $db->query($sql, [$like])->getResultArray();
            foreach ($rows as $row) {
                $status = strtolower($row['status']);
                $stat['total'] += $row['jumlah'];
                // Mapping status database ke index array statistik
                if ($status === 'completed') $status = 'done';
                if (isset($stat[$status])) $stat[$status] = $row['jumlah'];
            }

            // Pastikan semua status ada di array


            // Statistik bulanan 6 bulan terakhir
            $date6bulan = date('Y-m-01', strtotime('-5 months'));
            $sqlMonthly = "
                SELECT bulan, SUM(total) as jumlah FROM (
                    SELECT DATE_FORMAT(created_at, '%b %Y') as bulan, COUNT(*) as total
                    FROM reports
                    WHERE note LIKE ? AND created_at >= ?
                    GROUP BY YEAR(created_at), MONTH(created_at)
                ) AS all_reports
                GROUP BY bulan
                ORDER BY STR_TO_DATE(CONCAT('01 ', bulan), '%d %b %Y') ASC
            ";
            $monthlyStats = $db->query($sqlMonthly, [$like, $date6bulan])->getResultArray();

            // Statistik kategori terbanyak
            $sqlCategory = "
   
        SELECT category, SUM(jumlah) as jumlah FROM (
    
        SELECT 
                TRIM(category) as category, COUNT(*) as jumlah
        FROM reports
        WHERE note LIKE ? AND created_at >= ?
            AND TRIM(category) != '' AND category IS NOT NULL
        GROUP BY category

        UNION ALL

        SELECT 
                'Lainnya' as category, COUNT(*) as jumlah
        FROM reports
        WHERE note LIKE ? AND created_at >= ?
            AND TRIM(other_category) != '' AND other_category IS NOT NULL
) AS all_reports
GROUP BY category
ORDER BY jumlah DESC
LIMIT 10
";
                        $topCategories = $db->query($sqlCategory, [$like, $date6bulan, $like, $date6bulan])->getResultArray();

            $data = [
                'title' => 'Dashboard Admin Bidang',
                'bidangStats' => [$kebidangan => $stat],
                'kebidangan' => $kebidangan,
                'userRole' => 'admin_bidang',
                'userName' => $user['name'] ?? 'Admin Bidang',
                'topCategories' => $topCategories,
                'monthlyStats' => $monthlyStats,
                'countReports' => $countReports,
                'countRejected' => $countRejected,
                'countArsip' => $countArsip,
                'layout' => 'admin_bidang/layouts',
            ];

            return view('universal/dashboard', $data);

        } catch (\Exception $e) {
            log_message('error', '[Dashboard Error] ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memuat dashboard');
            return redirect()->back();
        }
    }

    public function listLaporan()
    {
        $kebidangan = session()->get('kebidangan');
        $perPage = 10;
        $filters = [
            'status' => $this->request->getGet('status'),
            'category' => $this->request->getGet('category'),
            'sort' => $this->request->getGet('sort') ?? 'newest'
        ];

        $statusList = !empty($filters['status'])
            ? [$filters['status']]
            : ['verified', 'disposition', 'progress', 'completed'];

        $result = $this->getFilteredReports($statusList, $filters, $kebidangan, $perPage);

        $data = [
            'title' => 'Daftar Laporan Disposisi untuk ' . $kebidangan,
            'filters' => $filters,
            'userRole' => 'admin-bidang',
            'layout' => 'universal/layout',
        ] + $result;

        return view('universal/list_report', $data);
    }

    /**
     * Menampilkan profil admin bidang
     */
    public function profile()
    {
        return view('admin_bidang/profile', [
            'title' => 'Profil Admin Bidang'
        ]);
    }

    /**
     * Verifikasi laporan (baik user maupun anonim)
     */
    public function verify($source, $id)
    {
        $rules = [
            'status' => 'required|in_list[verified,rejected]',
        ];

        if (!$this->validate($rules)) {
            $msg = $this->validator->getErrors();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => implode(', ', $msg)]);
            }
            return redirect()->back()->withInput()->with('errors', $msg);
        }

        $status = $this->request->getPost('status');
        $note2 = $this->request->getPost('note') ?? '';
        $username = session('username');

        $kebidangan = $this->getValidKebidangan();
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($source === 'user') {
            $msg = 'Operasi untuk laporan user tidak didukung setelah penghapusan reports_user';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        } else {
            $report = $this->reportModel->find($id);
            if (!$report) {
                $msg = 'Laporan tidak ditemukan';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg]);
                }
                return redirect()->back()->with('error', $msg);
            }

            // Jika status yang dipilih adalah 'rejected' -> lakukan soft delete
            if ($status === 'rejected') {
                $db = \Config\Database::connect();
                $db->table('reports')->where('id', $id)->update([
                    'status' => 'deleted',
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'deleted_by' => $username
                ]);

                // Masukkan log status 'deleted' dengan catatan (Dihapus)
                $this->statusLogModel->insert([
                    'report_id'     => $id,
                    'tracking_code' => $report['tracking_code'] ?? '',
                    'report_source' => 'laporan-umum',
                    'status'        => 'deleted',
                    'note'          => '(Dihapus)',
                    'file'          => null,
                    'updated_by'    => $username,
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'kebidangan'    => $kebidangan
                ]);

                $msg = 'Laporan berhasil dihapus (soft delete)';
            } else {
                $this->reportModel->update($id, [
                    'status' => $status,
                    'verified_at' => date('Y-m-d H:i:s'),
                    'verified_by' => $username
                ]);

                $this->statusLogModel->insert([
                    'report_id'     => $id,
                    'tracking_code' => $report['tracking_code'] ?? '',
                    'report_source' => 'laporan-umum',
                    'status'        => $status,
                    'note'          => $note2,
                    'file'          => null,
                    'updated_by'    => $username,
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'kebidangan'    => $kebidangan
                ]);

                $msg = 'Laporan berhasil diverifikasi';
            }
        }
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => $msg]);
        }
        $back = $this->request->getPost('back');
        if ($back) {
            return redirect()->to($back)->with('success', $msg);
        } else {
            return redirect()->to('admin-bidang/laporan/' . $id)->with('success', $msg);
        }
    }

    public function update_progress($id)
    {
        // Only support reports table
        $report = $this->reportModel->find($id);
        $reportSource = 'laporan-umum';
        if (!$report) {
            $msg = 'Laporan tidak ditemukan';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }
        $trackingCode = $report['tracking_code'] ?? '';

        $progressNote = $this->request->getPost('progress_note');
        if (!$progressNote) {
            $msg = 'Catatan progress wajib diisi';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Update reports table only
        $this->reportModel->update($id, ['status' => 'progress']);

        $kebidangan = $this->getValidKebidangan();
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('username', session('username'))->first();

        $this->statusLogModel->insert([
            'report_id'     => $id,
            'tracking_code' => $trackingCode,
            'report_source' => $reportSource,
            'status'        => 'progress',
            'note'          => $progressNote,
            'updated_by'    => session('username'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'kebidangan'    => $kebidangan
        ]);

        $msg = 'Status laporan diubah menjadi Progress';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => $msg]);
        }
        $back = $this->request->getPost('back');
        if ($back) {
            return redirect()->to($back)->with('success', $msg);
        } else {
            return redirect()->to('admin-bidang/laporan/' . $id)->with('success', $msg);
        }
    }

    /**
     * Hapus (soft) laporan untuk Admin Bidang — hanya jika status sudah 'rejected'
     */
    public function delete($id)
    {
        $report = $this->reportModel->find($id);
        if (!$report) {
            $msg = 'Laporan tidak ditemukan';
            if ($this->request->isAJAX()) return $this->response->setJSON(['success' => false, 'message' => $msg]);
            return redirect()->back()->with('error', $msg);
        }

        if (strtolower($report['status']) !== 'rejected') {
            $msg = 'Hanya laporan dengan status "rejected" yang dapat dihapus oleh Admin Bidang';
            if ($this->request->isAJAX()) return $this->response->setJSON(['success' => false, 'message' => $msg]);
            return redirect()->back()->with('error', $msg);
        }

        $username = session('username') ?? 'admin_bidang';
        $kebidangan = $this->getValidKebidangan();

        $db = \Config\Database::connect();
        $db->transStart();
        // Soft delete
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
            'kebidangan'    => $kebidangan
        ]);

        $db->transComplete();
        if ($db->transStatus() === false) {
            log_message('error', '[Delete Error] Gagal melakukan soft-delete untuk laporan ' . $id);
            if ($this->request->isAJAX()) return $this->response->setJSON(['success' => false, 'message' => 'Gagal melakukan penghapusan']);
            return redirect()->back()->with('error', 'Gagal melakukan penghapusan');
        }

        $msg = 'Laporan berhasil dihapus (soft delete)';
        if ($this->request->isAJAX()) return $this->response->setJSON(['success' => true, 'message' => $msg]);
        return redirect()->back()->with('success', $msg);
    }

    public function update_completed($id)
    {
        // Only support reports table
        $report = $this->reportModel->find($id);
        $reportSource = 'anonim';
        if (!$report) {
            $msg = 'Laporan tidak ditemukan';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }
        $trackingCode = $report['tracking_code'] ?? '';

        $completedNote = $this->request->getPost('completed_note');
        if (!$completedNote) {
            $msg = 'Catatan wajib diisi';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $file = $this->request->getFile('completed_file');
        $fileName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['pdf', 'jpg', 'jpeg'])) {
                $msg = 'File harus berupa PDF, JPG, atau JPEG';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg]);
                }
                return redirect()->back()->with('error', $msg);
            }
            $fileName = uniqid('completed_') . '.' . $ext;
            $file->move(FCPATH . 'uploads/status_log/', $fileName);
        }

        // Update reports table only
        $this->reportModel->update($id, ['status' => 'completed']);

        $kebidangan = $this->getValidKebidangan();
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('username', session('username'))->first();

        $this->statusLogModel->insert([
            'report_id'     => $id,
            'tracking_code' => $trackingCode,
            'report_source' => $reportSource === 'user' ? 'laporan-user' : 'laporan-umum',
            'status'        => 'completed',
            'note'          => $completedNote,
            'file'          => $fileName,
            'updated_by'    => session('username'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'kebidangan'    => $kebidangan
        ]);

        $msg = 'Status laporan diubah menjadi Completed';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => $msg]);
        }
        $back = $this->request->getPost('back');
        if ($back) {
            return redirect()->to($back)->with('success', $msg);
        } else {
            return redirect()->to('admin-bidang/laporan/' . $id)->with('success', $msg);
        }
    }


    public function archive($id)
{
    $userRole = session('role') ?? 'admin-bidang';
    $kebidangan = session('kebidangan') ?? '';
    // Cek di laporan anonim
    $report = $this->reportModel->find($id);
    if ($report) {
        if (strtolower($report['status']) !== 'completed') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Laporan harus komplit dahulu'
            ]);
        }
        $note = 'Diarsipkan oleh ' . $userRole . ($kebidangan ? ' (' . $kebidangan . ')' : '') . ' pada ' . date('d/m/Y H:i') . '. Status sebelumnya: ' . $report['status'];
        $this->reportModel->update($id, [
            'status' => 'archived',
            'note' => $note
        ]);
        $this->statusLogModel->insert([
            'report_id'     => $id,
            'tracking_code' => $report['tracking_code'] ?? '',
            'report_source' => 'laporan-umum',
            'status'        => 'archived',
            'note'          => $note,
            'file'          => null,
            'updated_by'    => session('username') ?? $userRole,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);
        return $this->response->setJSON(['success' => true, 'message' => 'Laporan berhasil diarsipkan.']);
    }

    // Only support reports table; if not found in reports, report not found
    return $this->response->setJSON(['success' => false, 'message' => 'Laporan tidak ditemukan.']);
}

    /**
     * Helper untuk mendapatkan path folder bukti laporan di server.
     */
public function detailUser($id_pengaduan)
{
    // Operation for user reports is no longer supported
    return redirect()->back()->with('error', 'Operasi untuk laporan user tidak didukung setelah penghapusan reports_user');
}

public function detailAnonim($id)
{
    $this->statusLogModel = new \App\Models\ReportStatusLogModel();
    $report = $this->reportModel->find($id);
    if (!$report) {
        return redirect()->back()->with('error', 'Laporan anonim tidak ditemukan');
    }
    $report['sumber'] = 'laporan-umum';
    $report['universal_id'] = $report['id'];

    // Gunakan getEvidencePathByReport
    $evidencePath = $this->getEvidencePathByReport($report);
    $evidence_urls = [];
    for ($i = 1; $i <= 3; $i++) {
        $field = 'evidence_' . $i;
        if (!empty($report[$field])) {
            $file_path = $evidencePath . $report[$field];
            if (file_exists($file_path)) {
                $relative = str_replace('\\', '/', str_replace(FCPATH . 'uploads/', '', $evidencePath));
                $relative = rtrim($relative, '/');
                $evidence_urls[] = base_url('uploads/' . $relative . '/' . $report[$field]);
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
        'userRole' => 'admin-bidang',
        'layout' => 'universal/layout',
        'statusLogs' => $statusLogs,
        'evidence_urls' => $evidence_urls,
    ];
    return view('universal/report_detail', $data);
}


    /**
     * Mengarsipkan laporan secara massal
     * @return \CodeIgniter\HTTP\Response
     */
public function archive_bulk()
{
    $items = $this->request->getPost('items');
    if (!is_array($items) || empty($items)) {
        return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada laporan yang dipilih.']);
    }
    $archived = 0;
    $statusLogModel = new \App\Models\ReportStatusLogModel();
    $userRole = session('role') ?? 'admin';
    $kebidangan = session('kebidangan') ?? '';

    foreach ($items as $item) {
        $id = $item['id'] ?? null;
        $report = $this->reportModel->find($id);
        $reportSource = 'laporan-umum';

        if ($report && strtolower($report['status']) === 'completed') {
            $note = 'Diarsipkan oleh ' . $userRole . ($kebidangan ? ' (' . $kebidangan . ')' : '') . ' pada ' . date('d/m/Y H:i') . '. Status sebelumnya: ' . $report['status'];
            $this->reportModel->update($id, [
                'status' => 'archived',
                'note' => $note
            ]);
            $statusLogModel->insert([
                'report_id'     => $id,
                'tracking_code' => $report['tracking_code'] ?? '',
                'report_source' => $reportSource,
                'status'        => 'archived',
                'note'          => $note,
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

    /**
     * Ambil kebidangan user yang login (langsung, tanpa validasi enum)
     */
    private function getValidKebidangan()
    {
        $userModel = new \App\Models\UserModel();
        $username = session('username');
        $user = $userModel->where('username', $username)->first();
        // Ambil langsung, boleh tambahkan trim jika ingin
        return trim($user['kebidangan'] ?? '');
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
                    @unlink($filePath);
                }
            }
        }
    }

    /**
     * Return filesystem path to evidence folder for a given report.
     * Supports anonymous reports (`laporan-umum`) and legacy user reports.
     */
    private function getEvidencePathByReport(array $report): string
    {
        $base = FCPATH . 'uploads/';

        // Prefer explicit sumber/report_source when available
        $sumber = strtolower($report['sumber'] ?? $report['report_source'] ?? 'laporan-umum');

        if ($sumber === 'laporan-umum' || $sumber === 'anonim') {
            return $base . 'reports/';
        }

        // Fallback for user-origin reports
        return $base . 'reports_user/';
    }

    /**
     * Ambil dan filter laporan untuk listLaporan
     */
    private function getFilteredReports($statusList, $filters, $kebidangan, $perPage)
    {
        // Ambil laporan anonim
        $builder = $this->reportModel->like('note', $kebidangan);

        if (!empty($statusList)) {
        
            if (is_array($statusList)) {
                $builder->whereIn('status', $statusList);
            } else {
                $builder->where('status', $statusList);
            }
        }

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            if ($filters['category'] === 'Lainnya') {
                $builder->where('other_category IS NOT NULL')->where('other_category !=', '');
            } else {
                $builder->where('category', $filters['category']);
            }
        }

        $order = ($filters['sort'] ?? 'newest') === 'oldest' ? 'ASC' : 'DESC';
        $reports = $builder->orderBy('created_at', $order)->paginate($perPage, 'reports');
        foreach ($reports as &$report) {
            if (!isset($report['id']) && isset($report['id_pengaduan'])) {
                $report['id'] = $report['id_pengaduan'];
            }
            $report['sumber'] = 'anonim';
            $report['universal_id'] = $report['id'];
        }

        // reports_user table removed — do not include user reports
        $reportsGabungan = $reports;

        $currentPage = (int)($this->request->getGet('page_reports') ?? 1);
        $total = count($reportsGabungan);
        $totalPages = ceil($total / $perPage);

        return [
            'reports' => $reportsGabungan,
            'pager' => $this->reportModel->pager,
            'categories' => $this->reportModel->distinct()->select('category')->where('category IS NOT NULL')->findAll(),
            'otherCategories' => $this->reportModel->distinct()->select('other_category')->where('other_category IS NOT NULL')->where('other_category !=', '')->findAll(),
            'total' => $total,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
        ];
    }

    public function laporanRejectGabungan()
    {
        $kebidangan = session()->get('kebidangan');
        $perPage = 10;
        $filters = [
            'category' => $this->request->getGet('category'),
            'sort' => $this->request->getGet('sort') ?? 'newest'
        ];

    // Laporan anonim (note LIKE bidang)
    $builderAnon = $this->reportModel
        ->select("id, tracking_code, NULL as username, status, category, other_category, location, chronology, created_at, 'anonim' as sumber")
        ->where('status', 'rejected')
        ->like('note', $kebidangan);

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
        'userRole' => 'admin-bidang',
        'layout' => 'universal/layout',
    ]);
}

    /**
     * Menampilkan arsip laporan gabungan (anonim dan user)
     */
public function reportArsipGabungan()
{
    $kebidangan = session()->get('kebidangan');
    $filters = [
        'category' => $this->request->getGet('category'),
        'sort' => $this->request->getGet('sort') ?? 'newest',
        'year' => $this->request->getGet('year') ?? 'all'
    ];
    $perPage = 10;

    $builderAnon = $this->reportModel
        ->select("id, tracking_code, NULL as username, status, category, other_category, location, chronology, created_at, 'anonim' as sumber, NULL as id_pengaduan, nama_pelapor")
        ->where('status', 'archived')
        ->like('note', $kebidangan);

    // reports_user removed — only use reports table

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
        'userRole' => 'admin-bidang',
        'layout' => 'universal/layout',
    ]);
}



public function rekapLaporan()
{
    $bulan = $this->request->getGet('bulan');
    $tahun = $this->request->getGet('tahun');

    // Default tahun sekarang jika kosong
    $tahunSekarang = date('Y');
    $tahun = (isset($tahun) && is_numeric($tahun)) ? (int)$tahun : (int)$tahunSekarang;
    $bulan = (!empty($bulan) && is_numeric($bulan)) ? (int)$bulan : null;

    // Query from reports table only (reports_user removed)
    $db = \Config\Database::connect();
    $whereBulan = $bulan ? "MONTH(created_at) = $bulan" : "1=1";
    $whereTahun = $tahun ? "YEAR(created_at) = $tahun" : "1=1";

    // Rekap kategori dari reports saja
    $sql = "
        SELECT category, COUNT(*) as jumlah
        FROM reports
        WHERE $whereBulan AND $whereTahun AND status != 'deleted'
        GROUP BY category
        ORDER BY jumlah DESC
    ";
    $rekap = [];
    foreach ($db->query($sql)->getResultArray() as $row) {
        $rekap[$row['category'] ?: 'Kategori Lain'] = $row['jumlah'];
    }

    // Untuk dropdown tahun (ambil dari kedua tabel)
    // Untuk dropdown tahun (ambil dari reports saja)
    $years1 = $db->table('reports')->select('YEAR(created_at) as year')->distinct()->orderBy('year', 'DESC')->get()->getResultArray();
    $years = array_unique(array_column($years1, 'year'));
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
                SELECT COUNT(*) as jumlah
                FROM reports
                WHERE $whereBulanLalu AND $whereTahun AND status != 'deleted'
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
            SELECT MONTH(created_at) as bulan, COUNT(*) as total
            FROM reports
            WHERE YEAR(created_at) = $tahun AND status != 'deleted'
            GROUP BY MONTH(created_at)
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


    /**
     * Cetak laporan dalam format PDF
     */
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
    } else {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
}

}