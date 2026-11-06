<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class File extends Controller
{
    public function statusLog($filename)
    {
        $path = WRITEPATH . '../uploads/status_log/' . $filename; // sesuaikan path jika perlu
        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->response->download($path, null);
    }
}