<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <div class="page-header-title"><?= $article ? 'Edit Artikel' : 'Tambah Artikel Baru' ?></div>
        <div class="page-header-sub"><?= $article ? esc($article['title']) : 'Isi form di bawah untuk membuat artikel baru' ?></div>
    </div>
    <a href="<?= base_url('admin/knowledge-base') ?>" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<form action="<?= $article ? base_url('admin/knowledge-base/'.$article['id'].'/update') : base_url('admin/knowledge-base/store') ?>" method="POST" enctype="multipart/form-data">

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <!-- Main -->
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Judul Artikel <span style="color:#EF4444">*</span></label>
                    <input type="text" name="title" class="form-control" required value="<?= esc($article['title'] ?? '') ?>" placeholder="Judul yang jelas dan deskriptif...">
                </div>
                <div class="form-group">
                    <label class="form-label">Ringkasan / Excerpt</label>
                    <textarea name="excerpt" class="form-control" rows="2" placeholder="Deskripsi singkat (tampil di daftar artikel)..."><?= esc($article['excerpt'] ?? '') ?></textarea>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Konten Artikel <span style="color:#EF4444">*</span></label>
                    <div style="margin-bottom:8px;padding:10px 14px;background:#F0FDF4;border:1.5px dashed #6EE7B7;border-radius:8px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <i class="bi bi-file-earmark-text" style="color:#059669;font-size:18px"></i>
                        <div style="flex:1">
                            <div style="font-size:13px;font-weight:600;color:#065F46">Upload file Markdown (.md)</div>
                            <div style="font-size:11.5px;color:#6B7280">Konten akan otomatis diisi dari file. Judul diambil dari baris pertama (#).</div>
                        </div>
                        <input type="file" id="mdFile" name="md_file" accept=".md,.markdown" style="display:none" onchange="handleMdUpload(this)">
                        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('mdFile').click()">
                            <i class="bi bi-upload"></i> Pilih File .md
                        </button>
                        <span id="mdFileName" style="font-size:12px;color:#6B7280"></span>
                    </div>
                    <div style="border:1.5px solid var(--gray-300);border-radius:8px;overflow:hidden">
                        <div style="display:flex;gap:2px;flex-wrap:wrap;padding:8px;background:var(--gray-50);border-bottom:1px solid var(--gray-200)">
                            <?php foreach ([
                                ['bold','type-bold'],['italic','type-italic'],['underline','type-underline'],
                                ['|',''],
                                ['h2','type-h2'],['h3','type-h3'],
                                ['|',''],
                                ['ul','list-ul'],['ol','list-ol'],
                                ['|',''],
                                ['link','link-45deg'],['code','code-slash'],
                            ] as $btn): ?>
                            <?php if ($btn[0]==='|'): ?>
                                <div style="width:1px;background:var(--gray-300);margin:4px 2px"></div>
                            <?php else: ?>
                                <button type="button" onclick="fmt('<?= $btn[0] ?>')" style="width:30px;height:30px;border:none;background:transparent;border-radius:6px;cursor:pointer;color:var(--gray-600);display:flex;align-items:center;justify-content:center;font-size:14px" title="<?= $btn[0] ?>">
                                    <i class="bi bi-<?= $btn[1] ?>"></i>
                                </button>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <textarea id="contentEditor" name="content" style="width:100%;min-height:300px;padding:14px;border:none;outline:none;font-family:inherit;font-size:14px;resize:vertical;color:var(--gray-800)" placeholder="Tulis konten artikel di sini..."><?= $article['content'] ?? '' ?></textarea>
                    </div>
                    <div style="font-size:11.5px;color:var(--gray-400);margin-top:4px">Mendukung HTML dasar. Konten ini akan digunakan sebagai sumber AI jika opsi AI diaktifkan.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="card">
            <div class="card-header"><i class="bi bi-gear" style="color:var(--primary)"></i><span class="card-title">Pengaturan</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Kategori <span style="color:#EF4444">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Pilih kategori...</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($article['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['draft'=>'Draft','published'=>'Published','archived'=>'Archived'] as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= ($article['status'] ?? 'draft') == $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tags</label>
                    <input type="text" name="tags" class="form-control" value="<?= esc($article['tags'] ?? '') ?>" placeholder="password, reset, SSO">
                    <div class="form-text">Pisahkan dengan koma</div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Estimasi Waktu Baca</label>
                    <input type="text" name="read_time" class="form-control" value="<?= esc($article['read_time'] ?? '') ?>" placeholder="cth: 3 menit">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-stars" style="color:#7C3AED"></i><span class="card-title">AI Assistant</span></div>
            <div class="card-body">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer">
                    <input type="checkbox" name="use_for_ai" value="1" <?= ($article['use_for_ai'] ?? 1) ? 'checked' : '' ?> style="width:16px;height:16px;margin-top:2px;accent-color:#7C3AED">
                    <div>
                        <div style="font-size:13.5px;font-weight:500;color:var(--gray-800)">Gunakan sebagai sumber AI</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-top:2px">Artikel ini akan di-embed dan digunakan AI untuk menjawab pertanyaan pengguna (RAG)</div>
                    </div>
                </label>
                <?php if ($article && $article['embedding']): ?>
                <div style="margin-top:10px;padding:8px 10px;background:#F0FDF4;border-radius:8px;font-size:12px;color:#065F46">
                    <i class="bi bi-check-circle-fill"></i> Embedding sudah tersedia
                </div>
                <?php elseif ($article): ?>
                <div style="margin-top:10px;padding:8px 10px;background:#FEF3C7;border-radius:8px;font-size:12px;color:#92400E">
                    <i class="bi bi-exclamation-triangle"></i> Belum di-embed. Simpan ulang untuk generate embedding.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:8px">
            <button type="submit" name="status_submit" value="published" class="btn btn-primary w100"><i class="bi bi-send"></i> Publikasikan</button>
            <button type="submit" name="status_submit" value="draft" class="btn btn-outline w100"><i class="bi bi-floppy"></i> Simpan Draft</button>
        </div>
    </div>

</div>
</form>

<script>
function handleMdUpload(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('mdFileName').textContent = file.name;

    const reader = new FileReader();
    reader.onload = function(e) {
        const md = e.target.result;
        // Isi textarea konten dengan raw MD (server akan parse ke HTML saat submit)
        document.getElementById('contentEditor').value = md;

        // Auto-isi judul dari baris pertama # jika kosong
        const titleInput = document.querySelector('input[name="title"]');
        if (!titleInput.value.trim()) {
            const m = md.match(/^#\s+(.+)/m);
            if (m) titleInput.value = m[1].trim();
        }

        // Auto-isi excerpt jika kosong
        const excerptInput = document.querySelector('textarea[name="excerpt"]');
        if (!excerptInput.value.trim()) {
            const m = md.match(/^(?!#)[^\n]{20,}/m);
            if (m) excerptInput.value = m[0].trim().substring(0, 200);
        }
    };
    reader.readAsText(file);
}

function fmt(cmd) {
    const ta = document.getElementById('contentEditor');
    const start = ta.selectionStart, end = ta.selectionEnd;
    const sel = ta.value.substring(start, end);
    const tags = {
        bold: ['<strong>', '</strong>'],
        italic: ['<em>', '</em>'],
        underline: ['<u>', '</u>'],
        h2: ['<h2>', '</h2>'],
        h3: ['<h3>', '</h3>'],
        ul: ['<ul>\n  <li>', '</li>\n</ul>'],
        ol: ['<ol>\n  <li>', '</li>\n</ol>'],
        link: ['<a href="">', '</a>'],
        code: ['<code>', '</code>'],
    };
    if (!tags[cmd]) return;
    const [open, close] = tags[cmd];
    ta.value = ta.value.substring(0, start) + open + sel + close + ta.value.substring(end);
    ta.focus();
    ta.selectionStart = start + open.length;
    ta.selectionEnd = start + open.length + sel.length;
}
</script>

<?= $this->endSection() ?>
