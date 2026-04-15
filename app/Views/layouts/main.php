<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($unreadNotifications) && $unreadNotifications > 0) ? '(' . $unreadNotifications . ') ' : '' ?><?= $pageTitle ?? 'Helpdesk Pusim' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>

<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="<?= base_url('images/logo.png') ?>" alt="Logo" style="width: 42px; height: 42px; border-radius: 6px; object-fit: cover; margin-right: 8px;">
            <span>Helpdesk Pusim</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>
            <?php
            $role = session()->get('role_id');
            $isStaff = ($role == 1 || $role == 2 || $role == 4);
            ?>
            <a href="<?= base_url('dashboard') ?>" class="<?= $activePage == 'dashboard' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="<?= base_url('tickets') ?>" class="<?= $activePage == 'tickets' ? 'active' : '' ?>"><i class="bi bi-ticket-detailed"></i> <?= $isStaff ? 'Semua Tiket' : 'Tiket Saya' ?></a>

            <?php if (has_permission('Buat Tiket') || $role == 4): ?>
            <a href="<?= base_url('tickets/create') ?>" class="<?= $activePage == 'ticket-create' ? 'active' : '' ?>"><i class="bi bi-plus-circle"></i> Buat Tiket</a>
            <?php endif; ?>

            <?php if ($isStaff): ?>
            <div class="nav-section" style="margin-top: 16px;">Administrasi</div>
            <?php if (has_permission('Kelola User')): ?>
            <a href="<?= base_url('admin/users') ?>" class="<?= $activePage == 'admin-users' ? 'active' : '' ?>"><i class="bi bi-people"></i> Kelola User</a>
            <?php endif; ?>
            <?php if (has_permission('Kelola Departemen')): ?>
            <a href="<?= base_url('admin/departments') ?>" class="<?= $activePage == 'admin-departments' ? 'active' : '' ?>"><i class="bi bi-building"></i> Departemen</a>
            <?php endif; ?>
            <?php if (has_permission('Kelola Kategori')): ?>
            <a href="<?= base_url('admin/categories') ?>" class="<?= $activePage == 'admin-categories' ? 'active' : '' ?>"><i class="bi bi-tag"></i> Kategori</a>
            <?php endif; ?>
            <?php if (has_permission('Kelola Role')): ?>
            <a href="<?= base_url('admin/roles') ?>" class="<?= $activePage == 'admin-roles' ? 'active' : '' ?>"><i class="bi bi-shield-check"></i> Role & Izin</a>
            <?php endif; ?>
            <?php if (has_permission('Lihat Laporan')): ?>
            <a href="<?= base_url('admin/reports') ?>" class="<?= $activePage == 'admin-reports' ? 'active' : '' ?>"><i class="bi bi-graph-up"></i> Laporan</a>
            <?php endif; ?>
            <?php if (has_permission('Lihat Audit Log')): ?>
            <a href="<?= base_url('admin/audit-logs') ?>" class="<?= $activePage == 'admin-audit-logs' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i> Audit Log</a>
            <?php endif; ?>
            <?php endif; ?>

            <div class="nav-section" style="margin-top: 16px;">Akun</div>
            <a href="<?= base_url('notifications') ?>" class="<?= $activePage == 'notifications' ? 'active' : '' ?>">
                <i class="bi bi-bell"></i> Notifikasi
                <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                    <span class="nav-badge" style="background: #ef4444;"><?= $unreadNotifications ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= base_url('profile') ?>" class="<?= $activePage == 'profile' ? 'active' : '' ?>"><i class="bi bi-person-circle"></i> Profil Saya</a>
            <a href="<?= base_url('logout') ?>" style="color: #f87171;"><i class="bi bi-box-arrow-right"></i> Keluar</a>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="page-title" style="flex:1;">
                <?= $pageTitle ?? 'Halaman' ?>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <a href="<?= base_url('notifications') ?>" style="position:relative;display:inline-flex;align-items:center;color:var(--gray-500);text-decoration:none;" title="Notifikasi">
                    <i class="bi bi-bell<?= (isset($unreadNotifications) && $unreadNotifications > 0) ? '-fill' : '' ?>" style="font-size:18px;<?= (isset($unreadNotifications) && $unreadNotifications > 0) ? 'color:#f59e0b;' : '' ?>"></i>
                    <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                        <span style="position:absolute;top:-6px;right:-8px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;"><?= $unreadNotifications > 99 ? '99+' : $unreadNotifications ?></span>
                    <?php endif; ?>
                </a>
                <span style="font-size:13px;color:var(--gray-600);"><?= session()->get('name') ?></span>
                <span style="font-size:12px;color:var(--gray-400);"><?= session()->get('role_name') ?></span>
            </div>
        </header>

        <div class="main-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" style="background:#D1FAE5;border:1px solid #6EE7B7;color:#065F46;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-check-circle"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error" style="background:#FEE2E2;border:1px solid #FCA5A5;color:#991B1B;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    });
</script>

</body>
</html>





