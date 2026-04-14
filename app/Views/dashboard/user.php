<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <div class="page-header-title">Halo, <?= htmlspecialchars(explode(' ', session()->get('name'))[0]) ?>!</div>
        <div class="page-header-sub">Selamat datang di HelpDesk IT &mdash; pantau dan kelola laporan gangguan Anda.</div>
    </div>
    <a href="<?= base_url('tickets/create') ?>" class="btn btn-primary btn-lg"><i class="bi bi-plus-circle"></i> Buat Tiket Baru</a>
</div>

<div class="grid g4 mb-4">
    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>'" style="cursor:pointer">
        <div class="stat-icon si-blue"><i class="bi bi-ticket-detailed"></i></div>
        <div><div class="stat-val"><?= $stats['total']; ?></div><div class="stat-lbl">Total Tiket</div></div>
    </div>
    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>'" style="cursor:pointer">
        <div class="stat-icon si-red"><i class="bi bi-circle"></i></div>
        <div><div class="stat-val"><?= $stats['open']; ?></div><div class="stat-lbl">Open</div></div>
    </div>
    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>'" style="cursor:pointer">
        <div class="stat-icon si-orange"><i class="bi bi-arrow-repeat"></i></div>
        <div><div class="stat-val"><?= $stats['inProgress']; ?></div><div class="stat-lbl">In Progress</div></div>
    </div>
    <div class="stat-card" onclick="window.location='<?= base_url('tickets') ?>'" style="cursor:pointer">
        <div class="stat-icon si-green"><i class="bi bi-check-circle"></i></div>
        <div><div class="stat-val"><?= $stats['resolved']; ?></div><div class="stat-lbl">Selesai</div></div>
    </div>
</div>

<div class="grid g2">
    <!-- Recent Tickets -->
    <div class="card" style="grid-column:1/-1">
        <div class="card-header">
            <i class="bi bi-clock-history" style="color:var(--primary)"></i>
            <span class="card-title">Tiket Terbaru</span>
            <a href="<?= base_url('tickets') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recentTickets ?? []) > 0): ?>
                        <?php foreach ($recentTickets as $t): ?>
                            <tr onclick="window.location='<?= base_url('tickets/detail/'.$t['id']) ?>'" style="cursor:pointer">
                                <td><span class="fw-600"><?= $t['id']; ?></span></td>
                                <td><div class="truncate" style="max-width:220px"><?= esc($t['title']); ?></div></td>
                                <td><span class="badge"><?= $t['priority']; ?></span></td>
                                <td><span class="badge"><?= $t['status']; ?></span></td>
                                <td class="text-muted text-sm"><?= date('d M Y', strtotime($t['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center p-4 text-muted">Belum ada tiket. Buat tiket pertama Anda!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
