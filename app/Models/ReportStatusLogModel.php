<?php namespace App\Models;

use CodeIgniter\Model;

class ReportStatusLogModel extends Model
{
    protected $table = 'report_status_log';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'report_id', 'tracking_code', 'report_source', 'status', 'note', 'file', 'updated_by', 'updated_at','kebidangan'
    ];
    public $timestamps = true;
}