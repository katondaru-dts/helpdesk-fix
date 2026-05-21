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
        <form action="<?= base_url('tickets/store') ?>" method="POST" enctype="multipart/form-data">
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
            <div style="margin-bottom:25px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Foto Dokumentasi <span
                        style="color:#6b7280">(opsional, maks 2 foto)</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <!-- Slot Foto 1 -->
                    <div>
                        <label id="drop1"
                            style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px dashed #d1d5db;border-radius:8px;cursor:pointer;transition:all .25s;background:#fff">
                            <i class="bi bi-camera" id="icon1"
                                style="font-size:18px;color:#3b82f6;transition:color .25s"></i>
                            <span id="label1"
                                style="font-size:13px;font-weight:600;color:#374151;transition:color .25s">Foto 1</span>
                            <span id="sub1" style="font-size:11px;color:#9ca3af;margin-left:auto">Pilih gambar</span>
                            <input type="file" name="photo" accept="image/*" id="photoInput" style="display:none">
                        </label>
                        <div id="photo1thumb" style="display:none;margin-top:6px;position:relative">
                            <img id="photo1preview" alt="Preview Foto 1"
                                style="width:100%;height:90px;object-fit:cover;border-radius:6px;border:2px solid #22c55e;cursor:zoom-in;transition:opacity .2s"
                                title="Klik untuk preview">
                            <button type="button" id="removePhoto1"
                                style="position:absolute;top:4px;right:4px;background:rgba(239,68,68,.9);color:white;border:none;border-radius:50%;width:22px;height:22px;font-size:13px;font-weight:bold;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;z-index:2"
                                title="Hapus foto">&times;</button>
                        </div>
                    </div>
                    <!-- Slot Foto 2 -->
                    <div>
                        <label id="drop2"
                            style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px dashed #d1d5db;border-radius:8px;cursor:pointer;transition:all .25s;background:#fff">
                            <i class="bi bi-camera" id="icon2"
                                style="font-size:18px;color:#3b82f6;transition:color .25s"></i>
                            <span id="label2"
                                style="font-size:13px;font-weight:600;color:#374151;transition:color .25s">Foto 2</span>
                            <span id="sub2" style="font-size:11px;color:#9ca3af;margin-left:auto">Pilih gambar</span>
                            <input type="file" name="photo2" accept="image/*" id="photoInput2" style="display:none">
                        </label>
                        <div id="photo2thumb" style="display:none;margin-top:6px;position:relative">
                            <img id="photo2preview" alt="Preview Foto 2"
                                style="width:100%;height:90px;object-fit:cover;border-radius:6px;border:2px solid #22c55e;cursor:zoom-in;transition:opacity .2s"
                                title="Klik untuk preview">
                            <button type="button" id="removePhoto2"
                                style="position:absolute;top:4px;right:4px;background:rgba(239,68,68,.9);color:white;border:none;border-radius:50%;width:22px;height:22px;font-size:13px;font-weight:bold;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;z-index:2"
                                title="Hapus foto">&times;</button>
                        </div>
                    </div>
                </div>
                <div style="font-size:12px;color:#9ca3af;margin-top:6px"><i class="bi bi-info-circle"></i> Format: JPG,
                    PNG. Maks 5MB per foto. Bisa ambil dari kamera.</div>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" id="submitBtn"
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
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle"
                        style="color:#3b82f6"></i> <b>Judul:</b> inti masalah</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle"
                        style="color:#3b82f6"></i> <b>Kategori:</b> pilih sesuai jenis</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle"
                        style="color:#3b82f6"></i> <b>Lokasi:</b> unit/ruang kejadian</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle"
                        style="color:#3b82f6"></i> <b>Deskripsi:</b> detail error & solusi yg sudah dicoba</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle"
                        style="color:#3b82f6"></i> <b>Nama pemohon:</b> nama pembuat tiket</li>
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
                                    <i class="bi bi-eye" style="font-size:10px"></i>
                                    <?= number_format($art['view_count']) ?>
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
<!-- ── Modal Preview Foto (Picasa Style) ── -->
<div id="photoModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(6px)">
    <div style="background:white;border-radius:16px;max-width:580px;width:100%;box-shadow:0 25px 60px rgba(0,0,0,.5);animation:modalIn .25s ease-out;overflow:hidden">
        <!-- Header -->
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:8px">
                <i class="bi bi-image" style="color:#3b82f6;font-size:18px"></i>
                <span style="font-weight:700;font-size:15px" id="modalPhotoTitle">Preview Foto</span>
            </div>
            <button type="button" id="modalClose" style="background:none;border:none;font-size:24px;color:#9ca3af;cursor:pointer;padding:4px;line-height:1;transition:color .2s" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#9ca3af'">&times;</button>
        </div>
        <!-- Zoom Container -->
        <div style="padding:20px;text-align:center;background:#f3f4f6;position:relative;overflow:hidden;min-height:200px;cursor:grab;user-select:none" id="zoomContainer">
            <img id="modalPhotoPreview" alt="Preview Foto" style="max-width:100%;max-height:400px;border-radius:8px;display:inline-block;box-shadow:0 4px 12px rgba(0,0,0,.2);transform-origin:center center;touch-action:none">
            <!-- Zoom Controls -->
            <div style="position:absolute;bottom:14px;right:14px;display:flex;gap:4px;align-items:center;background:rgba(255,255,255,.92);padding:5px 10px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.15)">
                <button type="button" id="zoomIn" style="background:none;border:none;cursor:pointer;font-size:20px;font-weight:700;color:#374151;padding:0 6px;line-height:1;border-radius:4px" title="Perbesar">+</button>
                <span id="zoomLevel" style="font-size:12px;font-weight:600;color:#6b7280;min-width:36px;text-align:center">100%</span>
                <button type="button" id="zoomOut" style="background:none;border:none;cursor:pointer;font-size:20px;font-weight:700;color:#374151;padding:0 6px;line-height:1;border-radius:4px" title="Perkecil">&minus;</button>
                <span style="width:1px;height:18px;background:#e5e7eb;display:inline-block;margin:0 2px"></span>
                <button type="button" id="zoomReset" style="background:none;border:none;cursor:pointer;font-size:13px;color:#3b82f6;padding:0 4px;line-height:1;border-radius:4px;font-weight:600" title="Reset zoom">Reset</button>
            </div>
        </div>
        <!-- Footer -->
        <div id="modalFooter" style="padding:14px 20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;align-items:center">
            <span id="modalMode" style="font-size:12px;color:#9ca3af;margin-right:auto"></span>
            <button type="button" id="modalCancel" style="padding:9px 18px;background:white;color:#6b7280;border:1px solid #d1d5db;border-radius:8px;font-weight:600;cursor:pointer;font-size:13px"><i class="bi bi-x"></i> Tutup</button>
            <button type="button" id="modalSubmit" style="display:none;padding:9px 22px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:white;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px;box-shadow:0 2px 8px rgba(59,130,246,.4)"><i class="bi bi-send-fill"></i> Kirim Laporan</button>
        </div>
    </div>
