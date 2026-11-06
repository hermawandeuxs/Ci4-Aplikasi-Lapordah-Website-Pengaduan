<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        
        // Ensure $arguments is an array
        $allowedRoles = is_array($arguments) ? $arguments : [$arguments];
        
        // Check if user's role is in allowed roles
        if (!in_array($role, $allowedRoles)) {
            return redirect()->back()
                           ->with('error', '<i class="fas fa-exclamation-circle"></i> Akses ditolak! Anda tidak memiliki hak akses sebagai ' . implode('/', $allowedRoles));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }
}