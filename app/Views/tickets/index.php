<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
if (!function_exists('getInitials')) {
    function getInitials(string $name): string {
        $parts = preg_split('/\s+/', mb_strtoupper(trim($name)));
        $parts = array_filter($parts);
        $parts = array_values($parts);
        if (count($parts) === 0) return '?';
        $first = mb_substr($parts[0], 0, 1);
        $last  = count($parts) > 1 ? mb_substr($parts[1], 0, 1) : '';
        return $first . $last;
    }
}
?>
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
        <?php if (has_permission('Ekspor Data')): ?>
            <button class="btn btn-outline" onclick="exportTickets()" style="background:white; color:#10b981; border:1px solid #10b981; font-weight:bold; cursor:pointer; padding:8px 15px; border-radius:8px"><i class="bi bi-file-earmark-excel"></i> Ekspor CSV/Excel</button>
        <?php endif; ?>
        <?php if (has_permission('Buat Tiket') || session()->get('role_id') == 4): ?>
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

<?php if ($isStaff): ?>
<!-- Bulk Action Toolbar -->
<div id="bulkToolbar" style="display:none; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:10px 16px; margin-bottom:12px; align-items:center; gap:10px; flex-wrap:wrap;">
    <span id="bulkCount" style="font-weight:600; color:#1e40af; font-size:14px;">0 tiket dipilih</span>
    <form id="bulkForm" action="<?= base_url('tickets/bulk-update-status') ?>" method="POST" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <?= csrf_field() ?>
        <div id="bulkIdsContainer"></div>
        <select name="bulk_status" class="form-select" style="width:auto;" required>
            <option value="">-- Ubah Status ke --</option>
            <?php foreach(['OPEN' => 'OPEN', 'IN_PROGRESS' => 'IN_PROGRESS', 'PENDING' => 'PENDING', 'RESOLVED' => 'RESOLVED', 'CLOSED' => 'CLOSED'] as $val => $label): ?>
                <?php if ($val === 'CLOSED' && is_technician() && !is_admin()): continue; endif; ?>
                <option value="<?= $val ?>"><?= $label ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Ubah status tiket yang dipilih?')"><i class="bi bi-check2-all"></i> Terapkan</button>
    </form>
    <button class="btn btn-outline btn-sm" onclick="clearSelection()"><i class="bi bi-x"></i> Batal</button>
