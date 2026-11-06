<?php 
/**
 * ========================================
 * USER MODEL
 * ========================================
 * 
 * Model untuk mengelola data user aplikasi LaporDAH:
 * - User biasa (role: 'user')
 * - Admin (role: 'admin')
 * - Admin Bidang (role: 'admin_bidang')
 * 
 * Fitur:
 * - CRUD user (Create, Read, Update, Delete)
 * - Validasi username & email unique
 * - Helper methods untuk query user
 * 
 * @package App\Models
 * @author LaporDAH Team
 */

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // ============ DATABASE CONFIGURATION ============
    protected $table = 'users';                 // Nama tabel di database
    protected $primaryKey = 'id';               // Primary key
    protected $returnType = 'array';            // Return type: array
    
    // Field yang boleh di-insert/update
    protected $allowedFields = [
        'nama_user',            // Nama lengkap user
        'username',             // Username untuk login
        'email',                // Email user
        'password_hash',        // Password yang di-hash
        'role',                 // Role: user, admin, admin_bidang
        'status',               // Status: 1=aktif, 0=nonaktif
        'kebidangan',           // Bidang/departemen (khusus admin_bidang)
        'created_at',
        'updated_at'
    ];
    
    // ============ TIMESTAMPS ============
    protected $useTimestamps = true;
    protected $createdField = 'created_at';     // Field created_at otomatis terisi
    protected $updatedField = 'updated_at';     // Field updated_at otomatis terisi
    
    // ============ VALIDASI ============
    // Validasi rules untuk insert/update
    protected $validationRules = [
        'username' => 'required|min_length[4]',     // Username minimal 4 karakter
        'email' => 'required|valid_email',          // Email harus valid format
        // password_hash tidak wajib di validasi di model ini
    ];

    /**
     * getUserByLogin() - Cari User Berdasarkan Username atau Email
     * 
     * Helper method untuk mencari user saat login
     * Bisa mencari berdasarkan username OR email
     * 
     * @param string $login Username atau email
     * @return array User data jika ditemukan, null jika tidak
     */
    public function getUserByLogin($login)
    {
        // Cari user berdasarkan username atau email
        return $this->where('username', $login)
                    ->orWhere('email', $login)
                    ->first();
    }
    
    /**
     * getKebidanganById() - Ambil Kebidangan User Berdasarkan ID
     * 
     * Helper method untuk mendapatkan kebidangan/bidang user
     * Digunakan untuk admin_bidang yang harus terikat ke bidang tertentu
     * 
     * @param int $userId ID user
     * @return string Kebidangan user
     */
    public function getKebidanganById($userId)
    {
        // Select kebidangan field, cari berdasarkan ID, dan return value
        return $this->select('kebidangan')
                    ->where('id', $userId)
                    ->get()
                    ->getRow('kebidangan');
    }
}