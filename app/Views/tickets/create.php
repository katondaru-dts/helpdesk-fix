<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-header-title">Buat Tiket Baru</div>
        <div class="page-header-sub">Laporkan gangguan atau masalah yang Anda hadapi</div>
    </div>
    <a href="<?= base_url('tickets') ?>" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<div class="grid" style="display:grid;grid-template-columns:1fr 340px;gap:20px">
    <div class="card" style="background:white;border-radius:12px;padding:30px">
        <?php if (session()->getFlashdata('error')): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:15px">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <form action="<?= base_url('tickets/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Judul Gangguan <span
                        style="color:#991b1b">*</span></label>
                <input type="text" name="title" required maxlength="200"
                    style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;"
                    placeholder="Contoh: Printer ruang finance tidak bisa print">
            </div>
            <?php $isStaff = in_array(session()->get('role_id'), [1, 2, 4]); ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
                <div style="<?= $isStaff ? '' : 'grid-column:span 2' ?>">
                    <label style="display:block;margin-bottom:8px;font-weight:600">Kategori <span
                            style="color:#991b1b">*</span></label>
                    <select name="cat_id" required
                        style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($isStaff): ?>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:600">Prioritas</label>
                        <select name="priority" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;">
                            <option value="LOW">Low</option>
                            <option value="MEDIUM" selected>Medium</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Nama Pemohon <span
                        style="color:#991b1b">*</span></label>
                <input type="text" name="requester_name" required maxlength="100"
                    style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;"
                    placeholder="Contoh: John Doe">
            </div>
            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Lokasi Gangguan <span
                        style="color:#991b1b">*</span></label>
                <select name="location" required
                    style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;">
                    <option value="">-- Pilih Unit / Lokasi --</option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= esc($unit) ?>"><?= esc($unit) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom:25px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Deskripsi Gangguan <span
                        style="color:#991b1b">*</span></label>
                <textarea name="description" rows="6" required
                    style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;"
                    placeholder="Jelaskan masalah secara rinci..."></textarea>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit"
                    style="background:#3b82f6;color:white;padding:12px 25px;border:none;border-radius:8px;font-weight:bold;cursor:pointer"><i
                        class="bi bi-send-fill"></i> Kirim Laporan</button>
                <button type="reset"
                    style="background:white;color:#6b7280;padding:12px 25px;border:1px solid #d1d5db;border-radius:8px;font-weight:bold;cursor:pointer">Reset</button>
            </div>
        </form>
    </div>
    <div style="display:flex;flex-direction:column;gap:16px">

        <?php /* Tips Pelaporan */ ?>
        <div class="card" style="background:white;border-radius:12px;padding:20px">
            <div style="font-weight:bold;margin-bottom:15px"><i class="bi bi-lightbulb" style="color:#f59e0b"></i> Tips
                Pelaporan</div>
            <ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:12px">
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle" style="color:#3b82f6"></i> <b>Judul:</b> inti masalah</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle" style="color:#3b82f6"></i> <b>Kategori:</b> pilih sesuai jenis</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle" style="color:#3b82f6"></i> <b>Lokasi:</b> unit/ruang kejadian</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle" style="color:#3b82f6"></i> <b>Deskripsi:</b> detail error & solusi yg sudah dicoba</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle" style="color:#3b82f6"></i> <b>Nama pemohon:</b> nama pembuat tiket</li>
            </ul>
        </div>

        <?php /* Artikel Populer */ ?>
        <?php if (!empty($popularArticles)): ?>
            <div class="card" style="background:white;border-radius:12px;padding:20px">
                <div style="font-weight:bold;margin-bottom:15px;display:flex;align-items:center;gap:8px">
                    <i class="bi bi-fire" style="color:#ef4444"></i> Artikel Populer
                </div>
                <ol style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0">
                    <?php foreach ($popularArticles as $i => $art): ?>
                        <li
                            style="display:flex;align-items:flex-start;gap:10px;padding:10px 0;<?= $i < count($popularArticles) - 1 ? 'border-bottom:1px solid #f3f4f6' : '' ?>">
                            <span
                                style="min-width:22px;height:22px;background:#eff6ff;color:#3b82f6;border-radius:50%;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0"><?= $i + 1 ?></span>
                            <div style="flex:1;min-width:0">
                                <a href="<?= base_url('knowledge-base/' . esc($art['slug'])) ?>" target="_blank"
                                    style="font-size:13px;color:#1e40af;text-decoration:none;font-weight:500;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden"
                                    title="<?= esc($art['title']) ?>">
                                    <?= esc($art['title']) ?>
                                </a>
                                <span style="font-size:11px;color:#9ca3af;margin-top:3px;display:block">
                                    <i class="bi bi-eye" style="font-size:10px"></i> <?= number_format($art['view_count']) ?>
                                    dilihat
                                </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>

        <?php /* Tidak menemukan jawaban? */ ?>
        <div
            style="background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 100%);border-radius:12px;padding:24px;text-align:center;color:white">
            <div style="font-size:28px;margin-bottom:8px">✨</div>
            <div style="font-weight:700;font-size:15px;margin-bottom:6px">Tidak menemukan jawaban?</div>
            <div style="font-size:13px;opacity:.85;margin-bottom:16px;line-height:1.5">Tanya AI Assistant atau cari di
                Knowledge Base kami</div>
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="<?= base_url('dashboard') ?>?open_ai=1"
                    style="display:block;background:white;color:#6366f1;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none"
                    onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                    ✦ Tanya AI
                </a>
                <a href="<?= base_url('knowledge-base') ?>"
                    style="display:block;background:rgba(255,255,255,.15);color:white;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,.3)"
                    onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                    <i class="bi bi-book"></i> Knowledge Base
                </a>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>