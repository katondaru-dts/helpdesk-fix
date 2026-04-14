<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<!-- DEBUG:  -->
<div class="page-header">
    <div>
        <div class="page-header-title">Admin Dashboard</div>
        <div class="page-header-sub">Pantau semua tiket dan kinerja tim IT Support</div>
    </div>
    <div class="d-flex gap-2">
        <?php if (has_permission('Buat Tiket') || $role == 4): ?>
            <a href="<?= base_url('tickets/create') ?>" class="btn btn-success"><i class="bi bi-plus-circle"></i> Buat Tiket</a>
        <?php endif; ?>
        <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline"><i class="bi bi-bar-chart"></i> Laporan</a>
        <a href="<?= base_url('tickets') ?>" class="btn btn-primary"><i class="bi bi-list-ul"></i> Semua Tiket</a>
    </div>
</div>

<!-- Stats: 5 kolom, 2 baris, horizontal layout -->
<div style="display:grid; grid-template-columns: repeat(5, 1fr); gap:12px; margin-bottom:24px;">

    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-blue" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-ticket-detailed"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:20px;"><?= $stats['total'] ?></div>
            <div class="stat-lbl">Total Tiket</div>
        </div>
    </div>

    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>?f-status=OPEN'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-red" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-circle"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:20px;"><?= $stats['open'] ?></div>
            <div class="stat-lbl">Open</div>
        </div>
    </div>

    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>?f-status=IN_PROGRESS'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-orange" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-arrow-repeat"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:20px;"><?= $stats['inProgress'] ?></div>
            <div class="stat-lbl">In Progress</div>
        </div>
    </div>

    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>?f-status=RESOLVED'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-green" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-check-circle"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:20px;"><?= $stats['resolved'] ?></div>
            <div class="stat-lbl">Selesai</div>
        </div>
    </div>

    <div class="stat-card" onclick="window.location='<?= base_url('admin/users') ?>'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-purple" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-people"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:20px;"><?= $stats['users'] ?></div>
            <div class="stat-lbl">User Aktif</div>
        </div>
    </div>

    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-cyan" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-clock-history"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:20px;"><?= $stats['unassigned'] ?></div>
            <div class="stat-lbl">Belum Diassign</div>
        </div>
    </div>

    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-orange" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-exclamation-triangle"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:20px;"><?= $stats['urgent'] ?></div>
            <div class="stat-lbl">Urgent</div>
        </div>
    </div>

    <div class="stat-card" onclick="window.location='<?= base_url('admin/reports') ?>'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-blue" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-bar-chart-line-fill"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:16px; line-height:1.3;">Laporan</div>
            <div class="stat-lbl">Statistik Sistem</div>
        </div>
    </div>

    <div class="stat-card" onclick="window.location='<?= base_url('admin/audit-logs') ?>'" style="cursor:pointer; padding:14px 12px; gap:10px;">
        <div class="stat-icon si-secondary" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-journal-text"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:16px; line-height:1.3;">Audit</div>
            <div class="stat-lbl">Log Aktivitas</div>
        </div>
    </div>

    <div class="stat-card" style="padding:14px 12px; gap:10px;">
        <div class="stat-icon si-green" style="width:38px;height:38px;font-size:17px;flex-shrink:0;"><i class="bi bi-star-fill"></i></div>
        <div style="min-width:0;">
            <div class="stat-val" style="font-size:20px;"><?= $stats['avgRating'] ?></div>
            <div class="stat-lbl">Avg Rating</div>
        </div>
    </div>

</div>

<div class="grid g2 mb-4">
    <!-- Urgent tickets -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-exclamation-triangle-fill" style="color:#DC2626"></i>
            <span class="card-title">Tiket Urgent / High</span>
            <a href="<?= base_url('tickets') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <?php if (count($urgentTickets ?? []) > 0): ?>
                <?php foreach ($urgentTickets as $t): ?>
                    <div class="d-flex ai-center gap-3 p-3" style="border-bottom:1px solid var(--gray-100);cursor:pointer" onclick="window.location='<?= base_url('tickets/detail/'.$t['id']) ?>'">
                        <div style="min-width:0;flex:1">
                            <div class="fw-600 truncate" style="font-size:13px"><?= esc($t['title']); ?></div>
                            <div class="text-xs text-muted mt-1"><?= $t['id']; ?> - <?= esc($t['reporter_name']); ?> - <?= date('d M Y', strtotime($t['created_at'])); ?></div>
                        </div>
                        <div class="d-flex gap-1">
                            <span class="badge badge-danger"><?= $t['priority']; ?></span>
                            <span class="badge badge-secondary"><?= $t['status']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-4 text-center text-muted">Tidak ada tiket mendesak.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Assignment -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-person-x-fill" style="color:#D97706"></i>
            <span class="card-title">Belum Diassign (<?= $stats['unassigned']; ?>)</span>
        </div>
        <div class="card-body p-0">
            <?php if (count($pendingTickets ?? []) > 0): ?>
                <?php foreach ($pendingTickets as $t): ?>
                    <div class="d-flex ai-center gap-3 p-3" style="border-bottom:1px solid var(--gray-100)">
                        <div style="min-width:0;flex:1">
                            <div class="fw-600 truncate" style="font-size:13px"><?= esc($t['title']); ?></div>
                            <div class="text-xs text-muted mt-1"><?= $t['id']; ?> - <?= esc($t['cat_name']); ?></div>
                        </div>
                        <a href="<?= base_url('tickets/detail/'.$t['id']) ?>" class="btn btn-sm btn-primary"><i class="bi bi-person-plus"></i> Assign</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-4 text-center text-muted">Semua tiket sudah diassign.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
