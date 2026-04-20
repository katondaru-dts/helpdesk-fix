<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Import Modern Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* Modern Premium Styling (Synced with Admin) */
.user-dash-wrapper {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: #374151;
}
.user-dash-wrapper * {
    font-family: inherit;
}
.user-dash-wrapper .dash-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(229, 231, 235, 0.8);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
/* Stat Card Colors - Vivid & Clean */
.card-total { background: #e0f2fe !important; border-color: #bae6fd !important; }
.card-open { background: #fee2e2 !important; border-color: #fecaca !important; }
.card-progress { background: #fef3c7 !important; border-color: #fde68a !important; }
.card-pending { background: #f3f4f6 !important; border-color: #e5e7eb !important; }
.card-resolved { background: #d1fae5 !important; border-color: #a7f3d0 !important; }

.user-dash-wrapper .dash-card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
}
.card-title-main {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 12px;
    letter-spacing: -0.2px;
}
.donut-wrap {
    position: relative;
    width: 68px;
    height: 68px;
    flex-shrink: 0;
}
.donut-wrap svg {
    transform: rotate(-90deg);
    width: 100%;
    height: 100%;
    filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.05));
}
.donut-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    line-height: 1.1;
}
.donut-link {
    display: block;
    text-decoration: none;
    color: inherit;
    padding: 6px;
    border-radius: 50%;
    transition: background 0.15s;
}
.donut-link:hover {
    background: rgba(0,0,0, 0.04);
}
.donut-num {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.5px;
}
.lgd {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 11.5px;
    color: #4B5563; /* Darker gray for better contrast on tinted bg */
    flex: 1;
}
.lgd-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.lgd-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
    flex-shrink: 0;
}
.lgd-left {
    display: flex;
    align-items: center;
    font-weight: 600; /* Bolder for better readability */
}
.ticket-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #F3F4F6;
    cursor: pointer;
    transition: background 0.15s, padding-left 0.15s;
    border-radius: 6px;
}
.ticket-row:hover {
    background: #F9FAFB;
    padding-left: 6px;
}
.ticket-row:last-child {
    border-bottom: none;
}
</style>

