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
        </div>

        <!-- History/Messages -->
        <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
            <div style="font-weight:bold;margin-bottom:20px;font-size:16px"><i class="bi bi-clock-history" style="color:var(--primary)"></i> Riwayat & Balasan</div>
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

        <!-- Rating -->
        <?php if ($rating): ?>
            <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
                <div style="font-weight:bold;margin-bottom:15px;font-size:16px"><i class="bi bi-star-fill" style="color:#f59e0b"></i> Rating Kepuasan</div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                    <span style="color:#f59e0b"><?= str_repeat('★', $rating['rating']) . str_repeat('☆', 5 - $rating['rating']) ?></span>
                    <b><?= $rating['rating'] ?>/5</b>
                </div>
                <?php if ($rating['feedback']): ?><p style="font-style:italic;color:#6b7280">"<?= esc($rating['feedback']) ?>"</p><?php endif; ?>
            </div>
        <?php elseif ($ticket['reporter_id'] == $userId && in_array($ticket['status'], ['RESOLVED', 'CLOSED'])): ?>
            <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
                <div style="font-weight:bold;margin-bottom:15px;font-size:16px"><i class="bi bi-star" style="color:#f59e0b"></i> Beri Rating</div>
                <form action="<?= base_url('tickets/rate/' . $ticket['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <label style="display:block;margin-bottom:5px;font-size:14px">Rating Pelayanan</label>
                    <select name="rating" class="form-select mb-3" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:15px">
                        <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                        <option value="4">⭐⭐⭐⭐ (Puas)</option>
                        <option value="3">⭐⭐⭐ (Cukup)</option>
                        <option value="2">⭐⭐ (Kurang)</option>
                        <option value="1">⭐ (Sangat Kurang)</option>
                    </select>
                    <textarea name="feedback" class="form-control mb-3" rows="2" placeholder="Komentar tambahan..." style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:15px"></textarea>
                    <button type="submit" class="btn btn-primary" style="background:#3b82f6;color:white;padding:10px 20px;border:none;border-radius:8px;font-weight:bold;cursor:pointer">Kirim Rating</button>
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
                        <label style="display:block;margin-bottom:5px;font-size:12px;font-weight:bold">Update Status</label>
                        <select name="new_status" class="form-select mb-2" style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:10px">
                            <?php 
                            $statuses = ['OPEN', 'IN_PROGRESS', 'PENDING', 'RESOLVED', 'CLOSED'];
                            foreach($statuses as $st): 
                                // Jika role teknisi (2), jangan tampilkan CLOSED
                                if (session()->get('role_id') == 2 && $st === 'CLOSED') continue;
                            ?>
                                <option value="<?= $st ?>" <?= $ticket['status'] == $st ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="notes" class="form-control mb-2" placeholder="Catatan..." style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:10px">
                        <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;background:#3b82f6;color:white;border:none;border-radius:8px;font-weight:bold;cursor:pointer">Update Status</button>
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
});
</script>
<?= $this->endSection() ?>