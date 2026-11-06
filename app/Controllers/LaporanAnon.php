<?php 
/**
 * ========================================
 * LAPORAN ANONIM CONTROLLER
 * ========================================
 * 
 * Menghandle pengajuan laporan anonim tanpa login.
 * 
 * Fitur:
 * - Submit laporan dengan file bukti
 * - Check status laporan via tracking code
 * - Display evidence images
 * 
 * @package App\Controllers
 * @author LaporDAH Team
 */

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use App\Models\ReportModel;

class LaporanAnon extends Controller
{
    use ResponseTrait;
    
    protected $reportModel;
    protected $uploadPath;
    
    public function __construct()
    {
        $this->reportModel = new ReportModel();
        // Set upload path untuk file bukti laporan
        $this->uploadPath = FCPATH . 'uploads/reports/';
        
        // Buat directory jika belum ada
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    /**
     * submit() - Submit Laporan Anonim
     * 
     * Handle GET (tampilkan form) dan POST (process laporan)
     * 
     * Form fields:
     * - category: Kategori laporan
     * - location: Lokasi kejadian (HARUS mengandung "pekalongan")
     * - chronology: Deskripsi kejadian
     * - nama_pelapor: Nama pelapor (opsional tapi diperlukan)
     * - alamat_pelapor: Alamat (opsional)
     * - email_pelapor: Email (opsional)
     * - hp_pelapor: No. HP (opsional)
     * - evidence: File bukti (max 3 file, max 2MB each, JPG/PNG)
     * - latitude: Koordinat latitude
     * - longitude: Koordinat longitude
     * 
     * Response: JSON dengan structure:
     * {
     *   "success": true/false,
     *   "message": "...",
     *   "tracking_code": "LAPORDAN-xxxxx" (jika berhasil)
     * }
     * 
     * @return string|JSON
     */
    public function submit()
    {
        try {
            // Validasi dasar tanpa file
            $rules = [
                'category' => 'required',
                'location' => 'required',
                'chronology' => 'required'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $this->validator->getErrors()
                ]);
            }

            // Handle file uploads dengan validasi manual
            $uploadedFiles = [];
            $files = $this->request->getFileMultiple('evidence');
            
            if ($files) {
                if (count($files) > 3) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Maksimal 3 file yang dapat diunggah'
                    ]);
                }

                foreach ($files as $idx => $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        if ($file->getSize() > 2048000) {
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => 'File ' . $file->getName() . ' melebihi 2MB'
                            ]);
                        }

                        $type = $file->getClientMimeType();
                        if (!in_array($type, ['image/jpeg', 'image/jpg', 'image/png'])) {
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => 'File harus berupa JPG, JPEG, atau PNG'
                            ]);
                        }

                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/reports', $newName);
                        $uploadedFiles['evidence_'.($idx+1)] = $newName;
                    }
                }
            }
            
            $request = $this->request;
            // Ambil data diri dari request
            $nama_pelapor = $request->getPost('nama_pelapor');
            if (empty($nama_pelapor)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama pelapor tidak boleh kosong'
                ]);
            }
            $alamat_pelapor = $request->getPost('alamat_pelapor');
            $email_pelapor = $request->getPost('email_pelapor');
            $hp_pelapor = $request->getPost('hp_pelapor');

            // Ambil koordinat lokasi dari request
            $latitude = $this->request->getPost('latitude');
            $longitude = $this->request->getPost('longitude');

            // Ambil lokasi kejadian dari request
            $lokasi_kejadian = strtolower($this->request->getPost('location'));

            // Validasi lokasi harus mengandung kata "pekalongan"
            if (strpos($lokasi_kejadian, 'pekalongan') === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Lokasi kejadian harus di Pekalongan.'
                ]);
            }

            // Generate tracking code
            $trackingCode = 'LAP-' . strtoupper(uniqid());

            // Prepare data for database insertion
            $data = [
                'tracking_code' => $trackingCode,
                'category' => $this->request->getPost('category'),
                'other_category' => $this->request->getPost('category') === 'Lainnya' ? 
                    $this->request->getPost('other_category') : 
                    null,
                'location' => $this->request->getPost('location'),
                'chronology' => $this->request->getPost('chronology'),
                'evidence_1' => $uploadedFiles['evidence_1'] ?? null,
                'evidence_2' => $uploadedFiles['evidence_2'] ?? null,
                'evidence_3' => $uploadedFiles['evidence_3'] ?? null,
                // Data diri:
                'nama_pelapor'  => $nama_pelapor,
                'alamat_pelapor' => $this->request->getPost('alamat_pelapor') ?? null,
                'email_pelapor' => $email_pelapor,
                'hp_pelapor'    => $hp_pelapor,
                // Koordinat:
                'latitude'      => $latitude,
                'longitude'     => $longitude,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Insert into database
            $this->reportModel->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Laporan berhasil dikirim',
                'tracking_code' => $trackingCode
            ]);

        } catch (\Exception $e) {
            log_message('error', '[LaporanAnon::submit] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function checkStatus()
    {
        $trackingCode = $this->request->getGet('code');
        
        if (!$trackingCode) {
            return $this->respond([
                'success' => false,
                'message' => 'Kode tracking diperlukan'
            ], 400);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('reports');
        $report = $builder->where('tracking_code', $trackingCode)->get()->getRow();

        if (!$report) {
            return $this->respond([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'data' => $report
        ], 200);
    }

    // Tambahkan method untuk menampilkan gambar
    public function showImage($filename)
    {
        $path = $this->uploadPath . $filename;
        if (file_exists($path)) {
            $mime = mime_content_type($path);
            header('Content-Type: ' . $mime);
            readfile($path);
            exit;
        }
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function getCategories()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('categories');
        $categories = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }
}