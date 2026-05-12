<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.article-content { line-height: 1.8; color: var(--gray-700); }
.article-content h1,.article-content h2,.article-content h3 { color: var(--gray-900); margin: 20px 0 10px; font-weight: 700; }
.article-content h2 { font-size: 18px; }
.article-content h3 { font-size: 16px; }
.article-content p { margin-bottom: 12px; }
.article-content ul,.article-content ol { padding-left: 20px; margin-bottom: 12px; }
.article-content li { margin-bottom: 4px; }
.article-content code { background: var(--gray-100); padding: 2px 6px; border-radius: 4px; font-size: 13px; }
.article-content pre { background: var(--gray-900); color: #e2e8f0; padding: 16px; border-radius: 8px; overflow-x: auto; margin-bottom: 12px; }
.article-content blockquote { border-left: 4px solid var(--primary); padding: 10px 16px; background: var(--primary-light); border-radius: 0 8px 8px 0; margin-bottom: 12px; }
.article-content img { max-width: 100%; border-radius: 8px; }
.article-content a { color: var(--primary); text-decoration: underline; }

.related-card { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--gray-100); text-decoration: none; color: inherit; }
.related-card:last-child { border-bottom: none; }
.related-card:hover .related-title { color: var(--primary); }
.related-title { font-size: 13px; font-weight: 500; color: var(--gray-800); line-height: 1.4; }
</style>

<!-- Breadcrumb -->
<nav style="font-size:13px;color:var(--gray-500);margin-bottom:16px;display:flex;align-items:center;gap:6px;flex-wrap:wrap">
    <a href="<?= base_url('knowledge-base') ?>" style="color:var(--primary)">Knowledge Base</a>
    <i class="bi bi-chevron-right" style="font-size:10px"></i>
    <a href="<?= base_url('knowledge-base?cat='.$article['category_id']) ?>" style="color:var(--primary)"><?= esc($article['category_name']) ?></a>
    <i class="bi bi-chevron-right" style="font-size:10px"></i>
    <span class="truncate" style="max-width:300px"><?= esc($article['title']) ?></span>
</nav>

<div style="display:grid;grid-template-columns:1fr 280px;gap:20px">
    <!-- Article -->
    <div>
        <div class="card">
            <div class="card-body" style="padding:28px">
                <!-- Header -->
                <div style="margin-bottom:20px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:600;background:<?= esc($article['category_color']) ?>22;color:<?= esc($article['category_color']) ?>">
                            <i class="<?= esc($article['category_icon']) ?>"></i> <?= esc($article['category_name']) ?>
                        </span>
                        <?php if ($article['read_time']): ?>
                        <span style="font-size:12px;color:var(--gray-400)"><i class="bi bi-clock"></i> <?= esc($article['read_time']) ?></span>
                        <?php endif; ?>
                        <span style="font-size:12px;color:var(--gray-400)"><i class="bi bi-eye"></i> <?= number_format($article['view_count']) ?> dibaca</span>
                    </div>
                    <h1 style="font-size:22px;font-weight:700;color:var(--gray-900);line-height:1.3;margin-bottom:8px"><?= esc($article['title']) ?></h1>
                    <?php if ($article['excerpt']): ?>
                    <p style="font-size:14px;color:var(--gray-500);line-height:1.6"><?= esc($article['excerpt']) ?></p>
                    <?php endif; ?>
                    <div style="font-size:12px;color:var(--gray-400);margin-top:8px">
                        Diperbarui: <?= date('d F Y', strtotime($article['updated_at'])) ?>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--gray-200);margin-bottom:20px">

                <!-- Content -->
                <div class="article-content">
                    <?= $article['content'] ?>
                </div>

                <!-- Tags -->
                <?php if ($article['tags']): ?>
                <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--gray-200)">
                    <span style="font-size:12px;font-weight:600;color:var(--gray-500);margin-right:8px">Tags:</span>
                    <?php foreach (explode(',', $article['tags']) as $tag): ?>
                    <a href="<?= base_url('knowledge-base/search?q='.urlencode(trim($tag))) ?>" style="display:inline-block;padding:3px 9px;border-radius:20px;background:var(--gray-100);color:var(--gray-600);font-size:12px;margin:2px;text-decoration:none"><?= esc(trim($tag)) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Feedback -->
                <div style="margin-top:24px;padding:16px;background:var(--gray-50);border-radius:10px;text-align:center">
                    <div style="font-size:13.5px;font-weight:600;color:var(--gray-700);margin-bottom:10px">Apakah artikel ini membantu?</div>
                    <div style="display:flex;justify-content:center;gap:10px">
                        <button class="btn btn-outline btn-sm" onclick="this.innerHTML='👍 Ya, terima kasih!';this.disabled=true"><i class="bi bi-hand-thumbs-up"></i> Ya</button>
                        <button class="btn btn-outline btn-sm" onclick="this.innerHTML='👎 Terima kasih';this.disabled=true"><i class="bi bi-hand-thumbs-down"></i> Tidak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <?php if (!empty($related)): ?>
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-link-45deg" style="color:var(--primary)"></i><span class="card-title">Artikel Terkait</span></div>
            <div class="card-body" style="padding:12px 16px">
                <?php foreach ($related as $r): ?>
                <a href="<?= base_url('knowledge-base/'.$r['slug']) ?>" class="related-card">
                    <div style="width:32px;height:32px;border-radius:8px;background:<?= esc($r['category_color']) ?>22;color:<?= esc($r['category_color']) ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px">
                        <i class="<?= esc($r['category_icon']) ?>"></i>
                    </div>
                    <div>
                        <div class="related-title"><?= esc($r['title']) ?></div>
                        <div style="font-size:11px;color:var(--gray-400);margin-top:2px"><i class="bi bi-eye"></i> <?= number_format($r['view_count']) ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card" style="background:linear-gradient(135deg,#2563EB,#7C3AED);border:none">
            <div class="card-body" style="padding:18px;text-align:center">
                <i class="bi bi-stars" style="font-size:24px;color:#fff;display:block;margin-bottom:8px"></i>
                <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:5px">Masih butuh bantuan?</div>
                <div style="font-size:12px;color:rgba(255,255,255,.8);margin-bottom:12px">Tanya AI atau buat tiket</div>
                <button onclick="window.openAiChat && window.openAiChat()" style="width:100%;padding:8px;background:#fff;color:#2563EB;border:none;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;margin-bottom:6px">
                    <i class="bi bi-stars"></i> Tanya AI
                </button>
                <a href="<?= base_url('tickets/create') ?>" style="display:block;padding:8px;background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;font-weight:600;font-size:13px;text-align:center">
                    <i class="bi bi-plus-circle"></i> Buat Tiket
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
