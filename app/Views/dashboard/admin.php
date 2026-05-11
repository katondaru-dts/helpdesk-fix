<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Import Modern Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Modern Premium Styling */
.admin-dash-wrapper {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: #374151;
}
.admin-dash-wrapper * {
    font-family: inherit;
}
.admin-dash-wrapper .dash-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(229, 231, 235, 0.8);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.admin-dash-wrapper .dash-card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
}
/* Vivid Stat Card Colors */
.card-total { background: #e0f2fe !important; border-color: #bae6fd !important; }
.card-open { background: #fee2e2 !important; border-color: #fecaca !important; }
.card-progress { background: #fef3c7 !important; border-color: #fde68a !important; }
.card-pending { background: #f3f4f6 !important; border-color: #e5e7eb !important; }
.card-resolved { background: #d1fae5 !important; border-color: #a7f3d0 !important; }
.card-users { background: #f5f3ff !important; border-color: #ddd6fe !important; }
.card-unassigned { background: #fff7ed !important; border-color: #ffedd5 !important; }
.card-urgent { background: #fef2f2 !important; border-color: #fee2e2 !important; }
.card-reports { background: #ecfdf5 !important; border-color: #d1fae5 !important; }
.card-audit { background: #f8fafc !important; border-color: #f1f5f9 !important; }

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
.cat-donut-num {
    font-size: 16px;
    font-weight: 700;
    color: #111827;
}
.lgd {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 11.5px;
    color: #4B5563;
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
    font-weight: 600;
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
/* Modern Scrollbar Styling */
.scroll-area {
    overflow-y: auto;
    padding-right: 8px;
}
.scroll-area::-webkit-scrollbar {
    width: 8px;
}
.scroll-area::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}
.scroll-area::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 10px;
}
.scroll-area::-webkit-scrollbar-thumb:hover {
    background: #1e293b;
}
.refresh-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    transition: all 0.2s ease;
    cursor: pointer;
    color: #6B7280;
    font-size: 16px;
    background: #F3F4F6;
}
.refresh-btn:hover {
    background: #E5E7EB;
    color: #374151;
}
@keyframes spin-once {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.refresh-btn.spinning {
    animation: spin-once 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
}

/* Category Grid Row */
.cat-item-wrap {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    width: calc(33.333% - 11px);
    margin-bottom: 12px;
    background: rgba(255,255,255,0.6);
    padding: 12px;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
}

/* Responsive Dashboard */
@media (max-width: 1024px) {
    .dash-stats-row { grid-template-columns: repeat(3, 1fr) !important; }
    .cat-item-wrap { width: calc(50% - 8px); }
}
@media (max-width: 768px) {
    .dash-header-row { flex-direction: column; align-items: flex-start !important; gap: 12px; }
    .dash-header-row .d-flex { flex-wrap: wrap; width: 100%; }
    .dash-header-row .d-flex .btn { flex: 1; justify-content: center; }
    .dash-stats-row { grid-template-columns: repeat(2, 1fr) !important; }
    .cat-item-wrap { width: calc(50% - 8px); }
}
@media (max-width: 480px) {
    .dash-stats-row { grid-template-columns: 1fr !important; }
    .cat-item-wrap { width: 100%; }
    .dash-header-row .d-flex .btn { font-size: 12px; padding: 7px 10px; }
}
</style>

<div class="admin-dash-wrapper">

    <!-- Header Section -->
    <div class="dash-header-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="font-size:24px; font-weight:700; color:#111827; letter-spacing:-0.5px;">Dashboard Status & Kinerja Layanan</div>
            <i class="bi bi-arrow-repeat refresh-btn" title="Refresh Dasbor"></i>
        </div>
        <div class="d-flex gap-2">
            <?php if (has_permission('Buat Tiket') || session()->get('role_id') == 4): ?>
                <a href="<?= base_url('tickets/create') ?>" class="btn btn-success" style="font-weight:500; border-radius:8px; padding:8px 16px;"><i class="bi bi-plus-circle"></i> Buat Tiket</a>
            <?php endif; ?>
            <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline-secondary" style="font-weight:500; border-radius:8px; padding:8px 16px;"><i class="bi bi-bar-chart"></i> Laporan</a>
            <a href="<?= base_url('tickets') ?>" class="btn btn-primary" style="font-weight:500; border-radius:8px; padding:8px 16px;"><i class="bi bi-list-ul"></i> Semua Tiket</a>
        </div>
    </div>

    <!-- ROW 1: MAIN STATS -->
    <div class="dash-stats-row" style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:16px;">
        
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
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4"/>
                        <?php if($op>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F43F5E" stroke-width="4" stroke-dasharray="<?=$op?>,100" stroke-dashoffset="0"/><?php endif;?>
                        <?php if($ip>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EAB308" stroke-width="4" stroke-dasharray="<?=$ip?>,100" stroke-dashoffset="-<?=$op?>"/><?php endif;?>
                        <?php if($pd>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#9CA3AF" stroke-width="4" stroke-dasharray="<?=$pd?>,100" stroke-dashoffset="-<?=$op+$ip?>"/><?php endif;?>
                        <?php if($rs>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" stroke-width="4" stroke-dasharray="<?=$rs?>,100" stroke-dashoffset="-<?=$op+$ip+$pd?>"/><?php endif;?>
                    </svg>
                    <div class="donut-center">
                        <a href="<?= base_url('tickets') ?>" class="donut-link"><div class="donut-num"><?= $stats['total'] ?></div></a>
                    </div>
                </div>
                <div class="lgd">
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#F43F5E;"></span>Open</div><div style="font-weight:700; color:#111827;"><?= $stats['open'] ?></div></div>
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#EAB308;"></span>Progres</div><div style="font-weight:700; color:#111827;"><?= $stats['inProgress'] ?></div></div>
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#10B981;"></span>Selesai</div><div style="font-weight:700; color:#111827;"><?= $stats['resolved'] ?></div></div>
                </div>
            </div>
        </div>

        <div class="dash-card card-open">
            <div class="card-title-main">Open</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctOpen = $total > 0 ? round($stats['open']/$total*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F43F5E" stroke-width="4" stroke-dasharray="<?= $pctOpen ?>,100"/>
                    </svg>
                    <div class="donut-center"><a href="<?= base_url('tickets') ?>?f-status=OPEN" class="donut-link"><div class="donut-num"><?= $stats['open'] ?></div></a></div>
                </div>
                <div class="lgd"><div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#F43F5E;"></span>Open</div></div></div>
            </div>
        </div>

        <div class="dash-card card-progress">
            <div class="card-title-main">In Progress</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctProg = $total > 0 ? round($stats['inProgress']/$total*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EAB308" stroke-width="4" stroke-dasharray="<?= $pctProg ?>,100"/>
                    </svg>
                    <div class="donut-center"><a href="<?= base_url('tickets') ?>?f-status=IN_PROGRESS" class="donut-link"><div class="donut-num"><?= $stats['inProgress'] ?></div></a></div>
                </div>
                <div class="lgd"><div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#EAB308;"></span>Progres</div></div></div>
            </div>
        </div>

        <div class="dash-card card-pending">
            <div class="card-title-main">Pending</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctPend = $total > 0 ? round($stats['pending']/$total*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#9CA3AF" stroke-width="4" stroke-dasharray="<?= $pctPend ?>,100"/>
                    </svg>
                    <div class="donut-center"><a href="<?= base_url('tickets') ?>?f-status=PENDING" class="donut-link"><div class="donut-num"><?= $stats['pending'] ?></div></a></div>
                </div>
                <div class="lgd"><div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#9CA3AF;"></span>Pending</div></div></div>
            </div>
        </div>

        <div class="dash-card card-resolved">
            <div class="card-title-main">Selesai</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctRes = $total > 0 ? round($stats['resolved']/$total*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" stroke-width="4" stroke-dasharray="<?= $pctRes ?>,100"/>
                    </svg>
                    <div class="donut-center"><a href="<?= base_url('tickets') ?>?f-status=RESOLVED" class="donut-link"><div class="donut-num"><?= $stats['resolved'] ?></div></a></div>
                </div>
                <div class="lgd"><div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#10B981;"></span>Selesai</div></div></div>
            </div>
        </div>
    </div>

    <!-- ROW 2: OTHER STATS -->
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:16px;">
        <?php if (has_permission('Kelola User')): ?>
        <div class="dash-card card-users" onclick="window.location='<?= base_url('admin/users') ?>'" style="cursor:pointer;">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">User Aktif</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#8B5CF6; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-people-fill"></i></div>
                <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats['users'] ?></div>
            </div>
        </div>
        <?php endif; ?>
        <div class="dash-card card-unassigned">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Belum Diassign</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#F59E0B; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-person-plus-fill"></i></div>
                <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats['unassigned'] ?></div>
            </div>
        </div>
        <div class="dash-card card-urgent">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Urgent / High</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#EF4444; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-fire"></i></div>
                <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats['urgent'] ?></div>
            </div>
        </div>
        <div class="dash-card card-reports" onclick="window.location='<?= base_url('admin/reports') ?>'" style="cursor:pointer;">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Laporan</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#10B981; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                <div style="font-size:13px; font-weight:600; color:#111827;">Lihat Detail</div>
            </div>
        </div>
        <div class="dash-card card-open" style="background:#FFF1F2 !important; border-color:#FECACA !important; cursor:pointer;" onclick="window.location='<?= base_url('tickets') ?>?f-overdue=1'">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;color:#E11D48;">Melewati SLA</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#E11D48; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-clock-history"></i></div>
                <div style="font-size:22px; font-weight:700; color:#E11D48;"><?= $stats['overdue'] ?></div>
            </div>
        </div>
    </div>


    <!-- ROW 3: CATEGORIES | URGENT & RECENT MESSAGES -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;align-items:start;">
        
        <div class="dash-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <div class="card-title-main" style="margin-bottom:0; font-size:16px;">Laporan Gangguan &amp; Tiket Baru</div>
                <i class="bi bi-arrow-repeat refresh-btn"></i>
            </div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:20px; font-weight:500;">Angka menunjukkan jumlah tiket terakumulasi</div>
            <div class="scroll-area" style="max-height: 370px;">
                <div style="display:flex;flex-wrap:wrap;gap:16px;padding-right:10px;">
                    <?php if (!empty($categoryStats)): ?>
                        <?php foreach ($categoryStats as $c):
                            $catTotal  = (int)($c['total'] ?? 0);
                            $catOpen   = (int)($c['open_count'] ?? 0);
                            $catProg   = (int)($c['inprogress_count'] ?? 0);
                            $catPend   = (int)($c['pending_count'] ?? 0);
                            $catRes    = (int)($c['resolved_count'] ?? 0);
                            $base2     = $catTotal ?: 1;
                            $op2 = round($catOpen/$base2*100);
                            $ip2 = round($catProg/$base2*100);
                            $pd2 = round($catPend/$base2*100);
                            $rs2 = round($catRes/$base2*100);
                        ?>
                        <div class="cat-item-wrap">
                            <div class="donut-wrap" style="width:64px;height:64px;">
                                <svg viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="5.2"/>
                                    <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#fff" stroke-width="4.8"/>
                                    <?php if($op2>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F43F5E" stroke-width="4.8" stroke-dasharray="<?=$op2?>,100" stroke-dashoffset="0"/><?php endif;?>
                                    <?php if($ip2>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EAB308" stroke-width="4.8" stroke-dasharray="<?=$ip2?>,100" stroke-dashoffset="-<?=$op2?>"/><?php endif;?>
                                    <?php if($pd2>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#9CA3AF" stroke-width="4.8" stroke-dasharray="<?=$pd2?>,100" stroke-dashoffset="-<?=$op2+$ip2?>"/><?php endif;?>
                                    <?php if($rs2>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" stroke-width="4.8" stroke-dasharray="<?=$rs2?>,100" stroke-dashoffset="-<?=$op2+$ip2+$pd2?>"/><?php endif;?>
                                    <?php if($catTotal==0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#eee" stroke-width="4.8" stroke-dasharray="100,0"/><?php endif;?>
                                </svg>
                                <div class="donut-center"><div class="cat-donut-num"><?= $catTotal ?></div></div>
                            </div>
                            <!-- Legend per status -->
                            <div class="lgd">
                                <div style="font-weight:700;font-size:12.5px;margin-bottom:6px;color:#111827;line-height:1.2;"><?= esc($c['cat_name']) ?></div>
                                <div class="lgd-row">
                                    <div class="lgd-left"><span class="lgd-dot" style="background:#F43F5E;"></span>Open</div>
                                    <div style="font-weight:700; color:#111827;"><?= $catOpen ?></div>
                                </div>
                                <div class="lgd-row">
                                    <div class="lgd-left"><span class="lgd-dot" style="background:#EAB308;"></span>In Progres</div>
                                    <div style="font-weight:700; color:#111827;"><?= $catProg ?></div>
                                </div>
                                <div class="lgd-row">
                                    <div class="lgd-left"><span class="lgd-dot" style="background:#9CA3AF;"></span>Pending</div>
                                    <div style="font-weight:700; color:#111827;"><?= $catPend ?></div>
                                </div>
                                <div class="lgd-row">
                                    <div class="lgd-left"><span class="lgd-dot" style="background:#10B981;"></span>Selesai</div>
                                    <div style="font-weight:700; color:#111827;"><?= $catRes ?></div>
                                </div>
                            </div>
                            </div>                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <!-- Urgent Tickets -->
            <div class="dash-card">
                <div style="display:flex;align-items:center;gap:8px;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid #F3F4F6;">
                    <div style="background:#FEE2E2; padding:6px; border-radius:8px; display:inline-flex;"><i class="bi bi-fire" style="color:#DC2626;"></i></div>
                    <span class="card-title-main" style="margin-bottom:0; flex:1;">Tiket Urgent / High</span>
                    <a href="<?= base_url('tickets') ?>" class="btn btn-outline-secondary btn-sm" style="font-size:10px; padding:2px 8px;">Lihat</a>
                </div>
                <div class="scroll-area" style="max-height: 80px;">
                    <?php if(!empty($urgentTickets)): foreach($urgentTickets as $t): ?>
                        <div class="ticket-row" onclick="window.location='<?= base_url('tickets/detail/'.$t['id']) ?>'">
                            <div style="min-width:0;flex:1;">
                                <div style="font-weight:600;font-size:13px;"><?= esc($t['title']) ?></div>
                                <div style="font-size:11px;color:#6B7280;">#<?= $t['id'] ?> &middot; <?= esc($t['reporter_name'] ?? '') ?></div>
                            </div>
                            <span class="badge" style="background:#EF444415; color:#EF4444; border:1px solid #EF444430; font-size:9px;"><?= $t['priority'] ?></span>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="padding:10px;text-align:center;color:#9CA3AF;font-size:12px;">Tidak ada tiket mendesak.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tiket Masuk Terbaru -->
            <div class="dash-card">
                <div style="display:flex;align-items:center;gap:8px;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid #F3F4F6;">
                    <div style="background:#F0FDF4; padding:6px; border-radius:8px; display:inline-flex;"><i class="bi bi-box-arrow-in-right" style="color:#16A34A;"></i></div>
                    <span class="card-title-main" style="margin-bottom:0; flex:1;">Tiket Masuk Terbaru (<?= $stats['open'] ?>)</span>
                </div>
                <div class="scroll-area" style="max-height: 80px;">
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <?php if (!empty($newIncomingTickets)): foreach ($newIncomingTickets as $t): ?>
                            <div class="ticket-row" onclick="window.location='<?= base_url('tickets/detail/'.$t['id']) ?>'" style="display:block; padding:10px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                    <span style="font-weight:700; font-size:13px; color:#1e293b;">#<?= $t['id'] ?></span>
                                    <span style="font-size:10px; color:#9CA3AF;"><?= date('d M, H:i', strtotime($t['created_at'])) ?></span>
                                </div>
                                <div style="font-weight:600; font-size:12.5px; color:#334155; margin-bottom:4px;"><?= esc($t['title']) ?></div>
                                <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                    <span style="font-size:10.5px; color:#64748b;"><i class="bi bi-person"></i> <?= esc($t['reporter_name']) ?></span>
                                    <span style="font-size:10.5px; color:#64748b;">&bull;</span>
                                    <span style="font-size:10.5px; color:#64748b;"><i class="bi bi-tag"></i> <?= esc($t['cat_name']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div style="padding:10px; text-align:center; color:#9CA3AF; font-size:12px;">Belum ada tiket masuk.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Unassigned Tickets -->
            <div class="dash-card">
                <div style="display:flex;align-items:center;gap:8px;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid #F3F4F6;">
                    <div style="background:#FEF3C7; padding:6px; border-radius:8px; display:inline-flex;"><i class="bi bi-person-x-fill" style="color:#D97706;"></i></div>
                    <span class="card-title-main" style="margin-bottom:0; flex:1;">Belum Diassign (<?= $stats['unassigned'] ?>)</span>
                </div>
                <div class="scroll-area" style="max-height: 80px;">
                    <?php if(!empty($pendingTickets)): foreach($pendingTickets as $t): ?>
                        <div class="ticket-row" onclick="window.location='<?= base_url('tickets/detail/'.$t['id']) ?>'">
                            <div style="min-width:0;flex:1;">
                                <div style="font-weight:600;font-size:13px;"><?= esc($t['title']) ?></div>
                                <div style="font-size:11px;color:#6B7280;">#<?= $t['id'] ?> &middot; <?= esc($t['cat_name'] ?? '') ?></div>
                            </div>
                            <button class="btn btn-sm btn-primary" style="font-size:10px; padding:2px 8px;">Assign</button>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="padding:10px;text-align:center;color:#9CA3AF;font-size:12px;">Semua tiket sudah diassign.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 4: CHARTS -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div class="dash-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <div>
                    <div class="card-title-main" style="margin-bottom:2px; font-size:16px;">Statistik Kinerja Tim Support</div>
                    <div style="font-size:12px; color:#9CA3AF;">Jumlah tiket selesai per teknisi</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <?php $currentFilter = request()->getGet('filter') ?: 'minggu_ini'; ?>
                    <select id="perf-filter" class="form-select form-select-sm" style="width:140px; font-size:12px; font-weight:600; border-radius:8px; border-color:#E5E7EB; cursor:pointer;">
                        <option value="hari_ini" <?= $currentFilter == 'hari_ini' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="kemarin" <?= $currentFilter == 'kemarin' ? 'selected' : '' ?>>Kemarin</option>
                        <option value="minggu_ini" <?= $currentFilter == 'minggu_ini' ? 'selected' : '' ?>>Minggu Ini</option>
                        <option value="bulan_ini" <?= $currentFilter == 'bulan_ini' ? 'selected' : '' ?>>Bulan Ini</option>
                    </select>
                    <i class="bi bi-arrow-repeat refresh-btn" style="background:#F3F4F6; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center;"></i>
                </div>
            </div>
            <div style="height:280px; position:relative;"><canvas id="lineChart"></canvas></div>
            <div style="margin-top:16px; padding-top:12px; border-top:1px solid #F3F4F6; font-size:11px; color:#6B7280; line-height:1.5;">
                <i class="bi bi-info-circle-fill" style="color:#3B82F6; margin-right:4px;"></i> 
                Angka menunjukkan <strong>jumlah tiket selesai</strong> oleh teknisi pada periode yang dipilih.
            </div>
        </div>

        <div class="dash-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <div>
                    <div class="card-title-main" style="margin-bottom:2px; font-size:16px;">Waktu Respons Tiket</div>
                    <div style="font-size:12px; color:#9CA3AF;">Rata-rata jam penanganan per kategori</div>
                </div>
                <i class="bi bi-info-circle-fill" style="color:#9CA3AF; cursor:help;" title="Dihitung dari tiket dibuat sampai status Selesai"></i>
            </div>
            <div style="height:280px; position:relative;"><canvas id="barChart"></canvas></div>
            <div style="margin-top:16px; padding-top:12px; border-top:1px solid #F3F4F6; font-size:11px; color:#6B7280; line-height:1.5;">
                <i class="bi bi-info-circle-fill" style="color:#3B82F6; margin-right:4px;"></i> 
                Angka menunjukkan <strong>rata-rata durasi (Jam)</strong> dari tiket dibuat hingga selesai.
            </div>
        </div>
    </div>
</div>

<script>
// Logic Refresh Universal
function handleRefresh(e) {
    const btn = e.currentTarget;
    btn.classList.add('spinning');
    setTimeout(() => {
        window.location.reload();
    }, 450);
}

document.addEventListener("DOMContentLoaded", function() {
    // Cari semua tombol refresh dan pasang event listener
    const refreshButtons = document.querySelectorAll('.refresh-btn');
    refreshButtons.forEach(btn => {
        btn.addEventListener('click', handleRefresh);
    });

    // Filter Waktu Logic
    const perfFilter = document.getElementById('perf-filter');
    if (perfFilter) {
        perfFilter.addEventListener('change', function() {
            window.location.href = '<?= base_url('dashboard') ?>?filter=' + this.value;
        });
    }

    const commonOptions = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: '#F3F4F6' }, ticks: { color: '#9CA3AF', font: { family: 'Inter', size: 10 } }, beginAtZero: true },
            x: { grid: { display: false }, ticks: { color: '#9CA3AF', font: { family: 'Inter', size: 10 } } }
        }
    };
    const lineData = <?= isset($chartLine) ? $chartLine : '{"labels":[], "datasets":[]}' ?>;
    const barData = <?= isset($chartBar) ? $chartBar : '{"labels":[], "data":[]}' ?>;
    const lineCtx = document.getElementById('lineChart');
    if (lineCtx && lineData.labels) {
        const ctx = lineCtx.getContext('2d');
        
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: lineData.labels,
                datasets: (lineData.datasets || []).map(item => {
                    // Create gradient
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, item.color + '33'); // 20% opacity
                    gradient.addColorStop(1, item.color + '00'); // 0% opacity
                    
                    return {
                        label: item.label,
                        data: item.data,
                        borderColor: item.color,
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4, // Smooth curves
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: item.color,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    };
                })
            },
            options: { 
                ...commonOptions, 
                interaction: { intersect: false, mode: 'index' },
                plugins: { 
                    legend: { 
                        display: true, 
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 6, padding: 20, font: { family: 'Inter', size: 11, weight: 500 } }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { family: 'Inter', size: 13, weight: 600 },
                        bodyFont: { family: 'Inter', size: 12 },
                        cornerRadius: 8,
                        usePointStyle: true
                    }
                } 
            }
        });
    }

    const barCtx = document.getElementById('barChart');
    if (barCtx && barData.labels) {
        const ctx = barCtx.getContext('2d');
        
        // Define a set of modern, distinct colors for the bars
        const barColors = [
            '#3B82F6', // Blue
            '#10B981', // Emerald
            '#F59E0B', // Amber
            '#8B5CF6', // Violet
            '#EF4444', // Red
            '#06B6D4', // Cyan
            '#F472B6', // Pink
            '#6366F1', // Indigo
            '#84CC16', // Lime
            '#EC4899'  // Pink-600
        ];

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: barData.labels.map(l => l.length > 12 ? l.substring(0, 12) + '..' : l),
                datasets: [{ 
                    label: 'Jam', 
                    data: barData.data, 
                    backgroundColor: barColors, // Apply the array of colors
                    borderRadius: 8,
                    barPercentage: 0.6,
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true,
                        callbacks: { 
                            label: (ctx) => ` Rata-rata: ${ctx.raw} Jam` 
                        }
                    }
                }
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
