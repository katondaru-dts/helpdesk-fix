<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Data Tiket</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; background: white; }
.header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #333; }
.header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
.header p { font-size: 11px; color: #555; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
thead tr { background: #1e3a5f; color: white; }
th { padding: 8px 6px; text-align: left; font-size: 11px; }
td { padding: 7px 6px; border-bottom: 1px solid #ddd; font-size: 11px; }
tr:nth-child(even) td { background: #f8f9fa; }
.no-data { text-align: center; padding: 30px; color: #999; }
.print-btn { text-align: right; margin-bottom: 15px; }
.print-btn button { padding: 8px 20px; background: #1e3a5f; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; }
@media print {
    .print-btn { display: none; }
    body { padding: 10px; }
}
</style>
</head>
<body>
<div class="print-btn">
    <button onclick="window.print()">&#128438; Cetak Halaman Ini</button>
</div>
<div class="header">
    <h1>LAPORAN DATA TIKET HELPDESK</h1>
    <p>Periode: <?= esc($dateFrom) ?> s/d <?= esc($dateTo) ?> &nbsp;|&nbsp; Dicetak: <?= date('d/m/Y H:i') ?></p>
</div>
<table>
    <thead>
        <tr>
            <th>ID Tiket</th>
            <th>Judul</th>
            <th>Prioritas</th>
            <th>Status</th>
            <th>Pengaju</th>
            <th>Departemen</th>
            <th>Kategori</th>
            <th>Tanggal Dibuat</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tickets)): ?>
        <tr><td colspan="8" class="no-data">Tidak ada data tiket untuk periode ini.</td></tr>
        <?php else: ?>
        <?php foreach ($tickets as $t): ?>
        <tr>
            <td><?= esc($t['id']) ?></td>
            <td><?= esc($t['title']) ?></td>
            <td><?= esc($t['priority']) ?></td>
            <td><?= esc($t['status']) ?></td>
            <td><?= esc($t['reporter_name']) ?></td>
            <td><?= esc($t['dept_name'] ?? '-') ?></td>
            <td><?= esc($t['cat_name']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>

