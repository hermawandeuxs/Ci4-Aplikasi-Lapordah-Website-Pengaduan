<?php
namespace App\Controllers;

use App\Models\ReportModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Reports extends BaseController
{
    protected $reportModel;
    protected $categories = [
        //'all' => 'Semua Kategori',
        'Pencemaran' => 'Pencemaran',
        'Sampah' => 'Sampah',
        'Perapihan pohon' => 'Perapihan pohon',
        'other' => 'Lainnya'
    ];

    public function __construct()
    {
        $this->reportModel = new ReportModel();
    }

    public function index()
    {
        try {
            $tracking_code = $this->request->getGet('tracking_code');
            $q = $this->request->getGet('q');
            $filter_active = $this->isFilterActive();

            // Default data (all reports)
            $defaultData = $this->getAllReports();

            // If tracking code exists, handle search
            if ($tracking_code) {
                if (!preg_match('/^[A-Za-z0-9-]+$/', $tracking_code)) {
                    session()->setFlashdata('error', 'Format kode tracking tidak valid');
                    return view('reports/index', $defaultData);
                }

                $report = $this->reportModel
                    ->where('tracking_code', $tracking_code)
                    ->where('deleted_at IS NULL')
                    ->where('status !=', 'archived') // Tambahkan filter ini
                    ->first();
                if (!$report) {
                    session()->setFlashdata('error', 'Tidak ada laporan dengan kode tracking tersebut');
                    return view('reports/index', $defaultData);
                }

                if (empty($reports)) {
                    session()->setFlashdata('error', 'Laporan tidak ditemukan untuk pencarian: ' . $q);
                    }

                // Untuk pencarian, pagination tidak perlu
                return view('reports/index', [
                    'title' => 'Hasil Pencarian',
                    'reports' => [$report],
                    'pager_links' => '',
                    'total' => 1,
                    'category' => 'all',
                    'sort' => 'newest',
                    'status' => '',
                    'tracking_code' => $tracking_code,
                    'categories' => $this->categories,
                    'all_reports' => $defaultData['reports'],
                    'statuses' => $defaultData['statuses'],
                    'currentPage' => 1
                ]);
            }

            $q = $this->request->getGet('q');
            if ($q !== null) {
                if (trim($q) === '') {
                    return redirect()->to(base_url('reports'));
                }

                // Batasi maksimal 2 kalimat
                $kalimat = preg_split('/[.!?]+/', $q, -1, PREG_SPLIT_NO_EMPTY);
                if (count($kalimat) > 2) {
                    session()->setFlashdata('error', 'Pencarian maksimal 2 kalimat.');
                    return redirect()->to(base_url('reports'));
                }

                $perPage = 10;
                $reports = $this->reportModel
                    ->groupStart()
                        ->like('tracking_code', $q)
                        ->orLike('location', $q)
                        ->orLike('category', $q)
                        ->orLike('chronology', $q)
                    ->groupEnd()
                    ->where('deleted_at IS NULL')
                    ->where('status !=', 'archived')
                    ->orderBy('created_at', 'DESC')
                    ->paginate($perPage, 'reports');

                $pager = $this->reportModel->pager;
                $pager->setPath('reports');

                $total = $pager->getTotal('reports');
                $page = $pager->getCurrentPage('reports');

                return view('reports/index', [
                    'title' => 'Hasil Pencarian',
                    'reports' => $reports,
                    'pager_links' => $pager->links('reports', 'bootstrap_pagination'),
                    'total' => $total,
                    'category' => 'all',
                    'sort' => 'newest',
                    'status' => '',
                    'q' => $q,
                    'categories' => $this->categories,
                    'all_reports' => $defaultData['reports'],
                    'statuses' => $defaultData['statuses'],
                    'currentPage' => $page
                ]);
            }

            // If filter is active, get filtered data
            if ($filter_active) {
                $filteredData = $this->getFilteredData();
                $filteredData['all_reports'] = $defaultData['reports'];
                return view('reports/index', $filteredData);
            }

            // Show all reports by default
            return view('reports/index', $defaultData);

        } catch (\Exception $e) {
            log_message('error', '[Reports::index] Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memuat data');
            return view('reports/index', $this->getAllReports());
        }
    }

    private function isFilterActive()
    {
        $category = $this->request->getGet('category');
        $sort = $this->request->getGet('sort');
        $status = $this->request->getGet('status');

        return ($category && $category !== 'all') ||
               ($sort && $sort !== 'newest') ||
               ($status && $status !== '');
    }

    private function getCurrentPage(): int
    {
        $page = (int)$this->request->getGet('page');
        return $page > 0 ? $page : 1;
    }

    private function getAllReports()
    {
        $perPage = 10;
        $reports = $this->reportModel
            ->where('status !=', 'rejected')
            ->where('status !=', 'archived')
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'reports');

        $pager = $this->reportModel->pager;
        $pager->setPath('reports');

        return [
            'title' => 'Jelajah Laporan',
            'reports' => $reports,
            'pager_links' => $pager->links('reports', 'bootstrap_pagination'),
            'total' => $pager->getTotal('reports'),
            'category' => 'all',
            'sort' => 'newest',
            'status' => '',
            'tracking_code' => '',
            'categories' => $this->categories,
            'currentPage' => $pager->getCurrentPage('reports'),
            'statuses' => [
                '' => 'Semua Status',
                'pending' => 'Pending',
                'verified' => 'Verified',
                'rejected' => 'Rejected'
            ]
        ];
    }

    private function getFilteredData()
    {
        $perPage = 10;
        $category = $this->request->getGet('category') ?? 'all';
        if (empty($category)) {
            $category = 'all';
        }
        $sort = $this->request->getGet('sort') ?? 'newest';
        $status = $this->request->getGet('status') ?? '';

        $builder = $this->reportModel
            ->where('status !=', 'archived')
            ->where('status !=', 'rejected');

        if ($status) {
            $builder->where('status', $status);
        }
        if ($category !== 'all') {
            if ($category === 'other') {
                $builder->groupStart()
                    ->where('category', 'Lainnya')
                    ->orWhere('other_category IS NOT NULL', null, false)
                    ->groupEnd();
            } else {
                $categoryLabel = $this->categories[$category] ?? $category;
                $builder->where('category', $categoryLabel);
            }
        }
        $builder->where('deleted_at IS NULL');
        $builder->orderBy('created_at', ($sort === 'oldest') ? 'ASC' : 'DESC');

        $reports = $builder->paginate($perPage, 'reports');
        $pager = $this->reportModel->pager;
        $pager->setPath('reports');

        $categoryLabel = $category !== 'all' ? ($this->categories[$category] ?? $category) : 'Semua Kategori';

        return [
            'title' => 'Hasil Filter',
            'reports' => $reports,
            'pager_links' => $pager->links('reports', 'bootstrap_pagination'),
            'total' => $pager->getTotal('reports'),
            'category' => $category,
            'category_label' => $categoryLabel,
            'sort' => $sort,
            'status' => $status,
            'tracking_code' => '',
            'categories' => $this->categories,
            'currentPage' => $pager->getCurrentPage('reports'),
            'statuses' => [
                '' => 'Semua Status',
                'pending' => 'Pending',
                'verified' => 'Verified',
                'rejected' => 'Rejected'
            ]
        ];
    }

    public function detail($id)
    {
        $reportModel = new \App\Models\ReportModel();
        $statusLogModel = new \App\Models\ReportStatusLogModel();

        $report = $reportModel->find($id);
        if (!$report) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Ambil evidence_1, evidence_2, evidence_3 sebagai array gambar
        $evidences = [];
        foreach (['evidence_1', 'evidence_2', 'evidence_3'] as $field) {
            if (!empty($report[$field])) {
                $evidences[] = $report[$field];
            }
        }
        $report['evidences'] = $evidences;

        // Ambil status/proses log
        $statusLogs = $statusLogModel->where('report_id', $id)->orderBy('updated_at', 'asc')->findAll();

        return view('reports/detail_report', [
            'report' => $report,
            'statusLogs' => $statusLogs,
        ]);
    }

    public function check()
    {
        $q = $this->request->getGet('q');
        // Lakukan pencarian sesuai logic Anda
        $found = $this->reportModel->where('tracking_code', $q)
            ->orLike('location', $q)
            ->orLike('category', $q)
            ->orLike('chronology', $q)
            ->countAllResults() > 0;
        return $this->response->setJSON(['found' => $found]);
    }
}

