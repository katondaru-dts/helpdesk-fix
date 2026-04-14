<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- DEBUG:  -->
<div class="page-header">
    <div>
        <div class="page-header-title"><?= $isStaff ? 'Semua Tiket' : 'Tiket Saya' ?></div>
        <div class="page-header-sub"><?= $totalRows ?> tiket ditemukan</div>
    </div>
    <div style="display:flex; gap:10px;">
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
        <form action="<?= base_url('tickets') ?>" method="GET" class="filter-bar" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <div class="search-wrap" style="flex: 1; min-width: 200px; display:flex; align-items:center; background:#f3f4f6; border-radius:8px; padding:0 10px;">
                <i class="bi bi-search" style="color:#666"></i>
                <input type="text" name="search" value="<?= esc($filters['search']) ?>" class="form-control" placeholder="Cari judul atau ID..." style="background:transparent; border:none; box-shadow:none;">
            </div>
            <select name="f-status" class="form-select" style="width:auto">
                <option value="">Status</option>
                <?php foreach(['OPEN', 'IN_PROGRESS', 'PENDING', 'RESOLVED', 'CLOSED'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filters['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <select name="f-priority" class="form-select" style="width:auto">
                <option value="">Prioritas</option>
                <?php foreach(['LOW', 'MEDIUM', 'HIGH', 'URGENT'] as $p): ?>
                    <option value="<?= $p ?>" <?= $filters['priority'] == $p ? 'selected' : '' ?>><?= $p ?></option>
                <?php endforeach; ?>
            </select>
            <select name="f-cat" class="form-select" style="width:auto">
                <option value="">Kategori</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filters['cat_id'] == $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($isStaff): ?>
                <select name="f-dept" class="form-select" style="width:auto">
                    <option value="">Dept</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $filters['dept_id'] == $d['id'] ? 'selected' : '' ?>><?= esc($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="f-assigned" class="form-select" style="width:auto">
                    <option value="">Teknisi</option>
                    <?php foreach ($technicians as $tech): ?>
                        <option value="<?= $tech['id'] ?>" <?= $filters['assigned_to'] == $tech['id'] ? 'selected' : '' ?>><?= esc($tech['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <div style="display: flex; gap: 5px; align-items: center;">
                <input type="date" name="f-from" value="<?= esc($filters['date_from']) ?>" class="form-control" style="width: auto;">
                <span class="text-muted">s/d</span>
                <input type="date" name="f-to" value="<?= esc($filters['date_to']) ?>" class="form-control" style="width: auto;">
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
            <a href="<?= base_url('tickets') ?>" class="btn btn-outline"><i class="bi bi-arrow-counterclockwise"></i></a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <?php if ($isStaff): ?><th>Pelapor</th><?php endif; ?>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <?php if ($isStaff): ?><th>Ditangani</th><?php endif; ?>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr onclick="window.location='<?= base_url('tickets/detail/' . $t['id']) ?>'" style="cursor:pointer">
                            <td><span class="fw-600" style="color:var(--primary)"><?= $t['id'] ?></span></td>
                            <td><div class="truncate" style="max-width:200px"><?= esc($t['title']) ?></div></td>
                            <td><span class="text-sm"><?= esc($t['cat_name']) ?></span></td>
                            <?php if ($isStaff): ?><td><span class="text-sm"><?= esc($t['reporter_name']) ?></span></td><?php endif; ?>
                            <td><span class="badge"><?= $t['priority'] ?></span></td>
                            <td><span class="badge"><?= $t['status'] ?></span></td>
                            <?php if ($isStaff): ?><td><span class="text-sm"><?= $t['assigned_name'] ?: '<span class="text-muted">&mdash;</span>' ?></span></td><?php endif; ?>
                            <td class="text-sm text-muted"><?= date('d/m/y', strtotime($t['created_at'])) ?></td>
                            <td onclick="event.stopPropagation()" style="display:flex; gap:5px; align-items:center;">
                                <a href="<?= base_url('tickets/detail/' . $t['id']) ?>" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a>
                                <?php if (session()->get('role_id') == 1): ?>
                                    <form action="<?= base_url('tickets/delete/' . $t['id']) ?>" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket ini? Tindakan ini tidak dapat dibatalkan.');">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color:#ef4444; border-color:#ef4444;"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center p-4 text-muted">Tidak ada tiket yang ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="pagination-wrap" style="padding: 20px; border-top: 1px solid #f3f4f6;">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>


