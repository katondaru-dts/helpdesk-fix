const { execFileSync } = require('child_process');
const fs = require('fs');

// The full content of the reports index view - hardcoded to avoid Google Drive interference
const content = `<?= \\$this->extend('layouts/main') ?>
<?= \\$this->section('content') ?>
<style>
@media print { .sidebar,.topbar,.page-header,.filter-card,.no-print{display:none !important;} body{background:white !important;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #ccc;padding:6px;font-size:10px;} }
</style>
<div class="page-header no-print">
    <div><div class="page-header-title">Laporan &amp; Statistik</div><div class="page-header-sub">Ringkasan performa sistem helpdesk</div></div>
</div>
<div class="filter-card card mb-4 no-print" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px;">
    <form action="<?= base_url('admin/reports') ?>" method="GET" style="display:flex;gap:15px;align-items:flex-end">
        <div style="flex:1"><label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600;color:#374151">Dari Tanggal</label><input type="date" name="f-from" value="<?= esc(\\$dateFrom) ?>" class="form-control" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db"></div>
        <div style="flex:1"><label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600;color:#374151">Sampai Tanggal</label><input type="date" name="f-to" value="<?= esc(\\$dateTo) ?>" class="form-control" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db"></div>
        <div style="display:flex;gap:10px">
            <button type="submit" style="background:#3b82f6;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer">Filter</button>
            <a href="<?= base_url('admin/reports') ?>" style="background:white;color:#6b7280;border:1px solid #d1d5db;padding:10px 15px;border-radius:8px;font-weight:bold;cursor:pointer;text-decoration:none">Reset</a>
        </div>
    </form>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px">
    <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);display:flex;align-items:center;gap:15px"><div style="width:48px;height:48px;border-radius:12px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center;font-size:24px"><i class="bi bi-ticket-detailed"></i></div><div><div style="font-size:13px;color:#6b7280;font-weight:600">Total Tiket</div><div style="font-size:24px;font-weight:bold;color:#111827"><?= number_format(\\$stats['total'] ?? 0) ?></div></div></div>
    <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);display:flex;align-items:center;gap:15px"><div style="width:48px;height:48px;border-radius:12px;background:#fefce8;color:#eab308;display:flex;align-items:center;justify-content:center;font-size:24px"><i class="bi bi-clock-history"></i></div><div><div style="font-size:13px;color:#6b7280;font-weight:600">Dalam Proses</div><div style="font-size:24px;font-weight:bold;color:#111827"><?= number_format(\\$stats['in_progress'] ?? 0) ?></div></div></div>
    <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);display:flex;align-items:center;gap:15px"><div style="width:48px;height:48px;border-radius:12px;background:#d1fae5;color:#10b981;display:flex;align-items:center;justify-content:center;font-size:24px"><i class="bi bi-check-circle"></i></div><div><div style="font-size:13px;color:#6b7280;font-weight:600">Selesai</div><div style="font-size:24px;font-weight:bold;color:#111827"><?= number_format(\\$stats['solved'] ?? 0) ?></div></div></div>
    <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);display:flex;align-items:center;gap:15px"><div style="width:48px;height:48px;border-radius:12px;background:#fff1f2;color:#f43f5e;display:flex;align-items:center;justify-content:center;font-size:24px"><i class="bi bi-star"></i></div><div><div style="font-size:13px;color:#6b7280;font-weight:600">Avg Rating</div><div style="font-size:24px;font-weight:bold;color:#111827"><?= number_format(\\$avgRating ?: 0, 1) ?>/5</div></div></div>
</div>
<div class="card mb-4" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:25px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px">
        <div style="font-size:16px;font-weight:bold;color:#111827">Data Tiket</div>
        <div class="no-print" style="display:flex;gap:10px">
            <button onclick="window.print()" style="border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-weight:600;background:white;color:#374151;cursor:pointer">Print</button>
            <a href="<?= base_url('admin/reports/excel') ?>?f-from=<?= esc(\\$dateFrom) ?>&f-to=<?= esc(\\$dateTo) ?>" style="border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-weight:600;background:white;color:#374151;text-decoration:none">Excel</a>
            <a href="<?= base_url('admin/reports/pdf') ?>?f-from=<?= esc(\\$dateFrom) ?>&f-to=<?= esc(\\$dateTo) ?>" style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-weight:600;text-decoration:none">PDF</a>
        </div>
    </div>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
        <thead><tr style="background:#f3f4f6">
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">ID</th>
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Judul</th>
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Prioritas</th>
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Status</th>
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Pengaju</th>
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Kategori</th>
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Deskripsi Gangguan</th>
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Link Dokumentasi</th>
            <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Tanggal</th>
        </tr></thead>
        <tbody>
            <?php foreach (\\$tickets as \\$t): ?>
            <tr style="border-bottom:1px solid #e5e7eb">
                <td style="padding:10px;font-size:13px;font-weight:600"><?= esc(\\$t['id']) ?></td>
                <td style="padding:10px;font-size:13px"><?= esc(\\$t['title']) ?></td>
                <td style="padding:10px;font-size:13px"><?= esc(\\$t['priority']) ?></td>
                <td style="padding:10px;font-size:13px"><?= esc(\\$t['status']) ?></td>
                <td style="padding:10px;font-size:13px"><?= esc(\\$t['reporter_name']) ?></td>
                <td style="padding:10px;font-size:13px"><?= esc(\\$t['cat_name']) ?></td>
                <td style="padding:10px;font-size:11px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc(\\$t['description'] ?? '') ?>"><?= esc(\\$t['description'] ?? '-') ?></td>
                <td style="padding:10px;font-size:11px" class="no-print">
                    <?php if(!empty(\\$t['drive_link'])): ?>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <a href="<?= esc(\\$t['drive_link']) ?>" target="_blank" style="color:#3b82f6;text-decoration:none;font-weight:600;"><i class="bi bi-box-arrow-up-right"></i> Buka</a>
                            <button type="button" onclick="openLinkModal(<?= \\$t['id'] ?>, '<?= esc(\\$t['drive_link']) ?>')" style="background:none;border:none;color:#f59e0b;cursor:pointer;padding:0;" title="Edit Link"><i class="bi bi-pencil-square"></i></button>
                        </div>
                    <?php else: ?>
                        <button type="button" onclick="openLinkModal(<?= \\$t['id'] ?>, '')" style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:4px 8px;border-radius:4px;cursor:pointer;font-size:10px;font-weight:600;"><i class="bi bi-upload"></i> Upload</button>
                    <?php endif; ?>
                </td>
                <td style="padding:10px;font-size:13px"><?= date('d/m/Y', strtotime(\\$t['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty(\\$tickets)): ?>
            <tr><td colspan="9" style="padding:30px;text-align:center;color:#9ca3af;font-size:14px">Tidak ada data tiket.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<div id="linkModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:white;padding:25px;border-radius:12px;width:100%;max-width:400px;box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h4 style="margin-top:0;margin-bottom:15px;font-size:16px;font-weight:bold;color:#111827;">Upload/Edit Link Dokumentasi</h4>
        <form id="linkForm" action="" method="POST">
            <div style="margin-bottom:15px;"><label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600;color:#374151">URL Google Drive</label><input type="url" name="drive_link" id="driveLinkInput" placeholder="https://drive.google.com/..." style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db"></div>
            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="closeLinkModal()" style="background:white;color:#6b7280;border:1px solid #d1d5db;padding:8px 15px;border-radius:8px;font-weight:bold;cursor:pointer">Batal</button>
                <button type="submit" style="background:#3b82f6;color:white;border:none;padding:8px 15px;border-radius:8px;font-weight:bold;cursor:pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>
function openLinkModal(ticketId, currentLink) {
    document.getElementById('linkModal').style.display = 'flex';
    document.getElementById('driveLinkInput').value = currentLink;
    document.getElementById('linkForm').action = '<?= base_url('admin/reports/update-link') ?>/' + ticketId;
}
function closeLinkModal() { document.getElementById('linkModal').style.display = 'none'; }
</script>
<?= \\$this->endSection() ?>`;

// Write as base64-encoded PHP script to be executed inside container
const phpScript = `<?php
$c = base64_decode('${Buffer.from(content).toString('base64')}');
$r = file_put_contents('/var/www/html/app/Views/admin/reports/index.php', $c);
echo $r === false ? 'FAILED' : 'OK: ' . $r . ' bytes';
?>`;

fs.writeFileSync('C:/Temp/write_view.php', phpScript, 'utf8');
console.log('PHP script size:', fs.statSync('C:/Temp/write_view.php').size, 'bytes');

// Copy PHP script into container then run it
execFileSync('docker', ['cp', 'C:/Temp/write_view.php', 'helpdesk-app:/tmp/write_view.php']);
console.log('Copied to container');

const result = execFileSync('docker', ['exec', 'helpdesk-app', 'php', '/tmp/write_view.php']);
console.log('Result:', result.toString());

// Verify
const verify = execFileSync('docker', ['exec', 'helpdesk-app', 'grep', '-c', 'Deskripsi Gangguan', '/var/www/html/app/Views/admin/reports/index.php']);
console.log('Verification (should be > 0):', verify.toString().trim());
