<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
@media (max-width: 768px) {
    .ticket-header-actions { flex-direction: column; width: 100%; }
    .ticket-header-actions .btn { width: 100%; justify-content: center; }
    .filter-date-row { flex-direction: column; align-items: stretch !important; }
    .filter-date-row input { width: 100% !important; }
}
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
</style>
<!-- DEBUG:  -->
<div class="page-header">
    <div>
        <div class="page-header-title"><?= $isStaff ? 'Semua Tiket' : 'Tiket Saya' ?></div>
        <div class="page-header-sub"><?= $totalRows ?> tiket ditemukan</div>
    </div>
    <div class="ticket-header-actions" style="display:flex; gap:10px;">
        <button class="btn btn-outline" onclick="exportTickets()" style="background:white; color:#10b981; border:1px solid #10b981; font-weight:bold; cursor:pointer; padding:8px 15px; border-radius:8px"><i class="bi bi-file-earmark-excel"></i> Ekspor CSV/Excel</button>
        <?php if (has_permission('Buat Tiket') || $role == 4): ?>
            <a href="<?= base_url('tickets/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Tiket</a>
        <?php endif; ?>
    </div>
</div>

<script>
function exportTickets() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '<?= base_url('tickets/export') ?>?' + params.toString();
}
</script>

<div class="card mb-4">
    <div class="card-body" style="padding:14px 20px">
        <form action="<?= base_url('tickets') ?>" method="GET" class="filter-bar">
            <div class="search-wrap" style="display:flex; align-items:center; background:#f3f4f6; border-radius:8px; padding:0 10px;">
                <i class="bi bi-search" id="searchIconBtn" style="color:#666; cursor:pointer; padding:4px 2px;" title="Klik untuk mencari"></i>
                <input type="text" name="search" id="searchInput" value="<?= esc($filters['search']) ?>" class="form-control" placeholder="Cari judul, isi atau ID..." style="background:transparent; border:none; box-shadow:none;">
            </div>
            <select name="f-status" class="form-select">
                <option value="">Status</option>
                <?php foreach(['OPEN', 'IN_PROGRESS', 'PENDING', 'RESOLVED', 'CLOSED'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filters['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <select name="f-priority" class="form-select">
                <option value="">Prioritas</option>
                <?php foreach(['LOW', 'MEDIUM', 'HIGH', 'URGENT'] as $p): ?>
                    <option value="<?= $p ?>" <?= $filters['priority'] == $p ? 'selected' : '' ?>><?= $p ?></option>
                <?php endforeach; ?>
            </select>
            <select name="f-cat" class="form-select">
                <option value="">Kategori</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filters['cat_id'] == $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($isStaff): ?>
                <select name="f-dept" class="form-select">
                    <option value="">Dept</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $filters['dept_id'] == $d['id'] ? 'selected' : '' ?>><?= esc($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="f-assigned" class="form-select">
                    <option value="">Teknisi</option>
                    <?php foreach ($technicians as $tech): ?>
                        <option value="<?= $tech['id'] ?>" <?= $filters['assigned_to'] == $tech['id'] ? 'selected' : '' ?>><?= esc($tech['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <div class="filter-date-row" style="display: flex; gap: 5px; align-items: center;">
                <input type="date" name="f-from" value="<?= esc($filters['date_from']) ?>" class="form-control" style="width: auto;">
                <span class="text-muted">s/d</span>
                <input type="date" name="f-to" value="<?= esc($filters['date_to']) ?>" class="form-control" style="width: auto;">
            </div>
            <div style="display:flex; gap:6px;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
                <a href="<?= base_url('tickets') ?>" class="btn btn-outline"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <?php
                $sortCol = $filters['sort'] ?? 'created_at';
                $sortDir = $filters['dir'] ?? 'DESC';
                $nextDir = $sortDir === 'ASC' ? 'DESC' : 'ASC';
                $baseParams = array_filter($filters, fn($k) => !in_array($k, ['sort','dir']), ARRAY_FILTER_USE_KEY);
                $sortLink = function($col, $label) use ($sortCol, $sortDir, $nextDir, $baseParams) {
                    $dir = ($sortCol === $col) ? $nextDir : 'ASC';
                    $icon = '';
                    if ($sortCol === $col) {
                        $icon = $sortDir === 'ASC' ? ' <i class="bi bi-caret-up-fill" style="font-size:10px"></i>' : ' <i class="bi bi-caret-down-fill" style="font-size:10px"></i>';
                    }
                    $params = http_build_query(array_merge($baseParams, ['sort' => $col, 'dir' => $dir]));
                    return '<a href="?' . $params . '" style="color:inherit;text-decoration:none">' . $label . $icon . '</a>';
                };
                ?>
                <tr>
                    <th style="width:90px;padding-left:16px"><?= $sortLink('id', 'ID') ?></th>
                    <th style="width:200px"><?= $sortLink('title', 'Judul') ?></th>
                    <th style="width:120px">Nama Pemohon</th>
                    <th style="width:100px"><?= $sortLink('cat_name', 'Kategori') ?></th>
                    <?php if ($isStaff): ?><th style="width:120px"><?= $sortLink('reporter_name', 'Pelapor') ?></th><?php endif; ?>
                    <th style="width:80px;text-align:center"><?= $sortLink('priority', 'Prioritas') ?></th>
                    <th style="width:100px;text-align:center"><?= $sortLink('status', 'Status') ?></th>
                    <th style="width:110px"><?= $sortLink('sla_deadline', 'SLA') ?></th>
                    <?php if ($isStaff): ?><th style="width:110px"><?= $sortLink('assigned_name', 'Ditangani') ?></th><?php endif; ?>
                    <th style="width:90px"><?= $sortLink('created_at', 'Tanggal') ?></th>
                    <th style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr onclick="window.location='<?= base_url('tickets/detail/' . $t['id']) ?>'" style="cursor:pointer">
                            <td style="padding-left:16px"><span style="font-family:monospace;font-size:12px;font-weight:700;color:#2563eb;background:#dbeafe;padding:2px 8px;border-radius:6px"><?= $t['id'] ?></span></td>
                            <td><div style="font-weight:600;font-size:13.5px;word-break:break-word;line-height:1.4"><?= esc($t['title']) ?></div></td>
                            <td><span style="font-size:13px;color:#475569;font-weight:500"><?= esc($t['requester_name'] ?? '') ?></span></td>
                            <td><span class="text-sm" style="font-size:13px"><?= esc($t['cat_name']) ?></span></td>
                            <?php if ($isStaff): ?><td><span style="font-size:13px;color:#475569;font-weight:500"><?= esc($t['reporter_name']) ?></span></td><?php endif; ?>
                            <td style="text-align:center"><span class="priority-badge pri-<?= strtoupper($t['priority']) ?>"><?= $t['priority'] ?></span></td>
                            <td style="text-align:center"><span class="status-badge sta-<?= strtoupper(str_replace(' ', '_', $t['status'])) ?>"><?= $t['status'] ?></span></td>
                            <td>
                                <?php if (in_array($t['status'], ['RESOLVED', 'CLOSED'])): ?>
                                    <span class="status-badge sta-RESOLVED">Selesai</span>
                                <?php elseif ($t['status'] === 'PENDING'): ?>
                                    <span class="status-badge sta-PENDING"><i class="bi bi-pause-fill"></i> Paused</span>
                                <?php elseif ($t['sla_deadline']): ?>
                                    <span class="sla-timer" data-deadline="<?= date('c', strtotime($t['sla_deadline'])) ?>" style="font-size:12px;color:#2563eb;font-weight:700">Menghitung...</span>
                                <?php else: ?>
                                    <span class="text-muted">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <?php if ($isStaff): ?><td><span style="font-size:13px;color:#475569;font-weight:500"><?= $t['assigned_name'] ?: '<span class="text-muted">&mdash;</span>' ?></span></td><?php endif; ?>
                            <td><span style="font-size:12px;color:#64748b"><?= date('d/m/Y', strtotime($t['created_at'])) ?></span></td>
                            <td onclick="event.stopPropagation()" style="display:flex; gap:5px; align-items:center;">
                                <a href="<?= base_url('tickets/detail/' . $t['id']) ?>" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a>
                                <?php if (session()->get('role_id') == 1): ?>
                                    <form action="<?= base_url('tickets/delete/' . $t['id']) ?>" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket ini? Tindakan ini tidak dapat dibatalkan.');">
                                            <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline" style="color:#ef4444; border-color:#ef4444;"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="11" class="text-center p-4 text-muted">Tidak ada tiket yang ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="pagination-wrap" style="padding: 20px; border-top: 1px solid #f3f4f6;">
        <?= $pager->links() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateTimers() {
        const now = new Date().getTime();
        document.querySelectorAll('.sla-timer').forEach(el => {
            const deadlineStr = el.getAttribute('data-deadline');
            if (!deadlineStr) return;

            const deadline = new Date(deadlineStr).getTime();
            if (isNaN(deadline)) return;

            const diff = deadline - now;

            if (diff <= 0) {
                        el.innerHTML = '<i class="bi bi-clock-history"></i> Overdue';
                        el.style.color = '#ef4444';
                    } else {
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                        el.innerHTML = hours + 'j ' + minutes + 'm ' + seconds + 's';

                        if (hours < 2) {
                            el.style.color = '#f97316';
                        } else {
                            el.style.color = '#22c55e';
                        }
                    }
        });
    }
    updateTimers();
    setInterval(updateTimers, 1000);
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var searchIcon = document.getElementById("searchIconBtn");
    var searchInput = document.getElementById("searchInput");
    if (searchIcon && searchInput) {
        searchIcon.addEventListener("click", function() {
            searchInput.closest("form").submit();
        });
        searchInput.addEventListener("keydown", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                searchInput.closest("form").submit();
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
