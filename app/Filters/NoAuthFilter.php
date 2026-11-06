<?php 
//namespace App\Filters;

//use CodeIgniter\HTTP\RequestInterface;
//use CodeIgniter\HTTP\ResponseInterface;
//use CodeIgniter\Filters\FilterInterface;

//class NoAuthFilter implements FilterInterface
{
    //public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');
            
            // Redirect based on role
            switch($role) {
                case 'admin':
                    return redirect()->to(base_url('admin/dashboard'));
                case 'admin_bidang':
                    return redirect()->to(base_url('admin-bidang/dashboard'));
                default:
                    return redirect()->to(base_url('user/dashboard'));
            }
        }
        return;
    }

    //public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
        return $response;
    }
}