<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        // Ambil role dari session
        $role = session()->get('role');
        
        // Gunakan getPath() untuk mendapatkan current URI
        $currentPath = $request->getPath();
        $segment = explode('/', $currentPath)[0];

        // Cek akses berdasarkan role
        if ($segment === 'admin' && $role !== 'admin') {
            return redirect()->to('/'.$role.'/dashboard');
        }
        
        if ($segment === 'admin-bidang' && $role !== 'admin_bidang') {
            return redirect()->to('/'.$role.'/dashboard');
        }
        
        if ($segment === 'user' && $role !== 'user') {
            return redirect()->to('/'.$role.'/dashboard');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}