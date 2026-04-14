<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <div class="page-header-title">Semua Notifikasi</div>
        <div class="page-header-sub">Riwayat notifikasi seluruh pengguna sistem</div>
    </div>
    <a href="<?= base_url('notifications') ?>" style="border-radius:8px;font-size:13px;padding:8px 16px;display:inline-flex;align-items:center;gap:6px;border:1.5px solid #6b7280;color:#6b7280;text-decoration:none;transition:all 0.2s" onmouseover="this.style.background='#6b7280';this.style.color='white'" onmouseout="this.style.background='transparent';this.style.color='#6b7280'">
        <i class="bi bi-person-fill"></i> Notifikasi Saya
    </a>
</div>

<!-- Stats Bar -->
<?php
$totalCount  = count($notifications);
$unreadCount = count(array_filter($notifications, fn($n) => !$n['is_read']));
$assignedCount = count(array_filter($notifications, fn($n) => $n['type'] === 'ASSIGNED'));
$newTicketCount = count(array_filter($notifications, fn($n) => $n['type'] === 'NEW_TICKET'));
?>
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px">
    <?php foreach ([
        ['label'=>'Total Notifikasi','val'=>$totalCount,    'icon'=>'bi-bell',                'bg'=>'#eff6ff','color'=>'#3b82f6'],
        ['label'=>'Belum Dibaca',    'val'=>$unreadCount,   'icon'=>'bi-bell-fill',            'bg'=>'#fef3c7','color'=>'#d97706'],
        ['label'=>'Penugasan',       'val'=>$assignedCount, 'icon'=>'bi-person-check-fill',    'bg'=>'#ecfdf5','color'=>'#10b981'],
        ['label'=>'Tiket Baru',      'val'=>$newTicketCount,'icon'=>'bi-ticket-detailed-fill', 'bg'=>'#f5f3ff','color'=>'#8b5cf6'],
    ] as $s): ?>
    <div style="background:white;border-radius:12px;padding:18px;box-shadow:0 2px 6px rgba(0,0,0,0.06);display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;border-radius:10px;background:<?= $s['bg'] ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi <?= $s['icon'] ?>" style="color:<?= $s['color'] ?>;font-size:20px"></i>
        </div>
        <div>
            <div style="font-size:22px;font-weight:700;color:#111827"><?= $s['val'] ?></div>
            <div style="font-size:12px;color:#6b7280"><?= $s['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.07);overflow:hidden">
    <!-- Table Header -->
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
        <div style="font-size:14px;font-weight:600;color:#374151">
            <i class="bi bi-list-ul" style="margin-right:6px"></i>Daftar Notifikasi
        </div>
        
        <!-- Filter Dropdown -->
        <div style="display:flex;align-items:center;gap:8px">
            <label for="notif-filter" style="font-size:13px;color:#6b7280;font-weight:500;">Filter:</label>
            <select id="notif-filter" onchange="filterNotifications()" style="padding:6px 12px;font-size:13px;border-radius:6px;border:1px solid #d1d5db;background:white;color:#374151;outline:none;cursor:pointer">
                <option value="all">Semua (All)</option>
                <option value="read">Sudah Dibaca (Read)</option>
                <option value="unread">Belum Dibaca (Unread)</option>
            </select>
        </div>
    </div>

    <?php if (empty($notifications)): ?>
        <div style="padding:60px;text-align:center;color:#9ca3af">
            <i class="bi bi-bell-slash" style="font-size:48px;display:block;margin-bottom:15px;color:#d1d5db"></i>
            <div style="font-size:16px;font-weight:600;color:#374151">Belum ada notifikasi</div>
        </div>
    <?php else: ?>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
            <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb">
                <th style="padding:11px 16px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap">Penerima</th>
                <th style="padding:11px 16px;text-align:left;font-weight:600;color:#6b7280">Notifikasi</th>
                <th style="padding:11px 16px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap">Tipe</th>
                <th style="padding:11px 16px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap">Tiket</th>
                <th style="padding:11px 16px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap">Waktu</th>
                <th style="padding:11px 16px;text-align:center;font-weight:600;color:#6b7280;white-space:nowrap">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($notifications as $n): ?>
        <?php
            $typeStyles = [
                'ASSIGNED'      => ['label'=>'Penugasan',   'bg'=>'#eff6ff','color'=>'#1d4ed8'],
                'NEW_TICKET'    => ['label'=>'Tiket Baru',  'bg'=>'#ecfdf5','color'=>'#065f46'],
                'STATUS_CHANGE' => ['label'=>'Status',      'bg'=>'#fffbeb','color'=>'#92400e'],
                'NEW_MESSAGE'   => ['label'=>'Pesan Baru',  'bg'=>'#f5f3ff','color'=>'#5b21b6'],
                'RESOLVED'      => ['label'=>'Selesai',     'bg'=>'#ecfdf5','color'=>'#065f46'],
            ];
            $ts = $typeStyles[$n['type']] ?? ['label'=>$n['type'],'bg'=>'#f3f4f6','color'=>'#374151'];
        ?>
        <tr class="notif-row" data-status="<?= $n['is_read'] ? 'read' : 'unread' ?>" style="border-bottom:1px solid #f3f4f6;transition:background 0.15s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
            <td style="padding:13px 16px;vertical-align:top">
                <div style="font-weight:600;color:#111827"><?= esc($n['target_user_name'] ?? '-') ?></div>
                <div style="font-size:11px;color:#9ca3af"><?= esc($n['target_user_email'] ?? '') ?></div>
                <?php if (!empty($n['target_role_name'])): ?>
                <div style="font-size:11px;color:#6b7280;margin-top:2px"><i class="bi bi-shield"></i> <?= esc($n['target_role_name']) ?></div>
                <?php endif; ?>
            </td>
            <td style="padding:13px 16px;vertical-align:top;max-width:320px">
                <div style="font-weight:600;color:#111827;margin-bottom:3px"><?= esc($n['title']) ?></div>
                <div style="color:#6b7280;font-size:12px;line-height:1.5"><?= esc($n['message']) ?></div>
            </td>
            <td style="padding:13px 16px;vertical-align:top">
                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:<?= $ts['bg'] ?>;color:<?= $ts['color'] ?>"><?= $ts['label'] ?></span>
            </td>
            <td style="padding:13px 16px;vertical-align:top">
                <?php if (!empty($n['ref_id'])): ?>
                <a href="<?= base_url('tickets/detail/' . $n['ref_id']) ?>" style="color:#3b82f6;text-decoration:none;font-weight:600;font-size:12px">
                    <i class="bi bi-link-45deg"></i> <?= esc($n['ticket_title'] ?? $n['ref_id']) ?>
                </a>
                <?php else: ?>
                <span style="color:#d1d5db">—</span>
                <?php endif; ?>
            </td>
            <td style="padding:13px 16px;vertical-align:top;white-space:nowrap">
                <div style="font-size:12px;color:#374151"><?= date('d M Y', strtotime($n['created_at'])) ?></div>
                <div style="font-size:11px;color:#9ca3af"><?= date('H:i', strtotime($n['created_at'])) ?></div>
            </td>
            <td style="padding:13px 16px;vertical-align:top;text-align:center">
                <?php if ($n['is_read']): ?>
                <span style="color:#10b981;font-size:13px" title="Sudah dibaca"><i class="bi bi-check-circle-fill"></i></span>
                <?php else: ?>
                <span style="color:#f59e0b;font-size:13px" title="Belum dibaca"><i class="bi bi-circle-fill"></i></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<script>
function filterNotifications() {
    const filterValue = document.getElementById('notif-filter').value;
    const rows = document.querySelectorAll('.notif-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        if (filterValue === 'all' || filterValue === status) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Handle empty state manually if all are hidden
    let emptyState = document.getElementById('filter-empty-state');
    const tableBody = document.querySelector('table tbody');
    
    if (visibleCount === 0 && tableBody) {
        if (!emptyState) {
            emptyState = document.createElement('tr');
            emptyState.id = 'filter-empty-state';
            emptyState.innerHTML = '<td colspan="6" style="padding:40px;text-align:center;color:#9ca3af"><div style="font-size:14px;font-weight:500;">Tidak ada notifikasi yang sesuai filter.</div></td>';
            tableBody.appendChild(emptyState);
        } else {
            emptyState.style.display = '';
        }
    } else if (emptyState) {
        emptyState.style.display = 'none';
    }
}
</script>

<?= $this->endSection() ?>