</div>
<?php endif; ?>

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
                    <?php if ($isStaff): ?>
                    <th style="width:36px;padding-left:12px;text-align:center">
                        <input type="checkbox" id="checkAll" title="Pilih semua" style="cursor:pointer;width:16px;height:16px;">
                    </th>
                    <?php endif; ?>
                    <th style="width:90px;padding-left:<?= $isStaff ? '8px' : '16px' ?>"><?= $sortLink('id', 'ID') ?></th>
                    <th style="width:200px"><?= $sortLink('title', 'Judul') ?></th>
                    <th style="width:120px">Nama Pemohon</th>
                    <th style="width:100px"><?= $sortLink('cat_name', 'Kategori') ?></th>
                    <?php if ($isStaff): ?><th style="width:120px"><?= $sortLink('reporter_name', 'Pelapor') ?></th><?php endif; ?>
                    <th style="width:80px;text-align:center"><?= $sortLink('priority', 'Prioritas') ?></th>
                    <th style="width:100px;text-align:center"><?= $sortLink('status', 'Status') ?></th>
                    <th style="width:110px"><?= $sortLink('sla_deadline', 'SLA') ?></th>
                    <?php if ($isStaff): ?><th style="width:110px"><?= $sortLink('assigned_name', 'Teknisi') ?></th><?php endif; ?>
                    <th style="width:90px"><?= $sortLink('created_at', 'Tanggal') ?></th>
                    <th style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr onclick="window.location='<?= base_url('tickets/detail/' . $t['id']) ?>'" style="cursor:pointer" class="ticket-row">
                            <?php if ($isStaff): ?>
                            <td onclick="event.stopPropagation()" style="text-align:center;padding-left:12px;">
                                <input type="checkbox" class="ticket-check" value="<?= esc($t['id']) ?>" style="cursor:pointer;width:16px;height:16px;">
                            </td>
                            <?php endif; ?>
                            <td style="padding-left:8px"><span style="font-family:monospace;font-size:12px;font-weight:700;color:#2563eb;background:#dbeafe;padding:2px 8px;border-radius:6px"><?= $t['id'] ?></span></td>
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
                            <?php if ($isStaff): ?>
                            <td>
                                <?php
                                    $names = !empty($t['assigned_names']) ? $t['assigned_names'] : ($t['assigned_name'] ?? '');
                                ?>
                                <?php if (!empty($names)): ?>
                                    <div style="display:flex; align-items:center;">
                                        <?php foreach (explode(', ', $names) as $idx => $nm): ?>
                                            <span title="<?= esc(trim($nm)) ?>" 
                                                  style="width:28px; height:28px; border-radius:50%; background:<?= ['#dbeafe','#dcfce7','#fef9c3','#fce7f3','#ede9fe'][$idx % 5] ?>; display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:<?= ['#1d4ed8','#15803d','#a16207','#9d174d','#6d28d9'][$idx % 5] ?>; cursor:pointer; border:2px solid #ffffff; letter-spacing:-0.5px; margin-left: <?= $idx > 0 ? '-8px' : '0' ?>; position:relative; z-index: <?= 10 - $idx ?>; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                <?= getInitials($nm) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td><span style="font-size:12px;color:#64748b"><?= date('d/m/Y', strtotime($t['created_at'])) ?></span></td>
                            <td onclick="event.stopPropagation()" style="display:flex; gap:5px; align-items:center;">
                                <a href="<?= base_url('tickets/detail/' . $t['id']) ?>" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a>
                                <?php if (is_admin()): ?>
                                    <form action="<?= base_url('tickets/delete/' . $t['id']) ?>" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket ini? Tindakan ini tidak dapat dibatalkan.');">
                                            <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline" style="color:#ef4444; border-color:#ef4444;"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $isStaff ? '12' : '11' ?>" class="text-center p-4 text-muted">Tidak ada tiket yang ditemukan.</td></tr>
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
<script>
(function() {
    var checkAll = document.getElementById('checkAll');
    var toolbar = document.getElementById('bulkToolbar');
    var countEl = document.getElementById('bulkCount');
    var idsContainer = document.getElementById('bulkIdsContainer');
    if (!checkAll) return;

    function getChecked() {
        return Array.from(document.querySelectorAll('.ticket-check:checked'));
    }

    function updateToolbar() {
        var checked = getChecked();
        if (checked.length > 0) {
            toolbar.style.display = 'flex';
            countEl.textContent = checked.length + ' tiket dipilih';
            idsContainer.innerHTML = '';
            checked.forEach(function(cb) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ticket_ids[]';
                inp.value = cb.value;
                idsContainer.appendChild(inp);
            });
        } else {
            toolbar.style.display = 'none';
            idsContainer.innerHTML = '';
        }
        checkAll.indeterminate = checked.length > 0 && checked.length < document.querySelectorAll('.ticket-check').length;
        checkAll.checked = checked.length > 0 && checked.length === document.querySelectorAll('.ticket-check').length;
    }

    checkAll.addEventListener('change', function() {
        document.querySelectorAll('.ticket-check').forEach(function(cb) {
            cb.checked = checkAll.checked;
        });
        updateToolbar();
    });

    document.querySelectorAll('.ticket-check').forEach(function(cb) {
        cb.addEventListener('change', updateToolbar);
    });
})();

function clearSelection() {
    document.querySelectorAll('.ticket-check').forEach(function(cb) { cb.checked = false; });
    var checkAll = document.getElementById('checkAll');
    if (checkAll) { checkAll.checked = false; checkAll.indeterminate = false; }
    document.getElementById('bulkToolbar').style.display = 'none';
}
</script>
<?= $this->endSection() ?>
