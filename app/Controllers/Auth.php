<?php 
/**
 * ========================================
 * AUTHENTICATION CONTROLLER
 * ========================================
 * 
 * Menghandle seluruh proses autentikasi aplikasi:
 * - Login (admin, admin_bidang, user biasa)
 * - Register (user baru)
 * - Forgot Password & Password Reset
 * - Logout
 * 
 * @package App\Controllers
 * @author LaporDAH Team
 */

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    /**
     * login() - Menampilkan Form Login
     * 
     * Menampilkan halaman login dimana user dapat memasukkan username/email & password
     * 
     * @return string View halaman login
     */
    // Tampilkan form login
    public function login()
    {
        return view('auth/login');
    }

    /**
     * attemptLogin() - Proses Login User
     * 
     * Memvalidasi kredensial user:
     * 1. Cari user berdasarkan username atau email
     * 2. Verifikasi password menggunakan password_verify()
     * 3. Cek status akun (harus aktif)
     * 4. Untuk admin_bidang, pastikan memiliki kebidangan
     * 5. Set session & redirect ke dashboard sesuai role
     * 
     * Alur:
     * - Admin -> /admin
     * - Admin Bidang -> /admin-bidang
     * 
     * @return redirect Ke dashboard atau kembali ke login dengan error
     */
    // Proses login
    public function attemptLogin()
    {
        // Ambil input dari form login
        $login    = $this->request->getPost('login_id');  // Username atau email
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        // Cari user berdasarkan username atau email
        $user = $userModel->getUserByLogin($login);

        // Jika user tidak ditemukan, redirect dengan pesan error
        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username/email tidak ditemukan.');
        }

        // Verifikasi password menggunakan password_verify (lebih aman daripada plain text)
        if (!password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Password salah.');
        }

        // Cek apakah akun user aktif (status = 1)
        if ($user['status'] != 1) {
            return redirect()->back()->withInput()->with('error', 'Akun Anda tidak aktif.');
        }

        // Validasi khusus: admin_bidang HARUS memiliki kebidangan
        if ($user['role'] === 'admin_bidang' && (empty($user['kebidangan']) || $user['kebidangan'] === null)) {
            return redirect()->back()->withInput()->with('error', 'Admin Bidang harus memiliki kebidangan.');
        }

        // Set session data (menyimpan info user di session untuk authenticated request)
        session()->set([
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'role'       => $user['role'],              // admin, admin_bidang, atau user
            'kebidangan' => $user['kebidangan'],         // Bidang untuk admin_bidang
            'isLoggedIn' => true                         // Flag login status
        ]);
        session()->regenerate(); // Regenerate session ID untuk keamanan
        session()->set('userPhoto', $user['photo'] ?? null);           // Foto profil
        session()->set('nama_user', $user['nama_user']);               // Nama lengkap

        // Redirect ke dashboard sesuai role user
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin');
        } elseif ($user['role'] === 'admin_bidang') {
            return redirect()->to('/admin-bidang');
        } 
    }


    /**
     * forgot() - Halaman Lupa Password
     * 
     * Menangani proses lupa password:
     * 1. Tampilkan form untuk input email
     * 2. Validasi email user ada di database
     * 3. Generate token reset password
     * 4. Simpan token ke tabel password_reset dengan expiry 1 jam
     * 5. Kirim email dengan link reset
     * 
     * @return string|redirect View form atau redirect dengan pesan
     */
    public function forgot()
    {
        // Jika method POST (form disubmit)
        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            // Cari user berdasarkan email
            $user = model('App\Models\UserModel')->where('email', $email)->first();
            
            // Jika email tidak ditemukan
            if (!$user) {
                return redirect()->back()->with('error', 'Email tidak ditemukan.');
            }
            
            // Generate token random untuk reset password
            $token = bin2hex(random_bytes(32));
            
            // Simpan token ke database dengan expiry time 1 jam
            model('App\Models\PasswordResetModel')->insert([
                'email' => $email,
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))
            ]);
            
            // Siapkan email service
            $emailService = \Config\Services::email();
            $resetLink = base_url("auth/reset?token=$token");
            
            // Setup email
            $emailService->setTo($email);
            $emailService->setFrom(env('email.fromEmail'), env('email.fromName'));
            $emailService->setSubject('Reset Password LaporDAH');
            $emailService->setMessage("Klik link berikut untuk reset password Anda: <a href='$resetLink'>$resetLink</a>");
            $emailService->setMailType('html');
            
            // Kirim email
            if ($emailService->send()) {
                return redirect()->to('/auth/forgot')->with('success', 'Link reset password telah dikirim ke email Anda.');
            } else {
                return redirect()->to('/auth/forgot')->with('error', 'Gagal mengirim email. Silakan coba lagi.');
            }
        }
        
        // Jika method GET, tampilkan form forgot password
        return view('auth/forgot');
    }

    /**
     * reset() - Proses Reset Password
     * 
     * Menangani reset password dengan token:
     * 1. Validasi token valid & belum kadaluarsa
     * 2. Tampilkan form untuk input password baru
     * 3. Validasi password & konfirmasi password cocok
     * 4. Update password user di database
     * 5. Hapus token dari database
     * 
     * @return string|redirect View form atau redirect dengan pesan
     */
    public function reset()
    {
        // Ambil token dari URL parameter
        $token = $this->request->getGet('token');
        
        // Cari token di database
        $reset = model('App\Models\PasswordResetModel')->where('token', $token)->first();
        
        // Validasi token: pastikan ada & belum kadaluarsa
        if (!$reset || strtotime($reset['expires_at']) < time()) {
            return redirect()->to('auth/login')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }
        
        // Jika method POST (form disubmit)
        if ($this->request->getMethod() === 'post') {
            // Ambil password baru dari form
            $password = $this->request->getPost('password');
            $confirm = $this->request->getPost('password_confirm');
            
            // Validasi password & konfirmasi cocok
            if ($password !== $confirm) {
                return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
            }
            
            // Cari user berdasarkan email dari reset token
            $user = model('App\Models\UserModel')->where('email', $reset['email'])->first();
            
            // Update password user dengan hash password baru
            model('App\Models\UserModel')->update($user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
            
            // Hapus token dari database (agar tidak bisa digunakan lagi)
            model('App\Models\PasswordResetModel')->where('token', $token)->delete();
            
            return redirect()->to('auth/login')->with('success', 'Password berhasil direset. Silakan login.');
        }
        
        // Jika method GET, tampilkan form reset password
        return view('auth/reset', ['token' => $token]);
    }
    
    /**
     * logout() - Logout User
     * 
     * Menghapus session user dan logout dari aplikasi
     * Redirect ke halaman login
     */
    // Logout
    public function logout()
    {
        // Destroy session (hapus semua data session user)
        session()->destroy();
        // Redirect ke halaman login dengan pesan success
        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }
}