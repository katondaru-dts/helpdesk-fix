<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <div class="page-header-title">Notifikasi</div>
        <div class="page-header-sub">Riwayat aktivitas tiket untuk akun Anda</div>
    </div>
    <?php $roleId = session()->get('role_id'); ?>
    <?php if ($roleId == 1 || $roleId == 2 || $roleId == 4): ?>
    <div>
        <a href="<?= base_url('notifications/all') ?>" class="btn btn-outline-primary btn-sm" style="border-radius:8px;font-size:13px;padding:8px 16px;display:inline-flex;align-items:center;gap:6px;border:1.5px solid #3b82f6;color:#3b82f6;text-decoration:none;transition:all 0.2s" onmouseover="this.style.background='#3b82f6';this.style.color='white'" onmouseout="this.style.background='transparent';this.style.color='#3b82f6'">
            <i class="bi bi-list-ul"></i> Semua Notifikasi
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.07);overflow:hidden">
    <?php if (empty($notifications)): ?>
        <div style="padding:60px;text-align:center;color:#9ca3af">
            <i class="bi bi-bell-slash" style="font-size:48px;display:block;margin-bottom:15px;color:#d1d5db"></i>
            <div style="font-size:16px;font-weight:600;color:#374151">Belum ada notifikasi</div>
            <div style="font-size:13px;margin-top:5px">Aktivitas tiket Anda akan muncul di sini.</div>
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
        <div style="display:flex;align-items:flex-start;gap:15px;padding:18px 22px;border-bottom:1px solid #f3f4f6;transition:background 0.2s;background:<?= $isUnread ? '#fafbff' : 'white' ?>" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='<?= $isUnread ? '#fafbff' : 'white' ?>'">
            <div style="width:40px;height:40px;border-radius:50%;background:<?= $style['bg'] ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi <?= $style['icon'] ?>" style="color:<?= $style['color'] ?>;font-size:17px"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                    <span style="font-size:13px;font-weight:600;color:#111827"><?= esc($n['title']) ?></span>
                    <?php if ($isUnread): ?>
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#3b82f6;flex-shrink:0"></span>
                    <?php endif; ?>
                </div>
                <div style="font-size:13px;color:#374151;margin-bottom:4px">
                    <?= esc($n['message']) ?>
                    <?php if (!empty($n['ref_id'])): ?>
                    — <a href="<?= base_url('tickets/detail/' . $n['ref_id']) ?>" style="color:#3b82f6;font-weight:600;text-decoration:none"><?= esc($n['ticket_title'] ?? $n['ref_id']) ?></a>
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
            <a href="<?= base_url('tickets/detail/' . $n['ref_id']) ?>" style="flex-shrink:0;color:#6b7280;text-decoration:none;padding:6px" title="Buka Tiket">
                <i class="bi bi-arrow-right-circle" style="font-size:18px"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
