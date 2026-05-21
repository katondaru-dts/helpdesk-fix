<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

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

    private function getTickets()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tickets t')
            ->select('t.id, t.title, t.status, t.priority, t.description, t.drive_link, t.location, t.requester_name,
                      u.name as reporter_name, d.name as dept_name, c.name as cat_name, t.created_at')
            ->join('users u', 't.reporter_id = u.id', 'left')
            ->join('departments d', 't.dept_id = d.id', 'left')
            ->join('categories c', 't.cat_id = c.id', 'left')
            ->orderBy('t.created_at', 'DESC');
        return $this->applyDateFilter($builder)->get()->getResultArray();
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
        $this->applyDateFilter($statsBuilder);
        $stats = $statsBuilder->get()->getRowArray();

        $pager = \Config\Services::pager();
        $page = max(1, (int) $this->request->getVar('page'));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $ticketsBuilder = $db->table('tickets t')
            ->select('t.id, t.title, t.status, t.priority, t.description, t.drive_link, t.location, t.requester_name,
                      u.name as reporter_name, d.name as dept_name, c.name as cat_name, t.created_at')
            ->join('users u', 't.reporter_id = u.id', 'left')
            ->join('departments d', 't.dept_id = d.id', 'left')
            ->join('categories c', 't.cat_id = c.id', 'left')
            ->orderBy('t.created_at', 'DESC');
        $this->applyDateFilter($ticketsBuilder);
        $tickets = $ticketsBuilder->get($perPage, $offset)->getResultArray();

        $data = [
            'pageTitle' => 'Laporan & Statistik',
            'activePage' => 'report',
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'stats' => $stats,
            'tickets' => $tickets,
            'pager_links' => ($stats['total'] ?? 0) > 0 ? $pager->makeLinks($page, $perPage, $stats['total']) : '',
        ];
        return view('admin/reports/index', $data);
    }

    public function excel()
    {
        if (!has_permission('Ekspor Data')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses Ditolak.');
        }

        $db = \Config\Database::connect();
        $statsBuilder = $db->table('tickets t')
            ->select("COUNT(*) as total,
                      SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets,
                      SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
                      SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved");
        $this->applyDateFilter($statsBuilder);
        $stats = $statsBuilder->get()->getRowArray();

        $tickets = $this->getTickets();
        $filename = "Helpdesk_Laporan_" . date('Ymd_His') . ".xls";

        return response()
            ->setHeader('Content-Type', 'application/vnd.ms-excel')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody(view('admin/reports/export_excel', [
                'stats' => $stats,
                'tickets' => $tickets
            ]));
    }

    public function export()
    {
        return $this->excel();
    }

    public function pdf()
    {
        if (!has_permission('Ekspor Data')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses Ditolak.');
        }

        $db = \Config\Database::connect();
        $statsBuilder = $db->table('tickets t')
            ->select("COUNT(*) as total,
                      SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets,
                      SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
                      SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved");
        $this->applyDateFilter($statsBuilder);
        $stats = $statsBuilder->get()->getRowArray();

        $tickets = $this->getTickets();
        $dateFromRaw = $this->request->getGet('f-from');
        $dateToRaw = $this->request->getGet('f-to');
        $dateFrom = $dateFromRaw ?: 'Semua';
        $dateTo = $dateToRaw ?: 'Semua';
        $rows = '';
        foreach ($tickets as $t) {
            $rows .= '<tr>
              <td>' . htmlspecialchars($t['id'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['title'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['priority'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['status'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['reporter_name'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['requester_name'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['location'] ?? '-') . '</td>
              <td>' . htmlspecialchars($t['description'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['drive_link'] ?? '') . '</td>
              <td>' . date('d/m/Y', strtotime($t['created_at'])) . '</td>
            </tr>';
        }
        if (empty($tickets))
            $rows = '<tr><td colspan="10" style="text-align:center;color:#999">Tidak ada data.</td></tr>';
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>body{font-family:DejaVu Sans,Arial,sans-serif;font-size:10px;margin:15px}
h2{text-align:center;font-size:14px;margin-bottom:5px}.sub{text-align:center;font-size:10px;color:#666;margin-bottom:15px}
.summary{width:50%;margin-bottom:20px;border-collapse:collapse;} .summary td{padding:4px;border:1px solid #ddd;}
table{width:100%;border-collapse:collapse}th{background:#1e3a5f;color:white;padding:7px 5px;text-align:left;font-size:10px}
td{padding:6px 5px;border-bottom:1px solid #e0e0e0;font-size:9px}tr:nth-child(even) td{background:#f8f9fa}</style>
</head><body>
<h2>LAPORAN DATA TIKET</h2>
<div class="sub">Periode: ' . htmlspecialchars($dateFrom) . ' s/d ' . htmlspecialchars($dateTo) . ' | Dicetak: ' . date('d/m/Y H:i') . '</div>
<table class="summary">
    <tr><td><strong>Total Tiket</strong></td><td>' . ($stats['total'] ?? 0) . '</td></tr>
    <tr><td><strong>Tiket Open</strong></td><td>' . ($stats['open_tickets'] ?? 0) . '</td></tr>
    <tr><td><strong>Tiket In Progress</strong></td><td>' . ($stats['in_progress'] ?? 0) . '</td></tr>
    <tr><td><strong>Tiket Solved/Closed</strong></td><td>' . ($stats['solved'] ?? 0) . '</td></tr>
</table>
<table><tr><th>ID</th><th>Judul Tiket</th><th>Prioritas</th><th>Status</th><th>Pelapor</th><th>Pemohon</th><th>Lokasi Gangguan</th><th>Deskripsi</th><th>Link</th><th>Tanggal</th></tr>
' . $rows . '</table></body></html>';
        $opts = new Options();
        $opts->set('isRemoteEnabled', true);
        $opts->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($opts);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Helpdesk_Tiket_" . date('Ymd_His') . ".pdf", ['Attachment' => 1]);
        exit;
    }

    public function printReport()
    {
        if (!has_permission('Ekspor Data')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses Ditolak.');
        }

        $data = [
            'tickets' => $this->getTickets(),
            'dateFrom' => $this->request->getGet('f-from') ?: 'Semua',
            'dateTo' => $this->request->getGet('f-to') ?: 'Semua',
        ];
        return view('admin/reports/print_report', $data);
    }

    public function updateLink($id)
    {
        $db = \Config\Database::connect();
        $link = $this->request->getPost('drive_link');
        $db->table('tickets')->where('id', $id)->update(['drive_link' => $link]);
        return redirect()->back()->with('success', 'Link Dokumentasi berhasil diperbarui.');
    }
}