<div class="user-dash-wrapper">
    <!-- Header Section -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <div style="font-size:24px; font-weight:700; color:#111827; letter-spacing:-0.5px;">Halo, <?= htmlspecialchars(explode(' ', session()->get('name'))[0]) ?>!</div>
            <div style="font-size:14px; color:#6B7280; margin-top:4px;">Pantau dan kelola laporan gangguan Anda di sini.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('tickets/create') ?>" class="btn btn-primary" style="font-weight:500; border-radius:8px; padding:8px 16px;"><i class="bi bi-plus-circle"></i> Buat Tiket Baru</a>
        </div>
    </div>

    <!-- ROW 1: STAT CARDS -->
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px;">

        <!-- Total Tiket -->
        <div class="dash-card card-total">
            <div class="card-title-main">Total Tiket</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php
                    $total = $stats['total'] ?: 1;
                    $op = round($stats['open']/$total*100);
                    $ip = round($stats['inProgress']/$total*100);
                    $pd = round($stats['pending']/$total*100);
                    $rs = round($stats['resolved']/$total*100);
                    ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4.5"/>
                        <?php if($op>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F43F5E" stroke-width="4.5" stroke-dasharray="<?=$op?>,100" stroke-dashoffset="0"/><?php endif;?>
                        <?php if($ip>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EAB308" stroke-width="4.5" stroke-dasharray="<?=$ip?>,100" stroke-dashoffset="-<?=$op?>"/><?php endif;?>
                        <?php if($pd>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#9CA3AF" stroke-width="4.5" stroke-dasharray="<?=$pd?>,100" stroke-dashoffset="-<?=$op+$ip?>"/><?php endif;?>
                        <?php if($rs>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" stroke-width="4.5" stroke-dasharray="<?=$rs?>,100" stroke-dashoffset="-<?=$op+$ip+$pd?>"/><?php endif;?>
                    </svg>
                    <div class="donut-center">
                        <a href="<?= base_url('tickets') ?>" class="donut-link">
                            <div class="donut-num"><?= $stats['total'] ?></div>
                        </a>
                    </div>
                </div>
                <div class="lgd">
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#F43F5E;"></span>Open</div><div style="font-weight:700; color:#111827;"><?= $stats['open'] ?></div></div>
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#EAB308;"></span>In Progres</div><div style="font-weight:700; color:#111827;"><?= $stats['inProgress'] ?></div></div>
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#10B981;"></span>Selesai</div><div style="font-weight:700; color:#111827;"><?= $stats['resolved'] ?></div></div>
                </div>
            </div>
        </div>

        <!-- Open -->
        <div class="dash-card card-open">
            <div class="card-title-main">Open</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctOpen = $stats['total'] > 0 ? round($stats['open']/$stats['total']*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4.5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F43F5E" stroke-width="4.5" stroke-dasharray="<?= $pctOpen ?>,100"/>
                    </svg>
                    <div class="donut-center">
                        <a href="<?= base_url('tickets') ?>?f-status=OPEN" class="donut-link">
                            <div class="donut-num"><?= $stats['open'] ?></div>
                        </a>
                    </div>
                </div>
                <div class="lgd">
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#F43F5E;"></span>Open</div></div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="dash-card card-progress">
            <div class="card-title-main">In Progress</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctProg = $stats['total'] > 0 ? round($stats['inProgress']/$stats['total']*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4.5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EAB308" stroke-width="4.5" stroke-dasharray="<?= $pctProg ?>,100"/>
                    </svg>
                    <div class="donut-center">
                        <a href="<?= base_url('tickets') ?>?f-status=IN_PROGRESS" class="donut-link">
                            <div class="donut-num"><?= $stats['inProgress'] ?></div>
                        </a>
                    </div>
                </div>
                <div class="lgd">
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#EAB308;"></span>In Progres</div></div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="dash-card card-pending">
            <div class="card-title-main">Pending</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctPend = $stats['total'] > 0 ? round($stats['pending']/$stats['total']*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(0,0,0,0.08)" stroke-width="5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#9CA3AF" stroke-width="4" stroke-dasharray="<?= $pctPend ?>,100"/>
                    </svg>
                    <div class="donut-center">
                        <a href="<?= base_url('tickets') ?>?f-status=PENDING" class="donut-link">
                            <div class="donut-num"><?= $stats['pending'] ?></div>
                        </a>
                    </div>
                </div>
                <div class="lgd">
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#9CA3AF;"></span>Pending</div></div>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div class="dash-card card-resolved">
            <div class="card-title-main">Selesai</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctRes = $stats['total'] > 0 ? round($stats['resolved']/$stats['total']*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4.5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" stroke-width="4.5" stroke-dasharray="<?= $pctRes ?>,100"/>
                    </svg>
                    <div class="donut-center">
                        <a href="<?= base_url('tickets') ?>?f-status=RESOLVED" class="donut-link">
                            <div class="donut-num"><?= $stats['resolved'] ?></div>
                        </a>
                    </div>
                </div>
                <div class="lgd">
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#10B981;"></span>Selesai</div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: TABLES -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        
        <!-- Tiket Terbaru -->
        <div class="dash-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #F3F4F6;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="background:#E0F2FE; padding:6px; border-radius:8px; display:inline-flex;">
                        <i class="bi bi-clock-history" style="color:#0284C7; font-size:15px;"></i>
                    </div>
                    <span class="card-title-main" style="margin-bottom:0;">Tiket Terbaru</span>
                </div>
                <a href="<?= base_url('tickets') ?>" class="btn btn-outline-secondary btn-sm" style="font-size:12px; font-weight:600; border-radius:6px;">Lihat Semua</a>
            </div>
            
            <div style="display:flex; flex-direction:column;">
                <?php if (count($recentTickets ?? []) > 0): ?>
                    <?php foreach ($recentTickets as $t): ?>
                        <div class="ticket-row" onclick="window.location='<?= base_url('tickets/detail/'.$t['id']) ?>'">
                            <div style="min-width:0;flex:1;">
                                <div style="font-weight:600;font-size:13.5px;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= esc($t['title']) ?></div>
                                <div style="font-size:11.5px;color:#6B7280;margin-top:3px;">#<?= $t['id'] ?> &middot; <?= date('d M Y', strtotime($t['created_at'])) ?></div>
                            </div>
                            <?php 
                                $badgeColor = '#9CA3AF';
                                if($t['status'] == 'OPEN') $badgeColor = '#F43F5E';
                                elseif($t['status'] == 'IN_PROGRESS') $badgeColor = '#EAB308';
                                elseif($t['status'] == 'RESOLVED' || $t['status'] == 'CLOSED') $badgeColor = '#10B981';
                            ?>
                            <span class="badge" style="background:<?= $badgeColor ?>15; color:<?= $badgeColor ?>; border:1px solid <?= $badgeColor ?>30; font-size:10.5px; padding:4px 8px; border-radius:6px;"><?= $t['status'] ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding:24px; text-align:center; color:#9CA3AF; font-size:13.5px;">Belum ada tiket.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Respon IT Support -->
        <div class="dash-card">
            <div style="display:flex;align-items:center;gap:8px;padding-bottom:12px;margin-bottom:16px;border-bottom:1px solid #F3F4F6;">
                <div style="background:#F0FDF4; padding:6px; border-radius:8px; display:inline-flex;">
                    <i class="bi bi-chat-left-dots-fill" style="color:#16A34A; font-size:15px;"></i>
                </div>
                <span class="card-title-main" style="margin-bottom:0;">Respon IT Support</span>
            </div>
            
            <div style="display:flex; flex-direction:column; gap:8px;">
                <?php if (count($recentMessages ?? []) > 0): ?>
                    <?php foreach ($recentMessages as $msg): ?>
                        <div class="ticket-row" onclick="window.location='<?= base_url('tickets/detail/'.$msg['ticket_id']) ?>'" style="display:block; padding:10px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <span style="font-weight:700; font-size:13px; color:#16A34A;"><?= esc($msg['sender_name']) ?></span>
                                <span style="font-size:10.5px; color:#9CA3AF;"><?= date('d M, H:i', strtotime($msg['sent_at'])) ?></span>
                            </div>
                            <div style="font-size:11.5px; color:#6B7280; margin-bottom:4px; font-weight:500;">Tiket: <?= esc($msg['ticket_title']) ?></div>
                            <div style="font-size:12.5px; color:#374151; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= esc($msg['message']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding:24px; text-align:center; color:#9CA3AF; font-size:13.5px;">
                        <i class="bi bi-chat-square-text d-block mb-2" style="font-size:24px; opacity:0.3;"></i>
                        Belum ada pesan baru dari IT Support.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
