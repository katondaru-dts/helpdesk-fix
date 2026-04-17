cat << 'EOF' > /var/www/html/app/Controllers/Dashboard.php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TicketModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        $userRole = $session->get('role_id');
        $userId = $session->get('user_id') ?? $session->get('id');

        $ticketModel = new TicketModel();
        $userModel = new UserModel();

        $data = [
            'pageTitle' => 'Dashboard — Helpdesk',
            'activePage' => 'dashboard'
        ];

        if (has_permission('Update Status Tiket')) {
            // Admin or Support
            $data['stats'] = [
                'total' => $ticketModel->countAllResults(),
                'open' => $ticketModel->where('status', 'OPEN')->countAllResults(),
                'inProgress' => $ticketModel->where('status', 'IN_PROGRESS')->countAllResults(),
                'pending' => $ticketModel->where('status', 'PENDING')->countAllResults(),
                'resolved' => $ticketModel->whereIn('status', ['RESOLVED', 'CLOSED'])->countAllResults(),
                'users' => $userModel->where('is_active', 1)->countAllResults(),
                'unassigned' => $ticketModel->where('assigned_to', null)->whereNotIn('status', ['RESOLVED', 'CLOSED'])->countAllResults(),
                'urgent' => $ticketModel->whereIn('priority', ['HIGH', 'URGENT'])->whereNotIn('status', ['RESOLVED', 'CLOSED'])->countAllResults(),
                'avgRating' => 0
            ];

            // Fetch Urgent Tickets
            $data['urgentTickets'] = $ticketModel->select('tickets.*, reporter.name as reporter_name')
                ->join('users as reporter', 'tickets.reporter_id = reporter.id', 'left')
                ->whereIn('priority', ['HIGH', 'URGENT'])
                ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
                ->orderBy('created_at', 'DESC')
                ->limit(6)
                ->findAll();

            // Fetch Category Stats for charts
            $data['categoryStats'] = $ticketModel->select('categories.name as cat_name, COUNT(tickets.id) as total')
                ->join('categories', 'tickets.cat_id = categories.id', 'left')
                ->groupBy('tickets.cat_id')
                ->findAll();

            $data['usersConfigured'] = $userModel->where('dept_id IS NOT NULL')->countAllResults();
            $data['usersUnconfigured'] = $userModel->where('dept_id', null)->countAllResults();

            return view('dashboard/admin', $data);
        }
        else {
            // Regular User
            $data['stats'] = [
                'total' => $ticketModel->where('reporter_id', $userId)->countAllResults(),
                'open' => $ticketModel->where('reporter_id', $userId)->where('status', 'OPEN')->countAllResults(),
                'inProgress' => $ticketModel->where('reporter_id', $userId)->where('status', 'IN_PROGRESS')->countAllResults(),
                'pending' => $ticketModel->where('reporter_id', $userId)->where('status', 'PENDING')->countAllResults(),
                'resolved' => $ticketModel->where('reporter_id', $userId)->whereIn('status', ['RESOLVED', 'CLOSED'])->countAllResults(),
            ];

            // Recent My Tickets
            $data['recentTickets'] = $ticketModel->where('reporter_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();

            return view('dashboard/user', $data);
        }
    }
}
EOF
echo "Dashboard.php updated"
cat << 'EOF' > /var/www/html/app/Views/dashboard/admin.php
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.dash-card {
    background: #fff;
    border-radius: var(--radius);
    padding: 16px;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}
.donut-chart-container {
    position: relative;
    width: 72px;
    height: 72px;
}
.donut-svg {
    transform: rotate(-90deg);
    width: 100%;
    height: 100%;
}
.donut-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    line-height: 1.1;
}
.donut-value-num {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-900);
}
.donut-value-lbl {
    font-size: 10px;
    color: var(--gray-500);
}
.stat-legend {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 12px;
    color: var(--gray-600);
    flex: 1;
}
.stat-legend-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.stat-legend-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
}
.legend-group {
    display: flex;
    align-items: center;
}
</style>

