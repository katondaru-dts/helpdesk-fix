<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div id="new-notif-banner" style="display:none; margin-bottom:15px; background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; padding:10px 15px; border-radius:10px; font-size:13px; font-weight:600; text-align:center; cursor:pointer" onclick="window.location.reload()">
    <i class="bi bi-arrow-clockwise"></i> Ada notifikasi baru. <span style="text-decoration:underline">Tampilkan sekarang</span>
</div>

<div class="page-header" style="margin-bottom:15px">
    <div>
        <div class="page-header-title">Notifikasi</div>
        <div class="page-header-sub">Riwayat aktivitas tiket untuk akun Anda</div>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="<?= base_url('notifications/mark-all-read') ?>" class="btn btn-outline" style="border-radius:8px;font-size:12px;padding:6px 12px;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;color:#4b5563;text-decoration:none">
            <i class="bi bi-check2-all"></i> Tandai Semua Dibaca
        </a>
    </div>
</div>

<!-- Tabs & Bulk Actions Container -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:15px;flex-wrap:wrap">
    <!-- Filter Tabs -->
    <div style="display:flex;background:#f3f4f6;padding:4px;border-radius:10px;gap:2px">
        <a href="<?= base_url('notifications?filter=all') ?>" style="padding:6px 16px;font-size:13px;font-weight:600;text-decoration:none;border-radius:8px;transition:all 0.2s;<?= $currentFilter === 'all' ? 'background:white;color:#3b82f6;box-shadow:0 1px 3px rgba(0,0,0,0.1)' : 'color:#6b7280' ?>">
            Semua
        </a>
        <a href="<?= base_url('notifications?filter=unread') ?>" style="padding:6px 16px;font-size:13px;font-weight:600;text-decoration:none;border-radius:8px;transition:all 0.2s;<?= $currentFilter === 'unread' ? 'background:white;color:#3b82f6;box-shadow:0 1px 3px rgba(0,0,0,0.1)' : 'color:#6b7280' ?>">
            Belum Dibaca
        </a>
        <a href="<?= base_url('notifications?filter=read') ?>" style="padding:6px 16px;font-size:13px;font-weight:600;text-decoration:none;border-radius:8px;transition:all 0.2s;<?= $currentFilter === 'read' ? 'background:white;color:#3b82f6;box-shadow:0 1px 3px rgba(0,0,0,0.1)' : 'color:#6b7280' ?>">
            Sudah Dibaca
        </a>
    </div>

    <!-- Bulk Action Controls -->
    <div id="bulk-actions" style="display:flex;align-items:center;gap:10px;visibility:hidden">
        <span id="selected-count" style="font-size:13px;color:#6b7280;font-weight:500">0 terpilih</span>
        <button type="button" onclick="bulkMarkRead()" class="btn btn-primary" style="background:#3b82f6;border-radius:8px;font-size:12px;padding:6px 12px;border:none;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-envelope-open"></i> Tandai Dibaca
        </button>
    </div>
</div>

