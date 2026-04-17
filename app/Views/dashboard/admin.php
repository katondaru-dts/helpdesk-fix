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
    color: #374151; /* gray-700 */
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
.card-title-main {
    font-size: 15px;
    font-weight: 600;
    color: #111827; /* gray-900 */
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
    color: #6B7280; /* gray-500 */
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
    font-weight: 500;
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
    padding-left: 6px; /* modern slight indent on hover */
}
.ticket-row:last-child {
    border-bottom: none;
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
    margin-bottom: 16px;
    background: #FAFAFA;
    padding: 12px;
    border-radius: 12px;
    border: 1px solid #F3F4F6;
}
</style>

<div class="admin-dash-wrapper">

    <!-- Header Section -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="font-size:24px; font-weight:700; color:#111827; letter-spacing:-0.5px;">Status Dasbor dan Kinerja</div>
            <i class="bi bi-arrow-repeat refresh-btn" title="Refresh Dasbor"></i>
        </div>
        <div class="d-flex gap-2">
            <?php if (has_permission('Buat Tiket') || $role == 4): ?>
                <a href="<?= base_url('tickets/create') ?>" class="btn btn-success" style="font-weight:500; border-radius:8px; padding:8px 16px;"><i class="bi bi-plus-circle"></i> Buat Tiket</a>
            <?php endif; ?>
            <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline-secondary" style="font-weight:500; border-radius:8px; padding:8px 16px;"><i class="bi bi-bar-chart"></i> Laporan</a>
            <a href="<?= base_url('tickets') ?>" class="btn btn-primary" style="font-weight:500; border-radius:8px; padding:8px 16px;"><i class="bi bi-list-ul"></i> Semua Tiket</a>
        </div>
    </div>

    <!-- ROW 1: STAT CARDS MAIN -->
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:16px;">

        <!-- Total Tiket: multi-color ring -->
        <div class="dash-card">
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
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F3F4F6" stroke-width="4.5"/>
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
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#10B981;"></span>Close</div><div style="font-weight:700; color:#111827;"><?= $stats['resolved'] ?></div></div>
                </div>
            </div>
        </div>

        <!-- Open -->
        <div class="dash-card">
            <div class="card-title-main">Open</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctOpen = $stats['total'] > 0 ? round($stats['open']/$stats['total']*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#FEE2E2" stroke-width="4.5"/>
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
        <div class="dash-card">
            <div class="card-title-main">In Progress</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctProg = $stats['total'] > 0 ? round($stats['inProgress']/$stats['total']*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#FEF9C3" stroke-width="4.5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EAB308" stroke-width="4.5" stroke-dasharray="<?= $pctProg ?>,100"/>
                    </svg>
                    <div class="donut-center">
                        <a href="<?= base_url('tickets') ?>?f-status=IN_PROGRESS" class="donut-link">
                            <div class="donut-num"><?= $stats['inProgress'] ?></div>
                        </a>
                    </div>
                </div>
                <div class="lgd">
                    <div class="lgd-row"><div class="lgd-left"><span class="lgd-dot" style="background:#EAB308;"></span>In Progress</div></div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="dash-card">
            <div class="card-title-main">Pending</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctPend = $stats['total'] > 0 ? round($stats['pending']/$stats['total']*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F3F4F6" stroke-width="4.5"/>
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#9CA3AF" stroke-width="4.5" stroke-dasharray="<?= $pctPend ?>,100"/>
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
        <div class="dash-card">
            <div class="card-title-main">Selesai</div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="donut-wrap">
                    <?php $pctRes = $stats['total'] > 0 ? round($stats['resolved']/$stats['total']*100) : 0; ?>
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#D1FAE5" stroke-width="4.5"/>
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

    <!-- ROW 2: Laporan Gangguan | Urgent + Belum Diassign -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

        <!-- Laporan Gangguan per Kategori - DINAMIS, multi-warna status -->
        <div class="dash-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <div class="card-title-main" style="margin-bottom:0; font-size:16px;">Laporan Gangguan &amp; Tiket Baru</div>
                <i class="bi bi-arrow-repeat refresh-btn" title="Refresh"></i>
            </div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:20px; font-weight:500;">Angka menunjukkan jumlah tiket terakumulasi</div>
            <div style="display:flex;flex-wrap:wrap;gap:16px;max-height:360px;overflow-y:auto;padding-right:6px;">
                <?php if (!empty($categoryStats)): ?>
                    <?php foreach ($categoryStats as $c):
                        $catTotal  = (int)($c['total'] ?? 0);
                        $catOpen   = (int)($c['open_count'] ?? 0);
                        $catProg   = (int)($c['inprogress_count'] ?? 0);
                        $catPend   = (int)($c['pending_count'] ?? 0);
                        $catRes    = (int)($c['resolved_count'] ?? 0);
                        $base      = $catTotal ?: 1;
                        $op2 = round($catOpen/$base*100);
                        $ip2 = round($catProg/$base*100);
                        $pd2 = round($catPend/$base*100);
                        $rs2 = round($catRes/$base*100);
                    ?>
                    <div class="cat-item-wrap">
                        <!-- Donut multi-warna status -->
                        <div class="donut-wrap" style="width:64px;height:64px;margin-top:2px;">
                            <svg viewBox="0 0 36 36">
                                <path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F3F4F6" stroke-width="4.8"/>
                                <?php if($op2>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F43F5E" stroke-width="4.8" stroke-dasharray="<?=$op2?>,100" stroke-dashoffset="0"/><?php endif;?>
                                <?php if($ip2>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EAB308" stroke-width="4.8" stroke-dasharray="<?=$ip2?>,100" stroke-dashoffset="-<?=$op2?>"/><?php endif;?>
                                <?php if($pd2>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#9CA3AF" stroke-width="4.8" stroke-dasharray="<?=$pd2?>,100" stroke-dashoffset="-<?=$op2+$ip2?>"/><?php endif;?>
                                <?php if($rs2>0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" stroke-width="4.8" stroke-dasharray="<?=$rs2?>,100" stroke-dashoffset="-<?=$op2+$ip2+$pd2?>"/><?php endif;?>
                                <?php if($catTotal == 0): ?><path d="M18 2.0845 a15.9155 15.9155 0 0 1 0 31.831 a15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#E5E7EB" stroke-width="4.8" stroke-dasharray="100,0"/><?php endif;?>
                            </svg>
                            <div class="donut-center">
                                <div class="cat-donut-num"><?= $catTotal ?></div>
                            </div>
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
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color:#9CA3AF;padding:12px;">Belum ada data kategori.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kanan: Urgent/High + Belum Diassign -->
        <div style="display:flex;flex-direction:column;gap:16px;">

            <div class="dash-card" style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid #F3F4F6;">
                    <div style="background:#FEE2E2; padding:6px; border-radius:8px; display:inline-flex;">
                        <i class="bi bi-exclamation-triangle-fill" style="color:#DC2626; font-size:15px;"></i>
                    </div>
                    <span class="card-title-main" style="margin-bottom:0; flex:1;">Tiket Urgent / High</span>
                    <a href="<?= base_url('tickets') ?>" class="btn btn-outline-secondary btn-sm" style="font-size:12px; font-weight:600; border-radius:6px;">Lihat Semua</a>
                </div>
                <?php if (!empty($urgentTickets)): foreach ($urgentTickets as $t): ?>
                <div class="ticket-row" onclick="window.location='<?= base_url('tickets/detail/'.$t['id']) ?>'">
                    <div style="min-width:0;flex:1;">
                        <div style="font-weight:600;font-size:13.5px;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= esc($t['title']) ?></div>
                        <div style="font-size:11.5px;color:#6B7280;margin-top:3px;">#<?= $t['id'] ?> &middot; <?= esc($t['reporter_name'] ?? '') ?></div>
                    </div>
                    <span class="badge b-<?= $t['status'] ?>" style="font-size:10.5px; padding:5px 8px; border-radius:6px;"><?= $t['priority'] ?></span>
                </div>
                <?php endforeach; else: ?>
                <div style="padding:16px;text-align:center;color:#9CA3AF;font-size:13.5px;">Tidak ada tiket mendesak.</div>
                <?php endif; ?>
            </div>

            <div class="dash-card" style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid #F3F4F6;">
                    <div style="background:#FEF3C7; padding:6px; border-radius:8px; display:inline-flex;">
                        <i class="bi bi-person-x-fill" style="color:#D97706; font-size:15px;"></i>
                    </div>
                    <span class="card-title-main" style="margin-bottom:0; flex:1;">Belum Diassign <span style="font-size:13px;color:#9CA3AF; font-weight:500;">(<?= $stats['unassigned'] ?>)</span></span>
                    <a href="<?= base_url('tickets') ?>" class="btn btn-outline-secondary btn-sm" style="font-size:12px; font-weight:600; border-radius:6px;">Lihat Semua</a>
                </div>
                <?php if (!empty($pendingTickets)): foreach ($pendingTickets as $t): ?>
                <div class="ticket-row" onclick="window.location='<?= base_url('tickets/detail/'.$t['id']) ?>'">
                    <div style="min-width:0;flex:1;">
                        <div style="font-weight:600;font-size:13.5px;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= esc($t['title']) ?></div>
                        <div style="font-size:11.5px;color:#6B7280;margin-top:3px;">#<?= $t['id'] ?> &middot; <?= esc($t['cat_name'] ?? '') ?></div>
                    </div>
                    <a href="<?= base_url('tickets/detail/'.$t['id']) ?>" class="btn btn-sm btn-primary" style="font-weight:600; border-radius:6px;" onclick="event.stopPropagation()"><i class="bi bi-person-plus"></i> Assign</a>
                </div>
                <?php endforeach; else: ?>
                <div style="padding:16px;text-align:center;color:#9CA3AF;font-size:13.5px;">Semua tiket sudah diassign.</div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- ROW 3: Line Chart | Bar Chart -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div class="dash-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <div class="card-title-main" style="margin-bottom:0; font-size:16px;">Statistik Kinerja Tim Support</div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <?php $currentFilter = request()->getGet('filter') ?: 'minggu_ini'; ?>
                    <select id="perf-filter" class="form-select form-select-sm" style="width:150px; font-size:13px; font-weight:500; border-radius:6px; height:36px; padding-top:4px; padding-bottom:4px;">
                        <option value="hari_ini" <?= $currentFilter == 'hari_ini' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="kemarin" <?= $currentFilter == 'kemarin' ? 'selected' : '' ?>>Kemarin</option>
                        <option value="minggu_ini" <?= $currentFilter == 'minggu_ini' ? 'selected' : '' ?>>Minggu Ini</option>
                        <option value="bulan_ini" <?= $currentFilter == 'bulan_ini' ? 'selected' : '' ?>>Bulan Ini</option>
                    </select>
                    <i class="bi bi-arrow-repeat refresh-btn" title="Refresh"></i>
                </div>
            </div>
            <div style="height:240px;position:relative;"><canvas id="lineChart"></canvas></div>
            <div style="margin-top:16px; padding-top:12px; border-top:1px solid #F3F4F6; font-size:11.5px; color:#6B7280; line-height:1.5;">
                <i class="bi bi-info-circle-fill" style="color:#3B82F6; margin-right:4px;"></i> 
                Angka pada grafik menunjukkan <strong>jumlah tiket yang berhasil diselesaikan</strong> (status Selesai/Closed) oleh masing-masing teknisi pada periode yang dipilih.
            </div>
        </div>
        <div class="dash-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <div class="card-title-main" style="margin-bottom:0; font-size:16px;">Waktu Respons Tiket</div>
                <i class="bi bi-arrow-repeat refresh-btn" title="Refresh"></i>
            </div>
            <div style="height:240px;position:relative;"><canvas id="barChart"></canvas></div>
            <div style="margin-top:16px; padding-top:12px; border-top:1px solid #F3F4F6; font-size:11.5px; color:#6B7280; line-height:1.5;">
                <i class="bi bi-info-circle-fill" style="color:#3B82F6; margin-right:4px;"></i> 
                Angka menunjukkan <strong>rata-rata waktu penanganan (dalam Jam)</strong> mulai dari tiket dibuat hingga dinyatakan selesai untuk setiap kategori.
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded",function(){
    document.querySelectorAll('.refresh-btn').forEach(function(btn){
        btn.addEventListener('click',function(){
            btn.classList.add('spinning');
            setTimeout(function(){ location.reload(); }, 400);
        });
    });

    const perfFilter = document.getElementById('perf-filter');
    if (perfFilter) {
        perfFilter.addEventListener('change', function() {
            const val = this.value;
            window.location.href = '<?= base_url('dashboard') ?>?filter=' + val;
        });
    }

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { border: { display: false }, grid: { color: '#F3F4F6' }, ticks: { color: '#9CA3AF', font: { family: 'Inter', size: 10 } }, beginAtZero: true },
            x: { border: { display: false }, grid: { display: false }, ticks: { color: '#9CA3AF', font: { family: 'Inter', size: 10 } } }
        }
    };

    // Data Dinamis dari Backend
    const lineData = <?= isset($chartLine) ? $chartLine : '{"labels":[], "datasets":[]}' ?>;
    const barData = <?= isset($chartBar) ? $chartBar : '{"labels":[], "data":[]}' ?>;

    const lineCtx = document.getElementById('lineChart');
    if (lineCtx && lineData.labels) {
        let datasetsArr = [];
        if (lineData.datasets && lineData.datasets.length > 0) {
            lineData.datasets.forEach(item => {
                datasetsArr.push({
                    label: item.label,
                    data: item.data,
                    borderColor: item.color,
                    backgroundColor: 'transparent',
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: '#fff'
                });
            });
        } else {
            // Fallback
            datasetsArr.push({
                label: 'Belum ada tiket selesai',
                data: [0,0,0,0,0,0,0],
                borderColor: '#E5E7EB',
                backgroundColor: 'transparent',
                borderWidth: 2.5
            });
        }

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: lineData.labels,
                datasets: datasetsArr
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: { display: true, position: 'bottom', align: 'center', padding: { top: 20 }, labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Inter', size: 11 } } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    ...commonOptions.scales,
                    y: { ...commonOptions.scales.y, suggestedMax: 5, ticks: { ...commonOptions.scales.y.ticks, precision: 0 } }
                }
            }
        });
    }

    const barCtx = document.getElementById('barChart');
    if (barCtx && barData.labels) {
        const shortLabels = barData.labels.map(l => l.length > 15 ? l.substring(0, 15) + '...' : l);
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: shortLabels,
                datasets: [{
                    label: 'Waktu Respons (Jam)',
                    data: barData.data,
                    backgroundColor: '#3B82F6',
                    borderRadius: 4,
                    barPercentage: 0.5 
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) { return ctx.raw + ' Jam'; }
                        }
                    }
                },
                scales: {
                    ...commonOptions.scales,
                    y: { ...commonOptions.scales.y, suggestedMax: 5 }
                }
            }
        });
    }
});
</script>



<?= $this->endSection() ?>



