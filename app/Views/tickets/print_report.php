<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Data Tiket - <?= esc($user['name']) ?></title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; background: white; color: #333; }
.header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #1e3a5f; }
.header h1 { font-size: 18px; font-weight: bold; color: #1e3a5f; margin-bottom: 5px; text-transform: uppercase; }
.header p { font-size: 11px; color: #555; }
.info-table { width: 100%; margin-bottom: 15px; font-size: 11px; border: none; }
.info-table td { padding: 3px 0; border: none; }
.info-table td.label { width: 120px; font-weight: bold; color: #555; }
table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
table.data-table thead tr { background: #1e3a5f; color: white; }
table.data-table th { padding: 10px 8px; text-align: left; font-size: 11px; font-weight: 600; border: 1px solid #1e3a5f; }
table.data-table td { padding: 8px 8px; border: 1px solid #ddd; font-size: 11px; vertical-align: top; }
table.data-table tr:nth-child(even) td { background: #f8f9fa; }
.no-data { text-align: center; padding: 30px; color: #999; font-style: italic; }
.print-btn { text-align: right; margin-bottom: 15px; }
.print-btn button { padding: 8px 20px; background: #1e3a5f; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; transition: background 0.2s; }
.print-btn button:hover { background: #152c4a; }
@media print {
    .print-btn { display: none; }
    body { padding: 10px; }
    table.data-table th { background: #1e3a5f !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>
<div class="print-btn">
    <button onclick="window.print()">&#128438; Cetak Laporan</button>
</div>
<div class="header">
    <h1>Laporan Tiket Gangguan</h1>
    <p>Sistem Informasi Helpdesk Pusim</p>
</div>

<table class="info-table">
    <tr>
        <td class="label">Nama Pengguna</td>
        <td>: <?= esc($user['name']) ?></td>
    </tr>
    <tr>
        <td class="label">Periode Laporan</td>
        <td>: <?= !empty($dateFrom) ? esc($dateFrom) : '-' ?> s/d <?= !empty($dateTo) ? esc($dateTo) : '-' ?></td>
    </tr>
    <tr>
        <td class="label">Tanggal Cetak</td>
        <td>: <?= date('d/m/Y H:i') ?> WIB</td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 80px;">ID Tiket</th>
            <th>Judul</th>
            <th style="width: 100px;">Kategori</th>
            <th style="width: 80px; text-align: center;">Prioritas</th>
            <th style="width: 90px; text-align: center;">Status</th>
            <th>Pemohon</th>
            <th>Teknisi</th>
            <th style="width: 110px;">Tanggal Dibuat</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tickets)): ?>
        <tr><td colspan="8" class="no-data">Tidak ada data tiket untuk periode ini.</td></tr>
        <?php else: ?>
        <?php foreach ($tickets as $t): ?>
        <tr>
            <td style="font-family: monospace; font-weight: bold;"><?= esc($t['id']) ?></td>
            <td><?= esc($t['title']) ?></td>
            <td><?= esc($t['cat_name']) ?></td>
            <td style="text-align: center;"><?= esc($t['priority']) ?></td>
            <td style="text-align: center;"><?= esc($t['status']) ?></td>
            <td><?= esc($t['requester_name'] ?? '') ?></td>
            <td><?= esc($t['assigned_names'] ?: ($t['assigned_name'] ?? '-')) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>
