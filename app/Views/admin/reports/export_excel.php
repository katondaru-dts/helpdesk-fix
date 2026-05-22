<h3>Ringkasan Laporan</h3>
<table border="1">
    <tr><td style="font-weight:bold;">Total Tiket</td><td><?= $stats['total'] ?? 0 ?></td></tr>
    <tr><td style="font-weight:bold;">Tiket Open</td><td><?= $stats['open_tickets'] ?? 0 ?></td></tr>
    <tr><td style="font-weight:bold;">Tiket In Progress</td><td><?= $stats['in_progress'] ?? 0 ?></td></tr>
    <tr><td style="font-weight:bold;">Tiket Solved/Closed</td><td><?= $stats['solved'] ?? 0 ?></td></tr>
</table>
<br/>

<h3>Data Tiket</h3>
<table border="1">
    <tr style="background:#1e3a5f;color:white;font-weight:bold">
        <td>ID</td>
        <td>Judul Tiket</td>
        <td>Prioritas</td>
        <td>Status</td>
        <td>Pelapor</td>
        <td>Pemohon</td>
        <td>Teknisi</td>
        <td>Lokasi Gangguan</td>
        <td>Deskripsi</td>
        <td>Link Dokumentasi</td>
        <td>Tanggal</td>
    </tr>
    <?php if (empty($tickets)): ?>
        <tr><td colspan="11">Tidak ada data tiket.</td></tr>
    <?php else: ?>
        <?php foreach ($tickets as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= esc($r['title']) ?></td>
                <td><?= $r['priority'] ?></td>
                <td><?= $r['status'] ?></td>
                <td><?= esc($r['reporter_name']) ?></td>
                <td><?= esc($r['requester_name']) ?></td>
                <td><?= esc($r['teknisi_name'] ?? '-') ?></td>
                <td><?= esc($r['location'] ?? '-') ?></td>
                <td><?= esc($r['description']) ?></td>
                <td><?= esc($r['display_link'] ?? $r['drive_link'] ?? '-') ?></td>
                <td><?= $r['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
