<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'action', 'target_table', 'target_id', 'details', 'ip_address', 'created_at'];
    protected $useTimestamps = true;
    protected $updatedField = '';
    
    public function logAction($action, $table, $targetId = null, $details = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $request = \Config\Services::request();
        
        $data = [
            'user_id' => session()->get('id') ?? 1,
            'action' => $action,
            'target_table' => $table,
            'target_id' => $targetId,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $builder->insert($data);
    }
}