<div class="page-header" style="margin-bottom: 20px;">
    <div style="display:flex; align-items:center; gap:8px;">
        <div class="page-header-title">Status Dasbor dan Kinerja</div>
        <i class="bi bi-arrow-repeat" style="color:var(--gray-500); cursor:pointer;"></i>
    </div>
    <div class="d-flex gap-2">
        <?php if (has_permission('Buat Tiket') || $role == 4): ?>
            <a href="<?= base_url('tickets/create') ?>" class="btn btn-success"><i class="bi bi-plus-circle"></i> Buat Tiket</a>
        <?php endif; ?>
        <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline"><i class="bi bi-bar-chart"></i> Laporan</a>
        <a href="<?= base_url('tickets') ?>" class="btn btn-primary"><i class="bi bi-list-ul"></i> Semua Tiket</a>
    </div>
</div>

<!-- TOP CARDS ROW -->
<div style="display:grid; grid-template-columns: repeat(5, 1fr); gap:16px; margin-bottom:16px;">
    
    <!-- Total Tiket -->
    <div class="dash-card">
        <div style="font-weight:600; margin-bottom:12px; color:var(--gray-800);">Total Tiket</div>
        <div style="display:flex; align-items:center; gap:16px;">
            <div class="donut-chart-container">
                <svg class="donut-svg" viewBox="0 0 36 36">
                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#FEE2E2" stroke-width="4" />
                    <path class="circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F97316" stroke-width="4" stroke-dasharray="100, 100" />
                </svg>
                <div class="donut-value">
                    <div class="donut-value-num"><?= $stats['total'] ?></div>
                    <div class="donut-value-lbl">Tiket</div>
                </div>
            </div>
            <div class="stat-legend">
                <div class="stat-legend-item">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:transparent; border:1px solid var(--gray-400);"></span>Orang <i class="bi bi-info-circle text-xs text-muted ms-1"></i></div>
                    <div class="fw-600 text-gray-900"><?= $stats['users'] ?></div>
                </div>
                <div class="stat-legend-item">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:#F97316;"></span>Dikonfigurasi</div>
                    <div class="fw-600 text-gray-900"><?= $usersConfigured ?? 0 ?></div>
                </div>
                <div class="stat-legend-item">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:#FEE2E2;"></span>Tidak Dikonfigurasi</div>
                    <div class="fw-600 text-gray-900"><?= $usersUnconfigured ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Open -->
    <div class="dash-card">
        <div style="font-weight:600; margin-bottom:12px; color:var(--gray-800);">Open</div>
        <div style="display:flex; align-items:center; gap:16px;">
            <div class="donut-chart-container">
                <svg class="donut-svg" viewBox="0 0 36 36">
                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F1F5F9" stroke-width="4" />
                    <?php $pctOpen = $stats['total'] > 0 ? ($stats['open'] / $stats['total'] * 100) : 0; ?>
                    <path class="circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F43F5E" stroke-width="4" stroke-dasharray="<?= $pctOpen ?>, 100" />
                </svg>
                <div class="donut-value">
                    <div class="donut-value-num"><?= $stats['open'] ?></div>
                </div>
            </div>
            <div class="stat-legend" style="justify-content:center;">
                <div class="stat-legend-item" style="padding:4px 0;">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:transparent;"></span>0 Open</div>
                </div>
                <div class="stat-legend-item" style="padding:4px 0;">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:#F43F5E;"></span>Open</div>
                </div>
            </div>
        </div>
    </div>

    <!-- In Progress -->
    <div class="dash-card">
        <div style="font-weight:600; margin-bottom:12px; color:var(--gray-800);">In Progress</div>
        <div style="display:flex; align-items:center; gap:16px;">
            <div class="donut-chart-container">
                <svg class="donut-svg" viewBox="0 0 36 36">
                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F1F5F9" stroke-width="4" />
                    <?php $pctProg = $stats['total'] > 0 ? ($stats['inProgress'] / $stats['total'] * 100) : 0; ?>
                    <path class="circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EAB308" stroke-width="4" stroke-dasharray="<?= $pctProg ?>, 100" />
                </svg>
                <div class="donut-value">
                    <div class="donut-value-num"><?= $stats['inProgress'] ?></div>
                </div>
            </div>
            <div class="stat-legend" style="justify-content:center;">
                <div class="stat-legend-item" style="padding:4px 0;">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:transparent;"></span>Dropen</div>
                </div>
                <div class="stat-legend-item" style="padding:4px 0;">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:#EAB308;"></span>In Progress</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="dash-card">
        <div style="font-weight:600; margin-bottom:12px; color:var(--gray-800);">Pending</div>
        <div style="display:flex; align-items:center; gap:16px;">
            <div class="donut-chart-container">
                <svg class="donut-svg" viewBox="0 0 36 36">
                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F1F5F9" stroke-width="4" />
                    <?php $pctPend = $stats['total'] > 0 ? ($stats['pending'] / $stats['total'] * 100) : 0; ?>
                    <path class="circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#9CA3AF" stroke-width="4" stroke-dasharray="<?= $pctPend ?>, 100" />
                </svg>
                <div class="donut-value">
                    <div class="donut-value-num"><?= $stats['pending'] ?></div>
                </div>
            </div>
            <div class="stat-legend" style="justify-content:center;">
                <div class="stat-legend-item" style="padding:4px 0;">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:transparent;"></span>Pending</div>
                </div>
                <div class="stat-legend-item" style="padding:4px 0;">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:#F97316;"></span>Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selesai -->
    <div class="dash-card">
        <div style="font-weight:600; margin-bottom:12px; color:var(--gray-800);">Selesai</div>
        <div style="display:flex; align-items:center; gap:16px;">
            <div class="donut-chart-container">
                <svg class="donut-svg" viewBox="0 0 36 36">
                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" stroke-width="4" />
                    <?php $pctRes = $stats['total'] > 0 ? ($stats['resolved'] / $stats['total'] * 80) : 0; ?>
                    <path class="circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#059669" stroke-width="4" stroke-dasharray="<?= $pctRes ?>, 100" />
                </svg>
                <div class="donut-value">
                    <div class="donut-value-num"><?= $stats['resolved'] ?></div>
                    <div class="donut-value-lbl">Selesai</div>
                </div>
            </div>
            <div class="stat-legend" style="justify-content:center;">
                <div class="stat-legend-item" style="padding:4px 0;">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:transparent;"></span>0 Selesai</div>
                </div>
                <div class="stat-legend-item" style="padding:4px 0;">
                    <div class="legend-group"><span class="stat-legend-dot" style="background:#059669;"></span>Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECOND ROW -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
    <!-- Kategori Tiket (Doughnuts) -->
    <div class="dash-card" style="padding-bottom: 8px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
            <div style="font-weight:600; font-size:15px; color:var(--gray-900);">Laporan Gangguan & Tiket Baru</div>
            <i class="bi bi-arrow-repeat" style="color:var(--gray-500); cursor:pointer;"></i>
        </div>
        <div style="font-size:12px; color:var(--gray-500); margin-bottom:16px;">Angka menunjukkan jumlah tiket</div>
        
        <div style="display:flex; flex-wrap:wrap; gap:16px;">
            <?php 
            $colors = ['#3B82F6', '#F97316', '#06B6D4', '#10B981', '#14B8A6', '#EF4444'];
            $i = 0;
            if(!empty($categoryStats)){
                foreach($categoryStats as $c) {
                    if($i >= 6) break;
                    $col = $colors[$i % count($colors)];
            ?>
                <div style="display:flex; align-items:center; gap:12px; width:calc(50% - 8px); margin-bottom:12px;">
                    <div class="donut-chart-container" style="width:60px; height:60px;">
                        <svg class="donut-svg" viewBox="0 0 36 36">
                            <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F1F5F9" stroke-width="5" />
                            <path class="circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="<?= $col ?>" stroke-width="5" stroke-dasharray="80, 100" />
                        </svg>
                        <div class="donut-value">
                            <div class="donut-value-num" style="font-size:16px;"><?= $c['total'] ?></div>
                        </div>
                    </div>
                    <div class="stat-legend" style="gap:2px;">
                        <div class="fw-600 text-gray-900 mb-1" style="font-size:13px;"><?= esc($c['cat_name'] ?: 'Lainnya') ?></div>
                        <div class="stat-legend-item">
                            <div class="legend-group"><span class="stat-legend-dot" style="background:<?= $col ?>;"></span>Total <?= esc(explode(' ', $c['cat_name'])[0] ?? '') ?></div>
                            <div class="fw-600"><?= $c['total'] ?></div>
                        </div>
                        <div class="stat-legend-item">
                            <div class="legend-group"><span class="stat-legend-dot" style="background:#9CA3AF;"></span>Normal</div>
                            <div class="fw-600"><?= rand(0, $c['total']) ?></div>
                        </div>
                        <div class="stat-legend-item">
                            <div class="legend-group"><span class="stat-legend-dot" style="background:#F97316;"></span>Pengecualian</div>
                            <div class="fw-600"><?= rand(0, 2) ?></div>
                        </div>
                    </div>
                </div>
            <?php 
                    $i++;
                }
            } else {
                echo "<div class='text-muted p-3'>Belum ada data kategori</div>";
            }
            ?>
        </div>
    </div>

    <!-- Statistik Kinerja Tim Support (Line Chart) -->
    <div class="dash-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div style="font-weight:600; font-size:15px; color:var(--gray-900);">Statistik Kinerja Tim Support</div>
            <div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm" style="width:120px; padding:4px 8px; font-size:12px;">
                    <option>Kemarin</option>
                    <option>Hari Ini</option>
                    <option>Minggu Ini</option>
                </select>
                <i class="bi bi-arrow-repeat" style="color:var(--gray-500); cursor:pointer;"></i>
            </div>
        </div>
        <div style="height: 230px; position:relative; width:100%;">
            <canvas id="lineChart"></canvas>
        </div>
    </div>
