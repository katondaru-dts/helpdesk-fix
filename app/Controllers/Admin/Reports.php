<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Reports extends BaseController
{
    private function applyDateFilter($builder)
    {
        $dateFrom = $this->request->getGet('f-from');
        $dateTo = $this->request->getGet('f-to');
        if (!empty($dateFrom))
            $builder->where('t.created_at >=', $dateFrom . ' 00:00:00');
        if (!empty($dateTo))
            $builder->where('t.created_at <=', $dateTo . ' 23:59:59');
        return $builder;
    }

    private function applyUserFilter($builder)
    {
        helper('auth');
        if (!is_staff() && !has_permission('Lihat Laporan')) {
            $builder->where('t.reporter_id', session()->get('id'));
        }
        return $builder;
    }

    private function resolvePhotoUrl(?string $value): string
    {
        if (empty($value))
            return '';
        helper('app');
        return resolve_minio_url($value) ?? '';
    }

    private function getTickets()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tickets t')
            ->select('t.id, t.title, t.status, t.priority, t.description, t.drive_link, t.location, t.requester_name,
                      u.name as reporter_name, tech.name as teknisi_name, d.name as dept_name, c.name as cat_name,
                      t.photo, t.photo2, t.created_at,
                      (SELECT GROUP_CONCAT(usr.name ORDER BY ta.assigned_at ASC SEPARATOR ", ")
                       FROM ticket_assignees ta
                       JOIN users usr ON ta.user_id = usr.id
                       WHERE ta.ticket_id = t.id) as teknisi_names')
            ->join('users u', 't.reporter_id = u.id', 'left')
            ->join('users tech', 't.assigned_to = tech.id', 'left')
            ->join('departments d', 't.dept_id = d.id', 'left')
            ->join('categories c', 't.cat_id = c.id', 'left')
            ->orderBy('t.created_at', 'DESC');
        $this->applyUserFilter($builder);
        $tickets = $this->applyDateFilter($builder)->get()->getResultArray();

        foreach ($tickets as &$t) {
            $photoUrls = [];
            if (!empty($t['photo']))
                $photoUrls[] = $this->resolvePhotoUrl($t['photo']);
            if (!empty($t['photo2']))
                $photoUrls[] = $this->resolvePhotoUrl($t['photo2']);

            if (empty($photoUrls)) {
                $msgPhotos = $db->table('ticket_messages')
                    ->where('ticket_id', $t['id'])
                    ->where('photo IS NOT NULL')
                    ->where('photo !=', '')
                    ->orderBy('sent_at', 'DESC')
                    ->get()
                    ->getResultArray();

                foreach ($msgPhotos as $mp) {
                    $u = $this->resolvePhotoUrl($mp['photo']);
                    if ($u)
                        $photoUrls[] = $u;
                }
            }

            $photoUrls = array_unique(array_filter($photoUrls));
            $t['display_link'] = !empty($t['drive_link']) ? $t['drive_link'] : implode("\n", $photoUrls);
        }
        unset($t);
        return $tickets;
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $dateFrom = $this->request->getGet('f-from');
        $dateTo = $this->request->getGet('f-to');

        $statsBuilder = $db->table('tickets t')
            ->select("COUNT(*) as total,
                      SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets,
                      SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
                      SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved");
        $this->applyUserFilter($statsBuilder);
        $this->applyDateFilter($statsBuilder);
        $stats = $statsBuilder->get()->getRowArray();

        $session = session();
        $perPage = (int) ($this->request->getGet('per_page') ?: ($session->get('report_per_page') ?: 10));
        if (!in_array($perPage, [10, 20, 30, 40, 50, 100, 500])) {
            $perPage = 10;
        }
        $session->set('report_per_page', $perPage);

        $pager = \Config\Services::pager();
        $page = max(1, (int) $this->request->getVar('page'));
        $offset = ($page - 1) * $perPage;

        $ticketsBuilder = $db->table('tickets t')
            ->select('t.id, t.title, t.status, t.priority, t.description, t.drive_link, t.location, t.requester_name,
                      u.name as reporter_name, tech.name as teknisi_name, d.name as dept_name, c.name as cat_name,
                      t.photo, t.photo2, t.created_at,
                      (SELECT GROUP_CONCAT(usr.name ORDER BY ta.assigned_at ASC SEPARATOR ", ")
                       FROM ticket_assignees ta
                       JOIN users usr ON ta.user_id = usr.id
                       WHERE ta.ticket_id = t.id) as teknisi_names')
            ->join('users u', 't.reporter_id = u.id', 'left')
            ->join('users tech', 't.assigned_to = tech.id', 'left')
            ->join('departments d', 't.dept_id = d.id', 'left')
            ->join('categories c', 't.cat_id = c.id', 'left')
            ->orderBy('t.created_at', 'DESC');
        $this->applyUserFilter($ticketsBuilder);
        $this->applyDateFilter($ticketsBuilder);
        $tickets = $ticketsBuilder->get($perPage, $offset)->getResultArray();

        foreach ($tickets as &$t) {
            $photoUrls = [];
            if (!empty($t['photo']))
                $photoUrls[] = $this->resolvePhotoUrl($t['photo']);
            if (!empty($t['photo2']))
                $photoUrls[] = $this->resolvePhotoUrl($t['photo2']);

            if (empty($photoUrls)) {
                $msgPhotos = $db->table('ticket_messages')
                    ->where('ticket_id', $t['id'])
                    ->where('photo IS NOT NULL')
                    ->where('photo !=', '')
                    ->orderBy('sent_at', 'DESC')
                    ->get()
                    ->getResultArray();

                foreach ($msgPhotos as $mp) {
                    $u = $this->resolvePhotoUrl($mp['photo']);
                    if ($u)
                        $photoUrls[] = $u;
                }
            }

            $photoUrls = array_unique(array_filter($photoUrls));

            // LOGIKA REFRESH: Jika drive_link berisi link MinIO, kita refresh agar tidak expired
            if (!empty($t['drive_link']) && str_contains($t['drive_link'], 'helpdesk-minio')) {
                $urlParts = parse_url($t['drive_link']);
                $path = ltrim($urlParts['path'] ?? '', '/');
                $path = preg_replace('/^helpdesk\//', '', $path);
                if (!empty($path)) {
                    $freshUrl = resolve_minio_url($path);
                    if ($freshUrl)
                        $t['drive_link'] = $freshUrl;
                }
            }

            $t['display_link'] = !empty($t['drive_link']) ? $t['drive_link'] : implode("\n", $photoUrls);
        }
        unset($t);

        $data = [
            'pageTitle' => 'Laporan & Statistik',
            'activePage' => 'report',
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'stats' => $stats,
            'tickets' => $tickets,
            'pager_links' => ($stats['total'] ?? 0) > 0 ? $pager->makeLinks($page, $perPage, $stats['total']) : '',
            'perPage' => $perPage,
        ];
        return view('admin/reports/index', $data);
    }

    public function excel()
    {
        $db = \Config\Database::connect();
        $statsBuilder = $db->table('tickets t')->select("COUNT(*) as total, SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets, SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress, SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved");
        $this->applyUserFilter($statsBuilder);
        $this->applyDateFilter($statsBuilder);
        $stats = $statsBuilder->get()->getRowArray();
        $tickets = $this->getTickets();
        $filename = "Helpdesk_Laporan_" . date('Ymd_His') . ".xls";
        return response()->setHeader('Content-Type', 'application/vnd.ms-excel')->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')->setBody(view('admin/reports/export_excel', ['stats' => $stats, 'tickets' => $tickets]));
    }

    public function pdf()
    {
        $db = \Config\Database::connect();
        $statsBuilder = $db->table('tickets t')->select("COUNT(*) as total, SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets, SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress, SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved");
        $this->applyUserFilter($statsBuilder);
        $this->applyDateFilter($statsBuilder);
        $stats = $statsBuilder->get()->getRowArray();
        $tickets = $this->getTickets();
        return view('admin/reports/export_excel', ['stats' => $stats, 'tickets' => $tickets]);
    }

    public function printReport()
    {
        if (!has_permission('Cetak Laporan')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mencetak laporan.');
        }

        $db = \Config\Database::connect();
        $dateFrom = $this->request->getGet('f-from');
        $dateTo = $this->request->getGet('f-to');

        $statsBuilder = $db->table('tickets t')->select("COUNT(*) as total, SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets, SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress, SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved");
        $this->applyUserFilter($statsBuilder);
        $this->applyDateFilter($statsBuilder);
        $stats = $statsBuilder->get()->getRowArray();

        $tickets = $this->getTickets();

        return view('admin/reports/print_report', [
            'stats' => $stats,
            'tickets' => $tickets,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    public function updateLink($id)
    {
        $db = \Config\Database::connect();
        $link = $this->request->getPost('drive_link');
        $db->table('tickets')->where('id', $id)->update(['drive_link' => $link]);
        return redirect()->back()->with('success', 'Link Dokumentasi berhasil diperbarui.');
    }
}