</div>
<style>
@keyframes modalIn { from { opacity:0; transform:translateY(-18px) scale(.96); } to { opacity:1; transform:translateY(0) scale(1); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // -- State --
    let data1 = '', data2 = '';
    let modalMode = 'preview';

    // -- Elements --
    const input1   = document.getElementById('photoInput');
    const input2   = document.getElementById('photoInput2');
    const drop1    = document.getElementById('drop1');
    const drop2    = document.getElementById('drop2');
    const label1   = document.getElementById('label1');
    const label2   = document.getElementById('label2');
    const icon1    = document.getElementById('icon1');
    const icon2    = document.getElementById('icon2');
    const sub1     = document.getElementById('sub1');
    const sub2     = document.getElementById('sub2');
    const thumb1   = document.getElementById('photo1thumb');
    const thumb2   = document.getElementById('photo2thumb');
    const prev1    = document.getElementById('photo1preview');
    const prev2    = document.getElementById('photo2preview');
    const rm1      = document.getElementById('removePhoto1');
    const rm2      = document.getElementById('removePhoto2');
    const form     = document.querySelector('form');
    const modal    = document.getElementById('photoModal');
    const modalTitle   = document.getElementById('modalPhotoTitle');
    const modalPreview = document.getElementById('modalPhotoPreview');
    const modalModeEl  = document.getElementById('modalMode');
    const modalSubmitBtn = document.getElementById('modalSubmit');
    const modalCancelBtn = document.getElementById('modalCancel');
    const modalCloseBtn  = document.getElementById('modalClose');

    // -- Helpers --
    function setGreen(drop, labelEl, iconEl, subEl, num) {
        drop.style.border      = '2px solid #22c55e';
        drop.style.background  = '#f0fdf4';
        labelEl.textContent    = 'Foto ' + num + ' \u2713';
        labelEl.style.color    = '#16a34a';
        iconEl.className       = 'bi bi-check-circle-fill';
        iconEl.style.color     = '#22c55e';
        subEl.textContent      = 'Ganti gambar';
    }

    function setNormal(drop, labelEl, iconEl, subEl, num) {
        drop.style.border      = '2px dashed #d1d5db';
        drop.style.background  = '#fff';
        labelEl.textContent    = 'Foto ' + num;
        labelEl.style.color    = '#374151';
        iconEl.className       = 'bi bi-camera';
        iconEl.style.color     = '#3b82f6';
        subEl.textContent      = 'Pilih gambar';
    }

    function openModal(src, mode, title) {
        modalMode = mode;
        modalPreview.src = src;
        modalTitle.textContent = title || 'Preview Foto';
        scale = 1; panX = 0; panY = 0; applyZoom();
        if (mode === 'submit') {
            modalSubmitBtn.style.display = 'inline-flex';
            modalSubmitBtn.style.alignItems = 'center';
            modalSubmitBtn.style.gap = '6px';
            modalModeEl.textContent = 'Periksa foto sebelum mengirim';
            modalCancelBtn.innerHTML = '<i class="bi bi-arrow-left"></i> Kembali';
        } else {
            modalSubmitBtn.style.display = 'none';
            modalModeEl.textContent = 'Scroll / pinch untuk zoom \u2022 Drag untuk geser';
            modalCancelBtn.innerHTML = '<i class="bi bi-x"></i> Tutup';
        }
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        scale = 1; panX = 0; panY = 0; applyZoom();
    }

    // -- Setup each photo slot --
    function setupSlot(input, drop, labelEl, iconEl, subEl, thumb, preview, removeBtn, num, getSet) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran foto maks 5MB!');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                getSet(e.target.result);
                preview.src = e.target.result;
                thumb.style.display = 'block';
            };
            reader.readAsDataURL(file);
            setGreen(drop, labelEl, iconEl, subEl, num);
        });

        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            input.value = '';
            getSet('');
            preview.src = '';
            thumb.style.display = 'none';
            setNormal(drop, labelEl, iconEl, subEl, num);
        });

        preview.addEventListener('click', function(e) {
            e.preventDefault();
            if (preview.src && preview.src !== window.location.href) {
                openModal(preview.src, 'preview', 'Preview Foto ' + num);
            }
        });
    }

    setupSlot(input1, drop1, label1, icon1, sub1, thumb1, prev1, rm1, 1, function(v) { if (v !== undefined) data1 = v; return data1; });
    setupSlot(input2, drop2, label2, icon2, sub2, thumb2, prev2, rm2, 2, function(v) { if (v !== undefined) data2 = v; return data2; });

    // -- Form submit --
    form.addEventListener('submit', function(e) {
        const hasPhoto = data1 || data2;
        if (hasPhoto) {
            e.preventDefault();
            openModal(data1 || data2, 'submit', 'Konfirmasi Kirim Laporan');
        }
    });

    // -- Modal buttons --
    modalCloseBtn.addEventListener('click', closeModal);
    modalCancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
    modalSubmitBtn.addEventListener('click', function() { closeModal(); form.submit(); });

    // -- Zoom & Drag --
    const zoomImg       = document.getElementById('modalPhotoPreview');
    const zoomContainer = document.getElementById('zoomContainer');
    const zoomInBtn     = document.getElementById('zoomIn');
    const zoomOutBtn    = document.getElementById('zoomOut');
    const zoomResetBtn  = document.getElementById('zoomReset');
    const zoomLevelEl   = document.getElementById('zoomLevel');
    let scale = 1, panX = 0, panY = 0;
    let isDragging = false, startX = 0, startY = 0;

    function applyZoom() {
        zoomImg.style.transform = 'translate(' + panX + 'px,' + panY + 'px) scale(' + scale + ')';
        zoomLevelEl.textContent = Math.round(scale * 100) + '%';
    }

    zoomInBtn.addEventListener('click', function() { scale = Math.min(scale + 0.25, 4); applyZoom(); });
    zoomOutBtn.addEventListener('click', function() { scale = Math.max(scale - 0.25, 0.25); applyZoom(); });
    zoomResetBtn.addEventListener('click', function() { scale = 1; panX = 0; panY = 0; applyZoom(); });

    zoomContainer.addEventListener('wheel', function(e) {
        e.preventDefault();
        scale = Math.min(Math.max(scale + (e.deltaY > 0 ? -0.12 : 0.12), 0.25), 4);
        applyZoom();
    }, { passive: false });

    zoomContainer.addEventListener('mousedown', function(e) {
        if (e.target.tagName === 'BUTTON' || e.target.tagName === 'SPAN') return;
        isDragging = true;
        startX = e.clientX - panX;
        startY = e.clientY - panY;
        zoomContainer.style.cursor = 'grabbing';
        e.preventDefault();
    });
    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        panX = e.clientX - startX;
        panY = e.clientY - startY;
        applyZoom();
    });
    document.addEventListener('mouseup', function() {
        if (isDragging) { isDragging = false; zoomContainer.style.cursor = 'grab'; }
    });

    let lastTouchDist = 0;
    zoomContainer.addEventListener('touchstart', function(e) {
        if (e.target.tagName === 'BUTTON') return;
        if (e.touches.length === 2) {
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            lastTouchDist = Math.sqrt(dx*dx + dy*dy);
        } else {
            isDragging = true;
            startX = e.touches[0].clientX - panX;
            startY = e.touches[0].clientY - panY;
        }
    }, { passive: true });
    zoomContainer.addEventListener('touchmove', function(e) {
        if (e.touches.length === 2) {
            e.preventDefault();
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            const dist = Math.sqrt(dx*dx + dy*dy);
            if (lastTouchDist > 0) {
                scale = Math.min(Math.max(scale + (dist - lastTouchDist) / 120, 0.25), 4);
                applyZoom();
            }
            lastTouchDist = dist;
        } else if (isDragging) {
            panX = e.touches[0].clientX - startX;
            panY = e.touches[0].clientY - startY;
            applyZoom();
        }
    }, { passive: false });
    zoomContainer.addEventListener('touchend', function() {
        isDragging = false; lastTouchDist = 0;
    }, { passive: true });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
    });
});
</script>

<?= $this->endSection() ?>
