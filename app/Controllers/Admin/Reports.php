<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class Reports extends BaseController
{
    private function getTickets()
    {
        $db = \Config\Database::connect();
        $dateFrom = $this->request->getGet('f-from');
        $dateTo = $this->request->getGet('f-to');
        $where = "1=1";
        if (!empty($dateFrom))
            $where .= " AND t.created_at >= " . $db->escape($dateFrom . ' 00:00:00');
        if (!empty($dateTo))
            $where .= " AND t.created_at <= " . $db->escape($dateTo . ' 23:59:59');
        $query = "SELECT t.id, t.title, t.status, t.priority, t.description, t.drive_link, t.location,
                    u.name as reporter_name, d.name as dept_name, c.name as cat_name, t.created_at
                  FROM tickets t
                  LEFT JOIN users u ON t.reporter_id = u.id
                  LEFT JOIN departments d ON t.dept_id = d.id
                  LEFT JOIN categories c ON t.cat_id  = c.id
                  WHERE $where ORDER BY t.created_at DESC";
        return $db->query($query)->getResultArray();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $dateFrom = $this->request->getGet('f-from');
        $dateTo = $this->request->getGet('f-to');
        $where = "1=1";
        if (!empty($dateFrom))
            $where .= " AND t.created_at >= " . $db->escape($dateFrom . ' 00:00:00');
        if (!empty($dateTo))
            $where .= " AND t.created_at <= " . $db->escape($dateTo . ' 23:59:59');
        $stats = $db->query("SELECT COUNT(*) as total,
            SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets,
            SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved
            FROM tickets t WHERE $where")->getRowArray();
        $avgRating = $db->query("SELECT AVG(rating) as r FROM ticket_ratings")->getRow()->r;
        $pager = \Config\Services::pager();
        $page = $this->request->getVar('page') ? (int)$this->request->getVar('page') : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $ticketsQuery = "SELECT t.id, t.title, t.status, t.priority, t.description, t.drive_link, t.location,
                    u.name as reporter_name, d.name as dept_name, c.name as cat_name, t.created_at
                  FROM tickets t
                  LEFT JOIN users u ON t.reporter_id = u.id
                  LEFT JOIN departments d ON t.dept_id = d.id
                  LEFT JOIN categories c ON t.cat_id  = c.id
                  WHERE $where ORDER BY t.created_at DESC LIMIT $perPage OFFSET $offset";

        $data = [
            'pageTitle' => 'Laporan & Statistik',
            'activePage' => 'report',
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'stats' => $stats,
            'avgRating' => $avgRating,
            'tickets' => $db->query($ticketsQuery)->getResultArray(),
            'pager_links' => $stats['total'] > 0 ? $pager->makeLinks($page, $perPage, $stats['total']) : '',
        ];
        return view('admin/reports/index', $data);
    }

    public function excel()
    {
        if (!has_permission('Ekspor Data')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses Ditolak. Anda tidak memiliki izin untuk mengekspor data.');
        }

        $db = \Config\Database::connect();
        $dateFrom = $this->request->getGet('f-from');
        $dateTo = $this->request->getGet('f-to');
        $where = "1=1";
        if (!empty($dateFrom))
            $where .= " AND t.created_at >= " . $db->escape($dateFrom . ' 00:00:00');
        if (!empty($dateTo))
            $where .= " AND t.created_at <= " . $db->escape($dateTo . ' 23:59:59');
        $stats = $db->query("SELECT COUNT(*) as total,
            SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets,
            SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved
            FROM tickets t WHERE $where")->getRowArray();
        $avgRating = $db->query("SELECT AVG(rating) as r FROM ticket_ratings")->getRow()->r;

        $tickets = $this->getTickets();
        $filename = "Helpdesk_Laporan_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");

        echo '<h3>Ringkasan Laporan</h3>';
        echo '<table border="1">';
        echo '<tr><td style="font-weight:bold;">Total Tiket</td><td>' . ($stats['total'] ?? 0) . '</td></tr>';
        echo '<tr><td style="font-weight:bold;">Tiket Open</td><td>' . ($stats['open_tickets'] ?? 0) . '</td></tr>';
        echo '<tr><td style="font-weight:bold;">Tiket In Progress</td><td>' . ($stats['in_progress'] ?? 0) . '</td></tr>';
        echo '<tr><td style="font-weight:bold;">Tiket Solved/Closed</td><td>' . ($stats['solved'] ?? 0) . '</td></tr>';
        echo '<tr><td style="font-weight:bold;">Rata-rata Rating</td><td>' . number_format($avgRating ?? 0, 1) . ' / 5</td></tr>';
        echo '</table><br/>';

        echo '<h3>Data Tiket</h3>';
        echo '<table border="1">
<tr style="background:#1e3a5f;color:white;font-weight:bold">
  <td>ID Tiket</td><td>Judul</td><td>Prioritas</td><td>Status</td>
  <td>Pengaju</td><td>Departemen</td><td>Kategori</td><td>Deskripsi</td><td>Link Dokumentasi</td><td>Tanggal Dibuat</td>
</tr>';
        if (empty($tickets)) {
            echo '<tr><td colspan="8">Tidak ada data tiket.</td></tr>';
        }
        else {
            foreach ($tickets as $r) {
                echo '<tr><td>' . htmlspecialchars($r['id'] ?? '') . '</td>
                      <td>' . htmlspecialchars($r['title'] ?? '') . '</td>
                      <td>' . htmlspecialchars($r['priority'] ?? '') . '</td>
                      <td>' . htmlspecialchars($r['status'] ?? '') . '</td>
                      <td>' . htmlspecialchars($r['reporter_name'] ?? '') . '</td>
                      <td>' . htmlspecialchars($r['dept_name'] ?? '-') . '</td>
                      <td>' . htmlspecialchars($r['cat_name'] ?? '') . '</td>
                      <td>' . htmlspecialchars($r['description'] ?? '') . '</td>
                      <td>' . htmlspecialchars($r['drive_link'] ?? '') . '</td>
                      <td>' . htmlspecialchars($r['created_at'] ?? '') . '</td></tr>';
            }
        }
        echo '</table>';
        exit;
    }

    public function export()
    {
        return $this->excel();
    }

    public function pdf()
    {
        if (!has_permission('Ekspor Data')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses Ditolak. Anda tidak memiliki izin untuk mengekspor data.');
        }

        $db = \Config\Database::connect();
        $dateFromRaw = $this->request->getGet('f-from');
        $dateToRaw = $this->request->getGet('f-to');
        $where = "1=1";
        if (!empty($dateFromRaw))
            $where .= " AND t.created_at >= " . $db->escape($dateFromRaw . ' 00:00:00');
        if (!empty($dateToRaw))
            $where .= " AND t.created_at <= " . $db->escape($dateToRaw . ' 23:59:59');
        $stats = $db->query("SELECT COUNT(*) as total,
            SUM(CASE WHEN status='OPEN' THEN 1 ELSE 0 END) as open_tickets,
            SUM(CASE WHEN status='IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as solved
            FROM tickets t WHERE $where")->getRowArray();
        $avgRating = $db->query("SELECT AVG(rating) as r FROM ticket_ratings")->getRow()->r;

        $tickets = $this->getTickets();
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
              <td>' . htmlspecialchars($t['cat_name'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['description'] ?? '') . '</td>
              <td>' . htmlspecialchars($t['drive_link'] ?? '') . '</td>
              <td>' . date('d/m/Y', strtotime($t['created_at'])) . '</td>
            </tr>';
        }
        if (empty($tickets))
            $rows = '<tr><td colspan="7" style="text-align:center;color:#999">Tidak ada data.</td></tr>';
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
    <tr><td><strong>Rata-rata Rating</strong></td><td>' . number_format($avgRating ?? 0, 1) . ' / 5</td></tr>
</table>
<table><tr><th>ID</th><th>Judul</th><th>Prioritas</th><th>Status</th><th>Pengaju</th><th>Kategori</th><th>Deskripsi</th><th>Link Dokumentasi</th><th>Tanggal</th></tr>
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
            return redirect()->to('/admin/reports')->with('error', 'Akses Ditolak. Anda tidak memiliki izin untuk mencetak data.');
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
