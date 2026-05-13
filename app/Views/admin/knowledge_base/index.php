<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <div class="page-header-title">Kelola Knowledge Base</div>
        <div class="page-header-sub">Buat, edit, dan kelola artikel panduan</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button class="btn btn-outline" onclick="openCatModal()"><i class="bi bi-folder-plus"></i> Kelola Kategori</button>
        <button class="btn btn-outline" onclick="reembedAll()" style="color:#7C3AED;border-color:#7C3AED"><i class="bi bi-stars"></i> Re-embed Semua</button>
        <a href="<?= base_url('admin/knowledge-base/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Artikel Baru</a>
    </div>
</div>

<!-- Stats -->
<div class="grid g4 mb-4">
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="bi bi-file-text"></i></div>
        <div><div class="stat-val"><?= $stats['total'] ?></div><div class="stat-lbl">Total Artikel</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-green"><i class="bi bi-check-circle"></i></div>
        <div><div class="stat-val"><?= $stats['published'] ?></div><div class="stat-lbl">Dipublikasikan</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-orange"><i class="bi bi-pencil-square"></i></div>
        <div><div class="stat-val"><?= $stats['draft'] ?></div><div class="stat-lbl">Draft</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-purple"><i class="bi bi-eye"></i></div>
        <div><div class="stat-val"><?= number_format($stats['views']) ?></div><div class="stat-lbl">Total Dibaca</div></div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-file-text" style="color:var(--primary)"></i>
        <span class="card-title">Daftar Artikel</span>
        <span class="text-muted text-sm"><?= $total ?> artikel</span>
    </div>
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="filter-bar">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" value="<?= esc($filters['search']) ?>" placeholder="Cari judul...">
            </div>
            <select name="cat" class="form-select" style="width:auto">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $filters['category_id']==$c['id']?'selected':'' ?>><?= esc($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-select" style="width:auto">
                <option value="">Semua Status</option>
                <option value="published" <?= $filters['status']=='published'?'selected':'' ?>>Published</option>
                <option value="draft" <?= $filters['status']=='draft'?'selected':'' ?>>Draft</option>
                <option value="archived" <?= $filters['status']=='archived'?'selected':'' ?>>Archived</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            <a href="<?= base_url('admin/knowledge-base') ?>" class="btn btn-outline"><i class="bi bi-arrow-counterclockwise"></i></a>
        </form>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th><th>Judul</th><th>Kategori</th><th>Status</th>
                    <th>AI</th><th>Dibaca</th><th>Diperbarui</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($articles)): ?>
                <tr><td colspan="8" class="text-center" style="padding:32px;color:var(--gray-400)">Belum ada artikel</td></tr>
            <?php else: ?>
            <?php foreach ($articles as $i => $a): ?>
            <tr>
                <td style="color:var(--gray-400);font-size:12px"><?= ($currentPage-1)*$perPage + $i + 1 ?></td>
                <td>
                    <div style="font-weight:600;color:var(--gray-900)"><?= esc($a['title']) ?></div>
                    <?php if ($a['excerpt']): ?>
                    <div style="font-size:12px;color:var(--gray-400)"><?= esc(mb_strimwidth($a['excerpt'], 0, 60, '...')) ?></div>
                    <?php endif; ?>
                </td>
                <td><span style="padding:2px 8px;border-radius:6px;font-size:11.5px;font-weight:500;background:#DBEAFE;color:#1D4ED8"><?= esc($a['category_name']) ?></span></td>
                <td>
                    <?php $sc = ['published'=>'badge-published','draft'=>'badge-draft','archived'=>'badge-archived']; ?>
                    <span class="badge <?= $sc[$a['status']] ?? '' ?>"><?= ucfirst($a['status']) ?></span>
                </td>
                <td>
                    <?php if ($a['use_for_ai']): ?>
                        <span title="Digunakan AI"><i class="bi bi-stars" style="color:#7C3AED"></i></span>
                    <?php else: ?>
                        <span style="color:var(--gray-300)"><i class="bi bi-dash"></i></span>
                    <?php endif; ?>
                </td>
                <td style="font-weight:600"><?= number_format($a['view_count']) ?></td>
                <td style="font-size:12.5px;color:var(--gray-400)"><?= date('d/m/Y', strtotime($a['updated_at'])) ?></td>
                <td>
                    <div style="display:flex;gap:4px">
                        <a href="<?= base_url('admin/knowledge-base/'.$a['id'].'/edit') ?>" class="btn btn-sm btn-outline" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="<?= base_url('knowledge-base/'.$a['slug']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Preview"><i class="bi bi-eye"></i></a>
                        <?php if ($a['use_for_ai'] && $a['status']==='published'): ?>
                        <button type="button" class="btn btn-sm btn-outline" title="Generate Embedding" onclick="reembed(<?= $a['id'] ?>, this)" style="color:#7C3AED;border-color:#7C3AED"><i class="bi bi-stars"></i></button>
                        <?php endif; ?>
                        <form action="<?= base_url('admin/knowledge-base/'.$a['id'].'/delete') ?>" method="POST" style="margin:0" onsubmit="return confirm('Hapus artikel ini?')">
                            <button type="submit" class="btn btn-sm btn-outline" style="color:#DC2626;border-color:#DC2626"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php $totalPages = ceil($total / $perPage); if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?<?= http_build_query(array_merge($filters, ['page'=>$currentPage-1])) ?>" class="pg-btn"><i class="bi bi-chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a href="?<?= http_build_query(array_merge($filters, ['page'=>$p])) ?>" class="pg-btn <?= $p==$currentPage?'active':'' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($currentPage < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($filters, ['page'=>$currentPage+1])) ?>" class="pg-btn"><i class="bi bi-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Category Modal -->
