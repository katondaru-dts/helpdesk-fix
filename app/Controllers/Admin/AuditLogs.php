<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class AuditLogs extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $pager = \Config\Services::pager();
        $page = max(1, (int) $this->request->getVar('page'));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = $db->table('audit_logs')->countAll();

        $logs = $db->table('audit_logs a')
            ->select('a.*, u.name as user_name, u.email as user_email')
            ->join('users u', 'a.user_id = u.id')
            ->orderBy('a.created_at', 'DESC')
            ->get($perPage, $offset)
            ->getResultArray();

        $data = [
            'pageTitle'  => 'Audit Logs - Helpdesk',
            'activePage' => 'audit-logs',
            'logs'       => $logs,
            'pager_links'=> $pager->makeLinks($page, $perPage, $total)
        ];

        return view('admin/audit_logs/index', $data);
    }
}