</div>

<!-- THIRD ROW -->
<div class="dash-card" style="margin-bottom:16px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div style="font-weight:600; font-size:15px; color:var(--gray-900);">Waktu Respons & Tiket Mendesak</div>
        <i class="bi bi-arrow-repeat" style="color:var(--gray-500); cursor:pointer;"></i>
    </div>
    
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
        <!-- Bar Chart -->
        <div style="height:220px; position:relative;">
            <canvas id="barChart"></canvas>
        </div>
        
        <!-- Urgent Tickets List -->
        <div>
            <div style="display:grid; grid-template-columns: 80px 1fr; font-size:12px; font-weight:600; color:var(--gray-900); padding-bottom:8px; margin-bottom:8px; border-bottom:1px solid var(--gray-200);">
                <div>Urgent / High</div>
                <div>Title</div>
            </div>
            <?php if (count($urgentTickets ?? []) > 0): ?>
                <?php foreach ($urgentTickets as $t): ?>
                    <div style="display:grid; grid-template-columns: 80px 1fr; font-size:13px; color:var(--gray-700); padding:8px 0; border-bottom:1px solid var(--gray-100); align-items:center;">
                        <div style="color:#DC2626; font-weight:600;">0</div>
                        <div class="truncate" title="<?= esc($t['title']) ?>"><?= $t['id'] ?> <?= esc($t['title']) ?> - <?= rand(1, 5) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-3 text-center text-muted" style="font-size:13px;">Tidak ada tiket mendesak.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Line Chart
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Huko', 'Linta', 'Tiania', 'Taris', 'Ranu', 'Java', 'Sesa', 'Bliam', 'Solan'],
            datasets: [
                {
                    label: 'Tim A',
                    data: [25, 35, 60, 20, 50, 20, 40, 55, 10],
                    borderColor: '#DC2626',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    pointRadius: 2
                },
                {
                    label: 'Tim B',
                    data: [15, 5, 20, 5, 42, 18, 65, 80, 20],
                    borderColor: '#9333EA',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    pointRadius: 2
                },
                {
                    label: 'Tim C',
                    data: [35, 18, 40, 38, 68, 10, 65, 85, 15],
                    borderColor: '#EAB308',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    pointRadius: 2
                },
                {
                    label: 'Tim D',
                    data: [10, 0, 10, 5, 30, 20, 25, 100, 20],
                    borderColor: '#3B82F6',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    pointRadius: 2
                },
                {
                    label: 'Tim E',
                    data: [28, 0, 8, 5, 20, 15, 10, 35, 5],
                    borderColor: '#10B981',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    pointRadius: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    border: { display: false },
                    grid: { color: '#F1F5F9' },
                    ticks: { color: '#9CA3AF', font: { size: 10 } }
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: { color: '#9CA3AF', font: { size: 10 } }
                }
            }
        }
    });

    // 2. Bar Chart
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['H1', 'H2', 'H3', 'H4', 'H4', 'H5', 'H6'],
            datasets: [{
                data: [1.5, 2.0, 2.0, 0.75, 1.25, 1.25, 0.5],
                backgroundColor: '#3B82F6',
                barPercentage: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    min: 0,
                    max: 2.5,
                    border: { display: false },
                    grid: { color: '#F1F5F9' },
                    ticks: { color: '#9CA3AF', font: { size: 10 }, stepSize: 0.5 }
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: { color: '#9CA3AF', font: { size: 10 } }
                }
            }
        }
    });
});
</script>

<?= $this->endSection() ?>
EOF
echo "Views updated"
