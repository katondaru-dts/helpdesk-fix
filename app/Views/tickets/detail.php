<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
.timeline-scroll {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 10px;
}
.timeline-scroll::-webkit-scrollbar {
    width: 6px;
}
.timeline-scroll::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 4px;
}
.timeline-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.timeline-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
<div class="grid" style="grid-template-columns:1fr 300px;gap:20px;max-width:1200px;margin:0 auto">
    <!-- Left Column -->
    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px">
                <div>
                    <h2 style="margin:0;font-size:20px;color:var(--primary)">#<?= $ticket['id'] ?>: <?= esc($ticket['title']) ?></h2>
                    <p style="color:#6b7280;margin:5px 0 0">Oleh <?= esc($ticket['reporter_name']) ?> &bull; <?= date('d M Y H:i', strtotime($ticket['created_at'])) ?></p>
                </div>
                <div style="text-align:right">
                    <span class="badge" style="display:inline-block;margin-bottom:5px"><?= $ticket['status'] ?></span><br>
                    <span class="badge" style="background:#f3f4f6;color:#374151"><?= $ticket['priority'] ?></span>
                </div>
            </div>
            <div style="border-top:1px solid #eee;padding-top:20px;line-height:1.6;color:#374151;white-space:pre-wrap"><?= esc($ticket['description']) ?></div>
            <?php if ($ticket['location']): ?>
                <div style="margin-top:15px;font-size:13px;color:#6b7280"><i class="bi bi-geo-alt"></i> Lokasi: <?= esc($ticket['location']) ?></div>
            <?php endif; ?>
            <?php if ($ticket['photo'] || $ticket['photo2']): ?>
                <div style="margin-top:15px">
                    <div style="font-size:13px;font-weight:600;color:#6b7280;margin-bottom:8px"><i class="bi bi-image"></i> Foto Dokumentasi</div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap">
                        <?php if ($ticket['photo']): ?>
                            <img src="<?= $ticket['photo'] ?>" alt="Foto 1" class="ticketPhoto" data-photo="<?= $ticket['photo'] ?>" style="max-width:48%;height:200px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;cursor:pointer;flex:1;min-width:200px">
                        <?php endif; ?>
                        <?php if ($ticket['photo2']): ?>
                            <img src="<?= $ticket['photo2'] ?>" alt="Foto 2" class="ticketPhoto" data-photo="<?= $ticket['photo2'] ?>" style="max-width:48%;height:200px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;cursor:pointer;flex:1;min-width:200px">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- History/Messages -->
        <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
            <div style="font-weight:bold;margin-bottom:20px;font-size:16px"><i class="bi bi-clock-history" style="color:var(--primary)"></i> Riwayat &amp; Balasan</div>
            <div class="timeline-scroll" style="display:flex;flex-direction:column;gap:20px">
                <?php if (empty($timeline)): ?>
                    <p style="text-align:center;color:#9ca3af;font-style:italic">Belum ada balasan.</p>
                <?php else: ?>
                    <?php foreach ($timeline as $item): ?>
                        <?php if ($item['type'] === 'msg'): ?>
                            <div style="display:flex;gap:15px;<?= $item['internal'] ? 'background:#fffbeb;padding:10px;border-radius:8px' : '' ?>">
                                <div style="width:35px;height:35px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:bold;color:#1d4ed8">
                                    <?= substr($item['by'] ?: 'User', 0, 1) ?>
                                </div>
                                <div style="flex:1">
                                    <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                                        <span style="font-weight:bold;font-size:14px">
                                            <?= esc($item['by'] ?: 'User') ?>
                                            <?php if ($item['internal']): ?><span style="font-size:10px;background:#fbbf24;color:white;padding:2px 6px;border-radius:10px;margin-left:5px">Internal</span><?php endif; ?>
                                        </span>
                                        <span style="font-size:12px;color:#9ca3af"><?= date('d/m/Y H:i', strtotime($item['at'])) ?></span>
                                    </div>
                                    <div style="font-size:14px;color:#4b5563;line-height:1.5;white-space:pre-wrap"><?= esc($item['msg']) ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="display:flex;gap:15px;align-items:flex-start">
                                <div style="width:35px;height:35px;background:#e5e7eb;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#6b7280">
                                    <i class="bi bi-arrow-repeat" style="font-size:14px"></i>
                                </div>
                                <div style="flex:1">
                                    <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                                        <span style="font-size:13px;color:#6b7280">
                                            <b><?= esc($item['by'] ?: 'System') ?></b> mengubah status ke
                                            <span class="badge" style="font-size:11px"><?= esc($item['status']) ?></span>
                                        </span>
                                        <span style="font-size:12px;color:#9ca3af"><?= date('d/m/Y H:i', strtotime($item['at'])) ?></span>
                                    </div>
                                    <?php if ($item['notes']): ?>
                                        <div style="font-size:13px;color:#6b7280;font-style:italic"><?= esc($item['notes']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($ticket['status'] != 'CLOSED'): ?>
            <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
                <div style="font-weight:bold;margin-bottom:15px;font-size:16px"><i class="bi bi-chat-dots" style="color:var(--primary)"></i> Tambah Balasan</div>
                <form action="<?= base_url('tickets/reply/' . $ticket['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <textarea name="message" class="form-control mb-3" rows="4" placeholder="Tulis balasan..." required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:15px"></textarea>
                    <?php if ($isStaff): ?>
                        <label style="display:flex;align-items:center;gap:8px;font-size:14px;color:#6b7280;margin-bottom:15px;cursor:pointer">
                            <input type="checkbox" name="is_internal"> Pesan internal (hanya terlihat Staff)
                        </label>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary" style="background:#3b82f6;color:white;padding:10px 20px;border:none;border-radius:8px;font-weight:bold;cursor:pointer">
                        <i class="bi bi-send"></i> Kirim Balasan
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column -->
    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
            <div style="font-weight:bold;margin-bottom:15px;font-size:16px"><i class="bi bi-info-circle" style="color:var(--primary)"></i> Info Tiket</div>
            <div style="display:flex;flex-direction:column;gap:15px">
                <div><div style="font-size:10px;color:#9ca3af;margin-bottom:3px;font-weight:bold">STATUS</div><span class="badge"><?= $ticket['status'] ?></span></div>
                <div><div style="font-size:10px;color:#9ca3af;margin-bottom:3px;font-weight:bold">PRIORITAS</div><span class="badge"><?= $ticket['priority'] ?></span></div>
                <div><div style="font-size:10px;color:#9ca3af;margin-bottom:3px;font-weight:bold">KATEGORI</div><div style="font-size:14px;font-weight:600"><?= esc($ticket['cat_name']) ?></div></div>
                <div><div style="font-size:10px;color:#9ca3af;margin-bottom:3px;font-weight:bold">PELAPOR</div><div style="font-size:14px"><?= esc($ticket['reporter_name']) ?></div></div>
                <?php if ($ticket['requester_name']): ?>
                <div><div style="font-size:10px;color:#9ca3af;margin-bottom:3px;font-weight:bold">NAMA PEMOHON</div><div style="font-size:14px"><?= esc($ticket['requester_name']) ?></div></div>
                <?php endif; ?>
                <div><div style="font-size:10px;color:#9ca3af;margin-bottom:3px;font-weight:bold">DITANGANI</div><div style="font-size:14px"><?= esc($ticket['assigned_name'] ?: 'Belum diassign') ?></div></div>
                <div><div style="font-size:10px;color:#9ca3af;margin-bottom:3px;font-weight:bold">DIBUAT</div><div style="font-size:14px"><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></div></div>
                
                <?php if ($ticket['sla_deadline'] && !in_array($ticket['status'], ['RESOLVED', 'CLOSED'])): ?>
                    <div style="margin-top:5px; padding:10px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">
                        <div style="font-size:10px;color:#9ca3af;margin-bottom:5px;font-weight:bold;text-align:center">SISA WAKTU (SLA)</div>
                        <?php if ($ticket['status'] === 'PENDING'): ?>
                            <div style="font-size:18px; font-weight:700; text-align:center; color:#f59e0b">
                                <i class="bi bi-pause-fill"></i> PAUSED
                            </div>
                        <?php else: ?>
                            <div class="sla-timer" data-deadline="<?= date('c', strtotime($ticket['sla_deadline'])) ?>" style="font-size:18px; font-weight:700; text-align:center; color:var(--primary)">
                                Menghitung...
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($canUpdateStatus || $canAssign): ?>
            <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
                <div style="font-weight:bold;margin-bottom:15px;font-size:16px"><i class="bi bi-gear" style="color:var(--primary)"></i> Aksi Staff</div>
                
                <?php if ($canUpdateStatus): ?>
                    <form action="<?= base_url('tickets/update-status/' . $ticket['id']) ?>" method="POST" style="margin-bottom:20px">
                        <?= csrf_field() ?>
                        <div style="margin-bottom:10px">
                            <label style="display:block;margin-bottom:5px;font-size:12px;font-weight:bold">Update Status</label>
                            <select name="new_status" class="form-select" style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;">
                                <?php 
                                $statuses = ['OPEN', 'IN_PROGRESS', 'PENDING', 'RESOLVED', 'CLOSED'];
                                foreach($statuses as $st): 
                                    if (session()->get('role_id') == 2 && $st === 'CLOSED') continue;
                                ?>
                                    <option value="<?= $st ?>" <?= $ticket['status'] == $st ? 'selected' : '' ?>><?= $st ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="margin-bottom:10px">
                            <label style="display:block;margin-bottom:5px;font-size:12px;font-weight:bold">Update Prioritas</label>
                            <select name="new_priority" class="form-select" style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;">
                                <option value="LOW" <?= $ticket['priority'] == 'LOW' ? 'selected' : '' ?>>LOW</option>
                                <option value="MEDIUM" <?= $ticket['priority'] == 'MEDIUM' ? 'selected' : '' ?>>MEDIUM</option>
                                <option value="HIGH" <?= $ticket['priority'] == 'HIGH' ? 'selected' : '' ?>>HIGH</option>
                                <option value="URGENT" <?= $ticket['priority'] == 'URGENT' ? 'selected' : '' ?>>URGENT</option>
                            </select>
                        </div>

                        <div style="margin-bottom:10px">
                            <label style="display:block;margin-bottom:5px;font-size:12px;font-weight:bold">Catatan Perubahan</label>
                            <input type="text" name="notes" class="form-control" placeholder="Alasan perubahan..." style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;background:#3b82f6;color:white;border:none;border-radius:8px;font-weight:bold;cursor:pointer">Simpan Perubahan</button>
                    </form>
                <?php endif; ?>
                
                <?php if ($canAssign): ?>
                    <div style="border-top:1px solid #eee;padding-top:20px">
                        <form action="<?= base_url('tickets/assign/' . $ticket['id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            <label style="display:block;margin-bottom:5px;font-size:12px;font-weight:bold">Assign ke Support</label>
                            <select name="assignee" class="form-select mb-2" style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:10px">
                                <option value="">-- Lepas Tugas --</option>
                                <?php foreach ($supports as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= $ticket['assigned_to'] == $s['id'] ? 'selected' : '' ?>><?= esc($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-outline" style="width:100%;padding:10px;background:white;color:#3b82f6;border:1px solid #3b82f6;border-radius:8px;font-weight:bold;cursor:pointer">Simpan Penugasan</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Modal Preview Foto (Picasa Style) ── -->
<div id="photoModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(6px)">
    <div style="background:white;border-radius:16px;max-width:640px;width:100%;box-shadow:0 25px 60px rgba(0,0,0,.5);animation:modalIn .25s ease-out;overflow:hidden">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:8px">
                <i class="bi bi-image" style="color:#3b82f6;font-size:18px"></i>
                <span style="font-weight:700;font-size:15px">Foto Dokumentasi</span>
            </div>
            <button type="button" id="modalClose" style="background:none;border:none;font-size:22px;color:#9ca3af;cursor:pointer;padding:4px;line-height:1">&times;</button>
        </div>
        <div style="padding:20px;text-align:center;background:#f3f4f6;position:relative;overflow:hidden;cursor:grab" id="zoomContainer">
                <img id="modalPhotoPreview" style="max-width:100%;max-height:480px;border-radius:8px;display:inline-block;box-shadow:0 4px 12px rgba(0,0,0,.15);transition:transform .2s ease;transform-origin:center center">
                <div style="position:absolute;bottom:12px;right:12px;display:flex;gap:6px;background:rgba(255,255,255,.9);padding:6px 10px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.15)">
                    <button type="button" id="zoomIn" style="background:none;border:none;cursor:pointer;font-size:18px;color:#374151;padding:2px 6px;line-height:1;border-radius:4px" title="Perbesar">+</button>
                    <span id="zoomLevel" style="font-size:12px;font-weight:600;color:#6b7280;min-width:32px;text-align:center;line-height:26px">100%</span>
                    <button type="button" id="zoomOut" style="background:none;border:none;cursor:pointer;font-size:18px;color:#374151;padding:2px 6px;line-height:1;border-radius:4px" title="Perkecil">&minus;</button>
                    <button type="button" id="zoomReset" style="background:none;border:none;cursor:pointer;font-size:14px;color:#3b82f6;padding:2px 6px;line-height:1;border-radius:4px" title="Reset zoom">&circlearrowleft;</button>
                </div>
            </div>
        <div style="padding:14px 20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end">
            <button type="button" id="modalCloseBtn" style="padding:9px 18px;background:white;color:#6b7280;border:1px solid #d1d5db;border-radius:8px;font-weight:600;cursor:pointer;font-size:13px"><i class="bi bi-x-lg"></i> Tutup</button>
        </div>
    </div>
</div>
<style>
@keyframes modalIn { from { opacity:0; transform:translateY(-20px) scale(.96); } to { opacity:1; transform:translateY(0) scale(1); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateTimers() {
        const now = new Date().getTime();
        const timers = document.querySelectorAll('.sla-timer');
        
        timers.forEach(el => {
            const deadlineStr = el.getAttribute('data-deadline');
            if (!deadlineStr) return;

            const deadline = new Date(deadlineStr).getTime();
            if (isNaN(deadline)) return;

            const diff = deadline - now;

            if (diff <= 0) {
                el.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Overdue';
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

    // ── Preview Foto Picasa Style ──
    const ticketPhotos = document.querySelectorAll('.ticketPhoto');
    const photoModal = document.getElementById('photoModal');
    const modalPreview = document.getElementById('modalPhotoPreview');
    const modalClose = document.getElementById('modalClose');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    ticketPhotos.forEach(function(img) {
        img.addEventListener('click', function() {
            modalPreview.src = this.dataset.photo;
            photoModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });

    function closePhotoModal() {
        photoModal.style.display = 'none';
        document.body.style.overflow = '';
        scale = 1; panX = 0; panY = 0;
        if (typeof applyZoom === 'function') applyZoom();
    }

    if (modalClose) modalClose.addEventListener('click', closePhotoModal);
    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closePhotoModal);
    if (photoModal) {
        photoModal.addEventListener('click', function(e) {
            if (e.target === photoModal) closePhotoModal();
        });
    }

    // ── Zoom & Drag ──
    const zoomImg = document.getElementById('modalPhotoPreview');
    const zoomContainer = document.getElementById('zoomContainer');
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const zoomResetBtn = document.getElementById('zoomReset');
    const zoomLevel = document.getElementById('zoomLevel');
    let scale = 1;
    let panX = 0, panY = 0;
    let isDragging = false, startX = 0, startY = 0;

    function applyZoom() {
        zoomImg.style.transform = 'translate(' + panX + 'px, ' + panY + 'px) scale(' + scale + ')';
        if (zoomLevel) zoomLevel.textContent = Math.round(scale * 100) + '%';
    }

    if (zoomInBtn) zoomInBtn.addEventListener('click', function() {
        scale = Math.min(scale + 0.25, 3);
        applyZoom();
    });
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', function() {
        scale = Math.max(scale - 0.25, 0.25);
        applyZoom();
    });
    if (zoomResetBtn) zoomResetBtn.addEventListener('click', function() {
        scale = 1; panX = 0; panY = 0;
        applyZoom();
    });

    if (zoomContainer) {
        zoomContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            scale = Math.min(Math.max(scale + delta, 0.25), 3);
            applyZoom();
        }, { passive: false });

        zoomContainer.addEventListener('mousedown', function(e) {
            if (e.target.tagName === 'BUTTON') return;
            isDragging = true;
            startX = e.clientX - panX;
            startY = e.clientY - panY;
            zoomContainer.style.cursor = 'grabbing';
        });
    }
    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        panX = e.clientX - startX;
        panY = e.clientY - startY;
        applyZoom();
    });
    document.addEventListener('mouseup', function() {
        isDragging = false;
        if (zoomContainer) zoomContainer.style.cursor = 'grab';
    });

    // Touch support: pinch zoom & one-finger drag
    let lastTouchDist = 0;
    let lastTouchX = 0, lastTouchY = 0;
    if (zoomContainer) {
        zoomContainer.addEventListener('touchstart', function(e) {
            if (e.target.tagName === 'BUTTON') return;
            if (e.touches.length === 2) {
                const dx = e.touches[0].clientX - e.touches[1].clientX;
                const dy = e.touches[0].clientY - e.touches[1].clientY;
                lastTouchDist = Math.sqrt(dx * dx + dy * dy);
            } else if (e.touches.length === 1) {
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
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (lastTouchDist > 0) {
                    const delta = (dist - lastTouchDist) / 100;
                    scale = Math.min(Math.max(scale + delta, 0.25), 3);
                    applyZoom();
                }
                lastTouchDist = dist;
            } else if (e.touches.length === 1 && isDragging) {
                panX = e.touches[0].clientX - startX;
                panY = e.touches[0].clientY - startY;
                applyZoom();
            }
        }, { passive: false });
        zoomContainer.addEventListener('touchend', function() {
            isDragging = false;
            lastTouchDist = 0;
        }, { passive: true });
    }
});
</script>
<?= $this->endSection() ?>