<div class="modal-overlay" id="catModal" style="display:none">
    <div class="modal-box" style="max-width:480px">
        <div class="modal-title"><i class="bi bi-folder2-open" style="color:var(--primary)"></i> Kelola Kategori</div>
        <div style="display:flex;gap:8px;margin-bottom:16px">
            <input type="text" id="catName" class="form-control" placeholder="Nama kategori..." style="flex:1">
            <button class="btn btn-primary" onclick="addCategory()"><i class="bi bi-plus"></i> Tambah</button>
        </div>
        <div id="catList" style="display:flex;flex-direction:column;gap:8px">
            <!-- Loaded via JS -->
        </div>
        <div class="modal-actions">
            <button class="btn btn-primary" onclick="document.getElementById('catModal').style.display='none'">Selesai</button>
        </div>
    </div>
</div>

<style>
.badge-published { background:#D1FAE5;color:#065F46; }
.badge-draft { background:#FEF3C7;color:#92400E; }
.badge-archived { background:#F1F5F9;color:#475569; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
function openCatModal() {
    document.getElementById('catModal').style.display = 'flex';
    loadCategories();
}
document.getElementById('catModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

function loadCategories() {
    fetch('<?= base_url('admin/knowledge-base/categories') ?>')
        .then(r => r.json()).then(cats => {
            const list = document.getElementById('catList');
            list.innerHTML = cats.map(c => `
                <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--gray-50);border-radius:8px;border:1px solid var(--gray-200)">
                    <i class="${c.icon}" style="color:${c.color};font-size:16px;width:20px;text-align:center"></i>
                    <span style="flex:1;font-size:13.5px;font-weight:500">${c.name}</span>
                    <span style="font-size:12px;color:var(--gray-400)">${c.article_count} artikel</span>
                    <button class="btn btn-sm btn-outline" style="color:#DC2626;border-color:#DC2626" onclick="deleteCategory(${c.id})"><i class="bi bi-trash"></i></button>
                </div>`).join('');
        });
}

function addCategory() {
    const name = document.getElementById('catName').value.trim();
    if (!name) return;
    fetch('<?= base_url('admin/knowledge-base/categories') ?>', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({name})
    }).then(() => { document.getElementById('catName').value = ''; loadCategories(); });
}

function deleteCategory(id) {
    if (!confirm('Hapus kategori ini?')) return;
    fetch(`<?= base_url('admin/knowledge-base/categories/') ?>${id}`, {method:'DELETE'})
        .then(() => loadCategories());
}

function reembed(id, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite"></i>';
    fetch(`<?= base_url('admin/knowledge-base/') ?>${id}/reembed`, {method:'POST'})
        .then(r => r.json())
        .then(d => {
            btn.innerHTML = '<i class="bi bi-check-lg"></i>';
            btn.style.color = '#065F46';
            setTimeout(() => { btn.innerHTML = '<i class="bi bi-stars"></i>'; btn.style.color = '#7C3AED'; btn.disabled = false; }, 2000);
        })
        .catch(() => { btn.innerHTML = '<i class="bi bi-stars"></i>'; btn.disabled = false; });
}

function reembedAll() {
    if (!confirm('Generate ulang embedding untuk semua artikel? Proses ini membutuhkan beberapa detik.')) return;
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Memproses...';
    fetch('<?= base_url('admin/knowledge-base/reembed-all') ?>', {method:'POST'})
        .then(r => r.json())
        .then(d => {
            alert(d.message);
            btn.innerHTML = '<i class="bi bi-stars"></i> Re-embed Semua';
            btn.disabled = false;
        })
        .catch(() => { btn.innerHTML = '<i class="bi bi-stars"></i> Re-embed Semua'; btn.disabled = false; });
}
</script>

<?= $this->endSection() ?>