<div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.07);overflow:hidden">
    <!-- Selection Header -->
    <div style="padding:12px 22px;background:#f9fafb;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:15px">
        <input type="checkbox" id="select-all" style="width:16px;height:16px;cursor:pointer">
        <span style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px">Daftar Notifikasi</span>
    </div>

    <?php if (empty($notifications)): ?>
        <div style="padding:60px;text-align:center;color:#9ca3af">
            <i class="bi bi-bell-slash" style="font-size:48px;display:block;margin-bottom:15px;color:#d1d5db"></i>
            <div style="font-size:16px;font-weight:600;color:#374151">Belum ada notifikasi</div>
            <div style="font-size:13px;margin-top:5px">Aktivitas tiket <?= $currentFilter !== 'all' ? 'dengan kriteria ini ' : '' ?>akan muncul di sini.</div>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
        <?php
            $iconMap = [
                'ASSIGNED'      => ['icon' => 'bi-person-check-fill', 'bg' => '#eff6ff', 'color' => '#3b82f6'],
                'NEW_TICKET'    => ['icon' => 'bi-ticket-detailed-fill', 'bg' => '#ecfdf5', 'color' => '#10b981'],
                'STATUS_CHANGE' => ['icon' => 'bi-arrow-repeat',        'bg' => '#fffbeb', 'color' => '#f59e0b'],
                'NEW_MESSAGE'   => ['icon' => 'bi-chat-dots-fill',       'bg' => '#f5f3ff', 'color' => '#8b5cf6'],
                'RESOLVED'      => ['icon' => 'bi-check-circle-fill',    'bg' => '#ecfdf5', 'color' => '#10b981'],
            ];
            $style = $iconMap[$n['type']] ?? ['icon' => 'bi-bell-fill', 'bg' => '#f3f4f6', 'color' => '#6b7280'];
            $isUnread = !$n['is_read'];
        ?>
        <div class="notif-item <?= $isUnread ? 'unread' : 'read' ?>" style="display:flex;align-items:flex-start;gap:15px;padding:18px 22px;border-bottom:1px solid #f3f4f6;transition:all 0.2s;background:<?= $isUnread ? '#fafbff' : 'white' ?>" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='<?= $isUnread ? '#fafbff' : 'white' ?>'">
            <div style="padding-top:2px">
                <input type="checkbox" class="notif-checkbox" value="<?= $n['id'] ?>" style="width:16px;height:16px;cursor:pointer">
            </div>
            <div style="width:40px;height:40px;border-radius:50%;background:<?= $style['bg'] ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi <?= $style['icon'] ?>" style="color:<?= $style['color'] ?>;font-size:17px"></i>
            </div>
            <div style="flex:1;min-width:0" onclick="window.location='<?= base_url('notifications/mark-read/' . $n['id']) ?>'; event.stopPropagation();" style="cursor:pointer">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;cursor:pointer">
                    <span style="font-size:13px;font-weight:600;color:#111827"><?= esc($n['title']) ?></span>
                    <?php if ($isUnread): ?>
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#3b82f6;flex-shrink:0"></span>
                    <?php endif; ?>
                </div>
                <div style="font-size:13px;color:#374151;margin-bottom:4px;cursor:pointer">
                    <?= esc($n['message']) ?>
                    <?php if (!empty($n['ref_id'])): ?>
                    — <a href="<?= base_url('tickets/detail/' . $n['ref_id']) ?>" style="color:#3b82f6;font-weight:600;text-decoration:none" onclick="event.stopPropagation()"><?= esc($n['ticket_title'] ?? $n['ref_id']) ?></a>
                    <?php endif; ?>
                </div>
                <div style="font-size:11px;color:#9ca3af">
                    <i class="bi bi-clock" style="margin-right:3px"></i>
                    <?php
                        $ts = strtotime($n['created_at']);
                        $diff = time() - $ts;
                        if ($diff < 60) echo 'Baru saja';
                        elseif ($diff < 3600) echo floor($diff/60) . ' menit lalu';
                        elseif ($diff < 86400) echo floor($diff/3600) . ' jam lalu';
                        else echo date('d M Y H:i', $ts);
                    ?>
                </div>
            </div>
            <?php if (!empty($n['ref_id'])): ?>
            <a href="<?= base_url('tickets/detail/' . $n['ref_id']) ?>" style="flex-shrink:0;color:#6b7280;text-decoration:none;padding:6px;border-radius:6px;transition:background 0.2s" title="Buka Tiket" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='transparent'">
                <i class="bi bi-arrow-right-circle" style="font-size:18px"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <!-- Pagination -->
        <div class="pagination-wrap" style="padding: 20px; border-top: 1px solid #f3f4f6; display: flex; justify-content: center;">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* Style for CodeIgniter's default pager to match the theme */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 5px;
}
.pagination li a {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 35px;
    height: 35px;
    padding: 0 10px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    color: #4b5563;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}
.pagination li.active a {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}
.pagination li a:hover:not(.active) {
    background: #f3f4f6;
    border-color: #d1d5db;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.notif-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');

    function updateBulkActions() {
        const checked = document.querySelectorAll('.notif-checkbox:checked').length;
        if (checked > 0) {
            bulkActions.style.visibility = 'visible';
            selectedCount.innerText = checked + ' terpilih';
        } else {
            bulkActions.style.visibility = 'hidden';
        }
        
        if (selectAll) {
            selectAll.checked = checked === checkboxes.length && checkboxes.length > 0;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkActions();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    window.bulkMarkRead = function() {
        const selectedIds = Array.from(document.querySelectorAll('.notif-checkbox:checked')).map(cb => cb.value);
        if (selectedIds.length === 0) return;

        if (!confirm('Tandai ' + selectedIds.length + ' notifikasi sebagai sudah dibaca?')) return;

        fetch('<?= base_url('notifications/bulk-mark-read') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: 'ids[]=' + selectedIds.join('&ids[]=')
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal memperbarui notifikasi.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    // Listen for new notifications from the polling system (main.php)
    window.addEventListener('new-notification', function(e) {
        const checked = document.querySelectorAll('.notif-checkbox:checked').length;
        // Only auto-reload if the user is not in the middle of selecting notifications
        if (checked === 0) {
            window.location.reload();
        } else {
            // Show a banner instead if they are busy
            const banner = document.getElementById('new-notif-banner');
            if (banner) banner.style.display = 'block';
        }
    });
});
</script>

<?= $this->endSection() ?>
