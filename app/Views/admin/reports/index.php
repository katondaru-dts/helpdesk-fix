<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    @media print {
        .no-print { display: none !important; }
        .main-wrapper { margin-left: 0; }
        .main-content { padding: 0; }
        body { background: white; }
    }
    .report-stat-card {
        background: white;
        border-radius: 14px;
        padding: 24px 20px;
        display: flex;
        align-items: center; gap: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07), 0 4px 16px rgba(0,0,0,0.04);
        border: 1px solid #e8edf3;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .report-stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.12); transform: translateY(-2px); }
    .report-stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
    .report-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 4px; }
    .report-stat-value { font-size: 30px; font-weight: 800; color: #0f172a; line-height: 1; }
    .filter-card { background: white; border-radius: 14px; padding: 22px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); border: 1px solid #e8edf3; margin-bottom: 28px; }
    .table-card { background: white; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); border: 1px solid #e8edf3; overflow: hidden; }
    .table-card-header { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; background: #f8fafc; }
    .table-card-title { font-size: 15px; font-weight: 700; color: #1e293b; flex: 1; }
    .report-table { width: 100%; border-collapse: collapse; }
    .report-table thead th { background: #f8fafc; padding: 12px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #64748b; border-bottom: 2px solid #e8edf3; white-space: nowrap; }
    .report-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    .report-table tbody tr:hover { background: #f8fafc; }
    .report-table tbody tr:last-child { border-bottom: none; }
    .report-table tbody td { padding: 14px 16px; font-size: 13.5px; color: #334155; vertical-align: middle; }
    .link-input-wrap { display: flex; gap: 6px; align-items: center; }
    .link-input { flex: 1; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 5px 10px; font-size: 11.5px; color: #334155; background: #f8fafc; transition: border 0.2s; min-width: 0; outline: none; }
    .link-input:focus { border-color: #2563eb; background: white; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    .link-save-btn { width: 30px; height: 30px; border-radius: 8px; border: none; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; flex-shrink: 0; transition: background 0.2s; }
    .link-save-btn:hover { background: #1d4ed8; }
    .link-open-btn { width: 30px; height: 30px; border-radius: 8px; border: 1.5px solid #e2e8f0; background: white; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; transition: all 0.2s; text-decoration: none; }
    .link-open-btn:hover { background: #dbeafe; border-color: #2563eb; }
    .priority-badge, .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.03em; }
    .pri-LOW    { background: #d1fae5; color: #065f46; }
    .pri-MEDIUM { background: #dbeafe; color: #1e40af; }
    .pri-HIGH   { background: #fef3c7; color: #92400e; }
    .pri-URGENT { background: #fee2e2; color: #991b1b; }
    .sta-OPEN       { background: #fee2e2; color: #991b1b; }
    .sta-IN_PROGRESS{ background: #fef3c7; color: #92400e; }
    .sta-PENDING    { background: #ede9fe; color: #5b21b6; }
    .sta-RESOLVED   { background: #d1fae5; color: #065f46; }
    .sta-CLOSED     { background: #f1f5f9; color: #475569; }
    .btn-cetak { padding: 8px 16px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: white; color: #475569; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s; }
    .btn-cetak:hover { background: #f1f5f9; }
    .btn-excel { padding: 8px 16px; border-radius: 8px; border: none; background: #059669; color: white; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; transition: background 0.2s; }
    .btn-excel:hover { background: #047857; }
    .btn-pdf { padding: 8px 16px; border-radius: 8px; border: none; background: #dc2626; color: white; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; transition: background 0.2s; }
    .btn-pdf:hover { background: #b91c1c; }
    .btn-filter { padding: 10px 20px; border-radius: 8px; border: none; background: #2563eb; color: white; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.2s; }
    .btn-filter:hover { background: #1d4ed8; }
    .btn-reset { padding: 10px 16px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: white; color: #475569; font-size: 13px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 6px; transition: all 0.2s; }
    .btn-reset:hover { background: #f1f5f9; }
</style>

<div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;" class="no-print">
    <div>
        <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">
            <i class="bi bi-bar-chart-fill" style="color: #2563eb; margin-right: 8px;"></i>Laporan &amp; Statistik
        </h2>
        <p style="font-size: 13px; color: #64748b; margin: 0;">Monitor performa IT Support dan ringkasan data tiket secara keseluruhan</p>
    </div>
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <button onclick="window.print()" class="btn-cetak"><i class="bi bi-printer"></i> Cetak</button>
        <a href="<?= base_url('admin/reports/excel') ?>?f-from=<?= esc($dateFrom) ?>&f-to=<?= esc($dateTo) ?>" class="btn-excel"><i class="bi bi-file-earmark-excel"></i> Excel</a>
        <a href="<?= base_url('admin/reports/pdf') ?>?f-from=<?= esc($dateFrom) ?>&f-to=<?= esc($dateTo) ?>" class="btn-pdf"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
    </div>
</div>

<div class="filter-card no-print">
    <form action="<?= base_url('admin/reports') ?>" method="GET" style="display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 180px;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Dari Tanggal</label>
            <input type="date" name="f-from" value="<?= esc($dateFrom) ?>" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13px; color: #334155; background: white; outline: none; box-sizing: border-box;">
        </div>
        <div style="flex: 1; min-width: 180px;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Sampai Tanggal</label>
            <input type="date" name="f-to" value="<?= esc($dateTo) ?>" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13px; color: #334155; background: white; outline: none; box-sizing: border-box;">
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <button type="submit" class="btn-filter"><i class="bi bi-search"></i> Filter</button>
            <a href="<?= base_url('admin/reports') ?>" class="btn-reset"><i class="bi bi-x-circle"></i> Reset</a>
        </div>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px;">
    <div class="report-stat-card">
        <div class="report-stat-icon" style="background: #dbeafe; color: #2563eb;"><i class="bi bi-ticket-perforated-fill"></i></div>
        <div>
            <div class="report-stat-label">Total Tiket</div>
            <div class="report-stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
        </div>
    </div>
    <div class="report-stat-card">
        <div class="report-stat-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="report-stat-label">Sedang Proses</div>
            <div class="report-stat-value"><?= number_format($stats['in_progress'] ?? 0) ?></div>
        </div>
    </div>
    <div class="report-stat-card" >
        <div class="report-stat-icon" style="background: #d1fae5; color: #059669;"><i class="bi bi-patch-check-fill"></i></div>
        <div>
            <div class="report-stat-label">Sudah Selesai</div>
            <div class="report-stat-value"><?= number_format($stats['solved'] ?? 0) ?></div>
        </div>
    </div>
    <div class="report-stat-card">
        <div class="report-stat-icon" style="background: #ede9fe; color: #7c3aed;"><i class="bi bi-star-fill"></i></div>
        <div>
            <div class="report-stat-label">Avg Rating</div>
            <div class="report-stat-value"><?= number_format($avgRating ?: 0, 1) ?><span style="font-size: 14px; font-weight: 500; color: #94a3b8;">/5</span></div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <i class="bi bi-table" style="color: #2563eb;"></i>
        <span class="table-card-title">Detail Laporan Tiket</span>
        <span style="font-size: 12px; color: #94a3b8; font-weight: 500;"><?= count($tickets) ?> tiket</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="report-table">
            <thead>
                <tr>
                    <th style="padding-left: 24px; width: 90px;">ID</th>
                    <th style="width: 220px;">Judul Tiket</th>
                    <th style="text-align: center; width: 100px;">Prioritas</th>
                    <th style="text-align: center; width: 120px;">Status</th>
                    <th style="width: 130px;">Pengaju</th>
                    <th style="width: 140px;">Lokasi Gangguan</th>
                    <th style="width: 230px;">Deskripsi</th>
                    <th style="width: 190px;">Link Dokumentasi</th>
                    <th style="text-align: right; padding-right: 24px; width: 100px;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 48px 24px; color: #94a3b8;">
                        <i class="bi bi-inbox" style="font-size: 36px; display: block; margin-bottom: 8px;"></i>
                        Tidak ada data tiket untuk periode ini.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($tickets as $t): ?>
                <tr>
                    <td style="padding-left: 24px;">
                        <span style="font-family: monospace; font-size: 12px; font-weight: 700; color: #2563eb; background: #dbeafe; padding: 2px 8px; border-radius: 6px;"><?= esc($t['id']) ?></span>
                    </td>
                    <td><div style="font-weight: 600; color: #1e293b; font-size: 13.5px;"><?= esc($t['title']) ?></div></td>
                    <td style="text-align: center;">
                        <span class="priority-badge pri-<?= strtoupper($t['priority']) ?>"><?= $t['priority'] ?></span>
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge sta-<?= strtoupper(str_replace(' ', '_', $t['status'])) ?>"><?= $t['status'] ?></span>
                    </td>
                    <td><div style="font-size: 13px; color: #475569; font-weight: 500;"><?= esc($t['reporter_name']) ?></div></td>
                    <td><div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 4px;"><?php if(!empty($t['location'])): ?><i class="bi bi-geo-alt-fill" style="color: #f59e0b; font-size: 11px;"></i> <?= esc($t['location']) ?><?php else: ?>—<?php endif; ?></div></td>
                    <td>
                        <div style="max-width: 220px; font-size: 12px; color: #64748b; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;" title="<?= esc($t['description']) ?>">
                            <?= esc($t['description'] ?: '—') ?>
                        </div>
                    </td>
                    <td>
                        <form action="<?= base_url('admin/reports/update-link/' . $t['id']) ?>" method="POST" class="link-input-wrap no-print">
                            <input type="text" name="drive_link" class="link-input" placeholder="Tambahkan link..." value="<?= esc($t['drive_link']) ?>">
                            <button type="submit" class="link-save-btn" title="Simpan"><i class="bi bi-check-lg"></i></button>
                            <?php if (!empty($t['drive_link'])): ?>
                            <a href="<?= esc($t['drive_link']) ?>" target="_blank" class="link-open-btn" title="Buka Link"><i class="bi bi-box-arrow-up-right"></i></a>
                            <?php endif; ?>
                        </form>
                    </td>
                    <td style="text-align: right; padding-right: 24px;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 500; white-space: nowrap;"><?= date('d/m/Y', strtotime($t['created_at'])) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(!empty($pager_links)): ?>
    <div class="no-print" style="padding:16px 24px; border-top: 1px solid #f1f5f9; background: white; border-radius: 0 0 14px 14px;">
        <?= $pager_links ?>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
