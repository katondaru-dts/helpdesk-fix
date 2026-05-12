<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <div class="page-header-title">Hasil Pencarian</div>
        <div class="page-header-sub"><?= count($articles) ?> hasil untuk "<strong><?= esc($keyword) ?></strong>"</div>
    </div>
    <a href="<?= base_url('knowledge-base') ?>" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card mb-4">
    <div class="card-body" style="padding:12px 16px">
        <form action="<?= base_url('knowledge-base/search') ?>" method="GET" style="display:flex;gap:8px">
            <div class="search-wrap" style="flex:1">
                <i class="bi bi-search"></i>
                <input type="text" name="q" class="form-control" value="<?= esc($keyword) ?>" placeholder="Cari artikel...">
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
        </form>
    </div>
</div>

<?php if (empty($articles)): ?>
    <div class="empty-state">
        <i class="bi bi-search"></i>
        <h4>Tidak ada hasil</h4>
        <p>Coba kata kunci lain atau <a href="<?= base_url('tickets/create') ?>" style="color:var(--primary)">buat tiket</a> untuk bantuan langsung.</p>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:10px">
    <?php foreach ($articles as $a): ?>
    <a href="<?= base_url('knowledge-base/'.$a['slug']) ?>" style="display:flex;gap:14px;align-items:flex-start;background:#fff;border-radius:10px;padding:16px 18px;border:1px solid var(--gray-200);text-decoration:none;color:inherit;transition:all .2s" onmouseover="this.style.borderColor='#2563EB'" onmouseout="this.style.borderColor='#E2E8F0'">
        <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:17px;color:var(--primary);flex-shrink:0">
            <i class="bi bi-file-text"></i>
        </div>
        <div style="flex:1">
            <div style="font-size:14px;font-weight:600;color:var(--gray-900);margin-bottom:4px"><?= esc($a['title']) ?></div>
            <?php if ($a['excerpt']): ?>
            <div style="font-size:12.5px;color:var(--gray-500);line-height:1.5;margin-bottom:6px"><?= esc($a['excerpt']) ?></div>
            <?php endif; ?>
            <div style="display:flex;align-items:center;gap:10px;font-size:11.5px;color:var(--gray-400)">
                <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:#DBEAFE;color:#1D4ED8"><?= esc($a['category_name']) ?></span>
                <span><i class="bi bi-eye"></i> <?= number_format($a['view_count']) ?></span>
            </div>
        </div>
        <i class="bi bi-chevron-right" style="color:var(--gray-300);font-size:14px;margin-top:4px"></i>
    </a>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
