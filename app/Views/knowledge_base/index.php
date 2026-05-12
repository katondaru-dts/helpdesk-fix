<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.kb-hero {
    background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
    border-radius: 16px; padding: 36px 28px; text-align: center; margin-bottom: 24px; position: relative; overflow: hidden;
}
.kb-hero h1 { font-size: 24px; font-weight: 700; color: #fff; margin-bottom: 6px; }
.kb-hero p { color: rgba(255,255,255,.8); margin-bottom: 20px; }
.kb-search-wrap {
    max-width: 500px; margin: 0 auto; display: flex;
    background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.2);
}
.kb-search-wrap input { flex: 1; padding: 13px 16px; border: none; outline: none; font-family: inherit; font-size: 14px; }
.kb-search-wrap button { padding: 0 18px; background: #2563EB; color: #fff; border: none; cursor: pointer; font-size: 17px; }
.kb-stats { display: flex; justify-content: center; gap: 24px; margin-top: 16px; }
.kb-stat strong { color: #fff; font-size: 18px; font-weight: 700; display: block; }
.kb-stat span { color: rgba(255,255,255,.75); font-size: 12px; }

.cat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
.cat-card {
    background: #fff; border-radius: 10px; padding: 16px; border: 1.5px solid var(--gray-200);
    cursor: pointer; transition: all .2s; display: flex; flex-direction: column; gap: 6px;
    text-decoration: none; color: inherit;
}
.cat-card:hover { border-color: var(--primary); box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-2px); }
.cat-card.active { border-color: var(--primary); background: var(--primary-light); }
.cat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
.cat-card h4 { font-size: 13px; font-weight: 600; color: var(--gray-900); }
.cat-card span { font-size: 11.5px; color: var(--gray-500); }

.kb-layout { display: grid; grid-template-columns: 1fr 280px; gap: 20px; }
.article-card {
    background: #fff; border-radius: 10px; padding: 16px 18px; border: 1px solid var(--gray-200);
    display: flex; gap: 14px; align-items: flex-start; transition: all .2s; text-decoration: none; color: inherit;
}
.article-card:hover { border-color: var(--primary); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.article-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 17px; color: var(--primary); flex-shrink: 0; }
.article-title { font-size: 14px; font-weight: 600; color: var(--gray-900); margin-bottom: 4px; }
.article-excerpt { font-size: 12.5px; color: var(--gray-500); line-height: 1.5; margin-bottom: 8px; }
.article-meta { display: flex; align-items: center; gap: 10px; font-size: 11.5px; color: var(--gray-400); flex-wrap: wrap; }
.cat-pill { padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }

.widget { background: #fff; border-radius: 10px; border: 1px solid var(--gray-200); overflow: hidden; margin-bottom: 16px; }
.widget-header { padding: 12px 16px; border-bottom: 1px solid var(--gray-100); font-size: 13.5px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.widget-header i { color: var(--primary); }
.widget-body { padding: 12px 16px; }
.popular-item { display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid var(--gray-100); text-decoration: none; color: inherit; }
.popular-item:last-child { border-bottom: none; }
.popular-item:hover .popular-title { color: var(--primary); }
.popular-num { width: 20px; height: 20px; border-radius: 5px; background: var(--gray-100); color: var(--gray-500); font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.popular-title { font-size: 12.5px; color: var(--gray-700); flex: 1; line-height: 1.4; }
.tag-cloud { display: flex; flex-wrap: wrap; gap: 6px; }
.tag { padding: 3px 9px; border-radius: 20px; background: var(--gray-100); color: var(--gray-600); font-size: 12px; cursor: pointer; transition: all .15s; text-decoration: none; }
.tag:hover { background: var(--primary-light); color: var(--primary); }

@media (max-width: 1024px) { .cat-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) {
    .cat-grid { grid-template-columns: repeat(2, 1fr); }
    .kb-layout { grid-template-columns: 1fr; }
    .kb-sidebar { display: none; }
}
@media (max-width: 480px) {
    .kb-hero { padding: 24px 16px; }
    .kb-hero h1 { font-size: 19px; }
    .cat-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .article-card { flex-direction: column; }
}
</style>

<!-- Hero -->
<div class="kb-hero">
    <h1><i class="bi bi-book"></i> Pusat Bantuan & Knowledge Base</h1>
    <p>Temukan jawaban cepat dari artikel panduan dan FAQ kami</p>
    <form action="<?= base_url('knowledge-base/search') ?>" method="GET" class="kb-search-wrap">
        <input type="text" name="q" placeholder="Cari artikel, panduan, atau FAQ..." value="<?= esc(service('request')->getGet('q') ?? '') ?>">
        <button type="submit"><i class="bi bi-search"></i></button>
    </form>
    <div class="kb-stats">
        <div class="kb-stat"><strong><?= $total ?></strong><span>Artikel</span></div>
        <div class="kb-stat"><strong><?= count($categories) ?></strong><span>Kategori</span></div>
    </div>
</div>

<!-- Categories -->
<div class="section-title mb-3"><i class="bi bi-grid-3x3-gap" style="color:var(--primary)"></i> Kategori</div>
<div class="cat-grid mb-4">
    <a href="<?= base_url('knowledge-base') ?>" class="cat-card <?= !$currentCat ? 'active' : '' ?>">
        <div class="cat-icon" style="background:#F1F5F9;color:#475569"><i class="bi bi-grid"></i></div>
        <h4>Semua Artikel</h4>
        <span><?= $total ?> artikel</span>
    </a>
    <?php foreach ($categories as $cat): ?>
    <a href="<?= base_url('knowledge-base?cat=' . $cat['id']) ?>" class="cat-card <?= $currentCat == $cat['id'] ? 'active' : '' ?>">
        <div class="cat-icon" style="background:<?= esc($cat['color']) ?>22;color:<?= esc($cat['color']) ?>">
            <i class="<?= esc($cat['icon']) ?>"></i>
        </div>
        <h4><?= esc($cat['name']) ?></h4>
        <span><?= $cat['article_count'] ?> artikel</span>
    </a>
    <?php endforeach; ?>
</div>

<!-- Articles + Sidebar -->
<div class="kb-layout">
    <div>
        <div class="section-title mb-3">
            <i class="bi bi-file-text" style="color:var(--primary)"></i> Artikel
            <span class="text-muted text-sm" style="font-weight:400"><?= $total ?> artikel</span>
        </div>

        <?php if (empty($articles)): ?>
            <div class="empty-state"><i class="bi bi-inbox"></i><h4>Belum ada artikel</h4><p>Belum ada artikel yang dipublikasikan.</p></div>
        <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:10px">
            <?php foreach ($articles as $a): ?>
            <a href="<?= base_url('knowledge-base/' . $a['slug']) ?>" class="article-card">
                <div class="article-icon" style="background:<?= esc($a['category_color']) ?>22;color:<?= esc($a['category_color']) ?>">
                    <i class="<?= esc($a['category_icon']) ?>"></i>
                </div>
                <div style="flex:1">
                    <div class="article-title"><?= esc($a['title']) ?></div>
                    <?php if ($a['excerpt']): ?>
                    <div class="article-excerpt"><?= esc($a['excerpt']) ?></div>
                    <?php endif; ?>
                    <div class="article-meta">
                        <span class="cat-pill" style="background:<?= esc($a['category_color']) ?>22;color:<?= esc($a['category_color']) ?>"><?= esc($a['category_name']) ?></span>
                        <span><i class="bi bi-eye"></i> <?= number_format($a['view_count']) ?></span>
                        <?php if ($a['read_time']): ?><span><i class="bi bi-clock"></i> <?= esc($a['read_time']) ?></span><?php endif; ?>
                        <span><i class="bi bi-calendar3"></i> <?= date('d M Y', strtotime($a['created_at'])) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php $totalPages = ceil($total / $perPage); if ($totalPages > 1): ?>
            <div class="pagination mt-4">
                <?php if ($currentPage > 1): ?>
                    <a href="?<?= http_build_query(['cat'=>$currentCat,'page'=>$currentPage-1]) ?>" class="pg-btn"><i class="bi bi-chevron-left"></i></a>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="?<?= http_build_query(['cat'=>$currentCat,'page'=>$p]) ?>" class="pg-btn <?= $p==$currentPage?'active':'' ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?<?= http_build_query(['cat'=>$currentCat,'page'=>$currentPage+1]) ?>" class="pg-btn"><i class="bi bi-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="kb-sidebar">
        <div class="widget">
            <div class="widget-header"><i class="bi bi-fire"></i> Artikel Populer</div>
            <div class="widget-body">
                <?php foreach (array_slice($popular, 0, 5) as $i => $p): ?>
                <a href="<?= base_url('knowledge-base/' . $p['slug']) ?>" class="popular-item">
                    <div class="popular-num"><?= $i+1 ?></div>
                    <div class="popular-title"><?= esc($p['title']) ?></div>
                    <div style="font-size:11px;color:var(--gray-400)"><?= number_format($p['view_count']) ?> 👁</div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="widget" style="background:linear-gradient(135deg,#2563EB,#7C3AED);border:none">
            <div class="widget-body" style="padding:18px;text-align:center">
                <i class="bi bi-stars" style="font-size:26px;color:#fff;display:block;margin-bottom:8px"></i>
                <div style="font-size:13.5px;font-weight:700;color:#fff;margin-bottom:5px">Tidak menemukan jawaban?</div>
                <div style="font-size:12px;color:rgba(255,255,255,.8);margin-bottom:12px">Tanya AI Assistant atau buat tiket</div>
                <button onclick="window.openAiChat && window.openAiChat()" style="width:100%;padding:8px;background:#fff;color:#2563EB;border:none;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;margin-bottom:6px">
                    <i class="bi bi-stars"></i> Tanya AI
                </button>
                <a href="<?= base_url('tickets/create') ?>" style="display:block;width:100%;padding:8px;background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;font-weight:600;font-size:13px;text-align:center">
                    <i class="bi bi-plus-circle"></i> Buat Tiket
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
