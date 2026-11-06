<?php namespace App\Models;

use CodeIgniter\Model;

class VisitorModel extends Model
{
    protected $table = 'visitor_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'ip_address',
        'user_agent',
        'page_visited',
        'visit_time'
    ];

    protected $useTimestamps = false;

    public function getTotalVisitors()
    {
        return $this->countAll();
    }
}