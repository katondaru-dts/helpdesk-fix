<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class AuditLogs extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $query = "
            SELECT a.*, u.name as user_name, u.email as user_email
            FROM audit_logs a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
        ";
        
        // Simple pagination approach for raw query
        $pager = \Config\Services::pager();
        $page = $this->request->getVar('page') ? (int)$this->request->getVar('page') : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = $db->query("SELECT COUNT(*) as c FROM audit_logs")->getRow()->c;
        $logs = $db->query($query . " LIMIT $perPage OFFSET $offset")->getResultArray();

        $data = [
            'pageTitle'  => 'Audit Logs - Helpdesk',
            'activePage' => 'audit-logs',
            'logs'       => $logs,
            'pager_links'=> $pager->makeLinks($page, $perPage, $total)
        ];

        return view('admin/audit_logs/index', $data);
    }
}
