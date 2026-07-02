<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        margin-bottom: 10px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 350px 1fr;
        align-items: center;
        gap: 30px;
        margin-bottom: 24px;
    }
    
    .form-label-col {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        text-align: right;
    }
    
    .form-label-col label {
        color: var(--gray-700); /* Abu gelap standar */
        font-weight: 600;
        font-size: 14px;
        margin: 0;
        user-select: none;
    }
    
    .form-control-col {
        max-width: 450px;
        position: relative;
    }

    /* Tooltips */
    .info-tooltip-trigger {
        position: relative;
        cursor: pointer;
        color: #9ca3af;
        display: inline-flex;
        align-items: center;
        transition: color 0.2s;
    }
    
    .info-tooltip-trigger:hover {
        color: var(--primary);
    }
    
    .info-tooltip-trigger::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%) scale(0.9);
        background: #1e293b;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        line-height: 1.4;
        white-space: normal;
        width: 240px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s, transform 0.2s, visibility 0.2s;
        z-index: 100;
        pointer-events: none;
        font-weight: 500;
        text-align: center;
    }
    
    .info-tooltip-trigger::before {
        content: '';
        position: absolute;
        bottom: 115%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: #1e293b transparent transparent transparent;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s, visibility 0.2s;
        z-index: 100;
        pointer-events: none;
    }
    
    .info-tooltip-trigger:hover::after,
    .info-tooltip-trigger:hover::before {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) scale(1);
    }

    /* Custom Dropdowns */
    .custom-select-container {
        position: relative;
        width: 100%;
    }
    
    .custom-select-trigger {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #374151; /* abu gelap standar */
        user-select: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .custom-select-trigger:hover {
        border-color: #9ca3af;
    }
    
    .custom-select-container.open .custom-select-trigger {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    
    .custom-select-chevron {
        font-size: 12px;
        color: #9ca3af;
        transition: transform 0.2s;
    }
    
    .custom-select-container.open .custom-select-chevron {
        transform: rotate(180deg);
    }
    
    .custom-select-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 6px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 50;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: opacity 0.15s, transform 0.15s, visibility 0.15s;
        max-height: 250px;
        overflow-y: auto;
    }
    
    .custom-select-container.open .custom-select-options {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .custom-select-option {
        padding: 10px 14px;
        cursor: pointer;
        background: white;
        color: #374151; /* abu gelap standar */
        font-size: 14px;
        transition: background 0.15s, color 0.15s;
    }
    
    .custom-select-option:hover {
        background: #f3f4f6; /* abu muda hover */
    }
    
    .custom-select-option.selected {
        background: #ef4444 !important; /* background merah */
        color: white !important; /* teks putih */
        font-weight: 600;
    }

    /* Radio Buttons */
    .radio-group-horizontal {
        display: flex;
        gap: 20px;
        align-items: center;
        height: 40px;
    }
    
    .radio-option {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .radio-option input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: #B8935A;
        cursor: pointer;
    }
    
    .radio-option label {
        font-size: 14px;
        color: #374151; /* abu gelap standar */
        cursor: pointer;
        font-weight: 500;
        user-select: none;
    }
    
    .info-radio {
        font-size: 14px;
        color: #9ca3af;
    }

    /* Toggle Switches */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }
    
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 34px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .toggle-switch input:checked + .toggle-slider {
        background-color: #22c55e;
    }
    
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    /* Suffix Input */
    .suffix-input-wrapper {
        position: relative;
        max-width: 130px;
    }
    
    .form-control-timeout {
        width: 100%;
        padding: 10px 45px 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        color: #374151; /* abu gelap standar */
        font-size: 14px;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .form-control-timeout:focus {
        border-color: #B8935A;
        box-shadow: 0 0 0 3px rgba(184, 147, 90, 0.15);
    }
    
    .input-suffix {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af; /* abu-abu */
        font-size: 13px;
        font-weight: 500;
        pointer-events: none;
        user-select: none;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        
        .form-label-col {
            justify-content: flex-start;
            text-align: left;
        }
    }
</style>

<div class="page-header">
    <div>
        <div class="page-header-title">Akun dan Keamanan</div>
        <div class="page-header-sub">Kelola pengguna, departemen, konfigurasi peran, serta parameter kebijakan sistem</div>
    </div>
</div>

<hr style="border: 0; height: 1px; background: #e5e7eb; margin: 20px 0 25px;">

<!-- Tab Navigation -->
<div class="tabs-container" style="display: flex; gap: 8px; border-bottom: 2px solid #e5e7eb; margin-bottom: 30px;">
    <?php if (is_admin() || has_permission('Kelola User')): ?>
    <button class="tab-button <?= $activeTab == 'users' ? 'active' : '' ?>" onclick="switchTab('users')" id="btn-tab-users" style="background: none; border: none; border-bottom: 3px solid <?= $activeTab == 'users' ? 'var(--primary)' : 'transparent' ?>; padding: 12px 24px; color: <?= $activeTab == 'users' ? 'var(--primary)' : '#6b7280' ?>; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-people"></i> Kelola User
    </button>
    <?php endif; ?>

    <?php if (is_admin() || has_permission('Kelola Departemen')): ?>
    <button class="tab-button <?= $activeTab == 'depts' ? 'active' : '' ?>" onclick="switchTab('depts')" id="btn-tab-depts" style="background: none; border: none; border-bottom: 3px solid <?= $activeTab == 'depts' ? 'var(--primary)' : 'transparent' ?>; padding: 12px 24px; color: <?= $activeTab == 'depts' ? 'var(--primary)' : '#6b7280' ?>; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-building"></i> Departemen
    </button>
    <?php endif; ?>

    <?php if (is_admin() || has_permission('Kelola Role') || has_permission('Kelola Role & Izin')): ?>
    <button class="tab-button <?= $activeTab == 'roles' ? 'active' : '' ?>" onclick="switchTab('roles')" id="btn-tab-roles" style="background: none; border: none; border-bottom: 3px solid <?= $activeTab == 'roles' ? 'var(--primary)' : 'transparent' ?>; padding: 12px 24px; color: <?= $activeTab == 'roles' ? 'var(--primary)' : '#6b7280' ?>; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-shield-check"></i> Role & Izin
    </button>
    <?php endif; ?>

    <?php if (is_admin()): ?>
    <button class="tab-button <?= $activeTab == 'settings' ? 'active' : '' ?>" onclick="switchTab('settings')" id="btn-tab-settings" style="background: none; border: none; border-bottom: 3px solid <?= $activeTab == 'settings' ? 'var(--primary)' : 'transparent' ?>; padding: 12px 24px; color: <?= $activeTab == 'settings' ? 'var(--primary)' : '#6b7280' ?>; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-sliders"></i> Parameter Dasar
    </button>
    <?php endif; ?>
</div>

<!-- Alert Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-4" style="display:flex;align-items:center;gap:10px;background:#d1fae5;color:#065f46;padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid #a7f3d0;">
        <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-4" style="display:flex;align-items:center;gap:10px;background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid #fca5a5;">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Tab Content: Kelola User -->
<?php if (is_admin() || has_permission('Kelola User')): ?>
<div class="tab-content" id="tab-users" style="display: <?= $activeTab == 'users' ? 'block' : 'none' ?>;">
    <div class="card mb-4" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:15px 20px; margin-bottom:20px;">
        <form action="<?= base_url('admin/security') ?>" method="GET" style="display:flex; gap:10px; align-items:center">
            <input type="hidden" name="activeTab" value="users">
            <div style="flex:1; position:relative">
                <i class="bi bi-search" style="position:absolute;left:10px;top:10px;color:#9ca3af"></i>
                <input type="text" name="search" value="<?= esc($search ?? '') ?>" class="form-control" placeholder="Cari nama atau email..." style="width:100%;padding:8px 8px 8px 35px;border-radius:8px;border:1px solid #d1d5db; box-sizing: border-box; outline: none;">
            </div>
            <select name="f-role" class="form-select" style="width:160px;padding:8px;border-radius:8px;border:1px solid #d1d5db">
                <option value="">Semua Role</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ($f_role ?? '') == $r['id'] ? 'selected' : '' ?>><?= esc($r['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary" style="background:#3b82f6;color:white;padding:8px 15px;border:none;border-radius:8px;font-weight:bold;cursor:pointer">Cari</button>
            <a href="<?= base_url('admin/security?activeTab=users') ?>" class="btn btn-outline" style="background:white;color:#6b7280;padding:8px 15px;border:1px solid #d1d5db;border-radius:8px;font-weight:bold;cursor:pointer; display: inline-flex; align-items:center;"><i class="bi bi-x-circle"></i> Reset</a>
        </form>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openUserModal()" style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: opacity 0.2s;">
            <i class="bi bi-person-plus"></i> Tambah User
        </button>
    </div>

    <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);overflow:hidden; padding: 0;">
        <table class="table" style="width:100%;border-collapse:collapse;text-align:left">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;background:#f9fafb;color:#4b5563;font-size:13px">
                    <th style="padding:15px 20px">User</th>
                    <th style="padding:15px">Email</th>
                    <th style="padding:15px">Role</th>
                    <th style="padding:15px">Departemen</th>
                    <th style="padding:15px">Status</th>
                    <th style="padding:15px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <td style="padding:15px 20px">
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:36px;height:36px;border-radius:50%;background:#3b82f6;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px">
                                    <?= strtoupper(substr(esc($u['name']), 0, 2)) ?>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:14px;color:#111827"><?= esc($u['name']) ?></div>
                                    <div style="font-size:12px;color:#6b7280">ID: <?= $u['id'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:15px;font-size:14px;color:#374151"><?= esc($u['email']) ?></td>
                        <td style="padding:15px"><span class="badge" style="background:#f3f4f6;color:#374151;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:600"><?= esc($u['role_name']) ?></span></td>
                        <td style="padding:15px;font-size:14px;color:#374151"><?= esc($u['dept_name']) ?></td>
                        <td style="padding:15px">
                            <?php if ($u['is_active']): ?>
                                <span class="badge" style="background:#d1fae5;color:#065f46;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:600">Aktif</span>
                            <?php else: ?>
                                <span class="badge" style="background:#fee2e2;color:#991b1b;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:600">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:15px;text-align:right;">
                            <div style="display:flex;gap:5px;justify-content:flex-end;align-items:center;">
                                <button style="background:white;color:#3b82f6;border:1px solid #3b82f6;padding:6px 12px;border-radius:6px;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:4px;" onclick='openUserModal(<?= json_encode($u) ?>)'><i class="bi bi-pencil"></i> Edit</button>
                                
                                <?php if ($u['role_id'] != 1): ?>
                                    <form action="<?= base_url('admin/users/toggle_status') ?>" method="POST" style="display:inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="current" value="<?= $u['is_active'] ?>">
                                        <?php if ($u['is_active']): ?>
                                            <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Nonaktifkan</button>
                                        <?php else: ?>
                                            <button type="submit" style="background:#d1fae5;color:#065f46;border:none;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Aktifkan</button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>

                                <?php if ($u['id'] != session()->get('id')): ?>
                                    <form action="<?= base_url('admin/users/delete') ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button type="submit" style="background:#ef4444;color:white;border:none;padding:7px 12px;border-radius:6px;cursor:pointer"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Tab Content: Departemen -->
<?php if (is_admin() || has_permission('Kelola Departemen')): ?>
<div class="tab-content" id="tab-depts" style="display: <?= $activeTab == 'depts' ? 'block' : 'none' ?>;">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openDeptModal()" style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: opacity 0.2s;">
            <i class="bi bi-plus-circle"></i> Tambah Departemen
        </button>
    </div>

    <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);overflow:hidden; padding: 0;">
        <table class="table" style="width:100%;border-collapse:collapse;text-align:left">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;background:#f9fafb;color:#4b5563;font-size:13px">
                    <th style="padding:15px 20px">Nama Departemen</th>
                    <th style="padding:15px;text-align:center">User Terdaftar</th>
                    <th style="padding:15px">Status</th>
                    <th style="padding:15px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $d): ?>
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:15px 20px;font-weight:600;color:#111827"><?= esc($d['name']) ?></td>
                    <td style="padding:15px;text-align:center">
                        <span style="background:#e0e7ff;color:#3730a3;padding:4px 10px;border-radius:999px;font-weight:600;font-size:13px"><?= $d['user_count'] ?></span>
                    </td>
                    <td style="padding:15px">
                        <?php if ($d['is_active']): ?>
                            <span style="background:#d1fae5;color:#065f46;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:600">Aktif</span>
                        <?php else: ?>
                            <span style="background:#fee2e2;color:#991b1b;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:600">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:15px;text-align:right;">
                        <div style="display:flex;gap:5px;justify-content:flex-end;align-items:center;">
                            <button style="background:white;color:#3b82f6;border:1px solid #3b82f6;padding:6px 12px;border-radius:6px;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:4px;" onclick='openDeptModal(<?= json_encode($d) ?>)'><i class="bi bi-pencil"></i> Edit</button>
                            <?php if ($d['id'] != 1): ?>
                                <form action="<?= base_url('admin/departments/toggle_status') ?>" method="POST" style="display:inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <input type="hidden" name="current" value="<?= $d['is_active'] ?>">
                                    <?php if ($d['is_active']): ?>
                                        <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Nonaktifkan</button>
                                    <?php else: ?>
                                        <button type="submit" style="background:#d1fae5;color:#065f46;border:none;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Aktifkan</button>
                                    <?php endif; ?>
                                </form>
                                <form action="<?= base_url('admin/departments/delete') ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus departemen ini?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:7px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer"><i class="bi bi-trash"></i> Hapus</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Tab Content: Role & Izin -->
<?php if (is_admin() || has_permission('Kelola Role') || has_permission('Kelola Role & Izin')): ?>
<div class="tab-content" id="tab-roles" style="display: <?= $activeTab == 'roles' ? 'block' : 'none' ?>;">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openRoleModal()" style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: opacity 0.2s;">
            <i class="bi bi-shield-plus"></i> Tambah Role baru
        </button>
    </div>

    <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);overflow:hidden; padding:0;">
        <table class="table" style="width:100%;border-collapse:collapse;text-align:left">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;background:#f9fafb;color:#4b5563;font-size:13px">
                    <th style="padding:15px 20px">Kode Role</th>
                    <th style="padding:15px">Nama Role</th>
                    <th style="padding:15px">Izin Akses</th>
                    <th style="padding:15px;text-align:center">User</th>
                    <th style="padding:15px;text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $r): 
                    $perms = json_decode($r['permissions'], true) ?: [];
                ?>
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <td style="padding:15px 20px;font-weight:bold;color:#111827">
                            <span style="background:#f3f4f6;padding:4px 8px;border-radius:6px;font-family:monospace;font-size:13px;border:1px solid #e5e7eb"><?= esc($r['code']) ?></span>
                        </td>
                        <td style="padding:15px;font-weight:600;color:#1f2937"><?= esc($r['name']) ?></td>
                        <td style="padding:15px;font-size:13px;color:#4b5563">
                            <?php if ($r['id'] == 1): ?>
                                <span style="color:#059669;font-weight:600"><i class="bi bi-star-fill"></i> Akses Penuh Sistem</span>
                            <?php else: ?>
                                <div style="display:flex;gap:5px;flex-wrap:wrap">
                                    <?php if(empty($perms)) echo '<em>Tidak ada izin khusus</em>'; ?>
                                    <?php foreach($perms as $p): ?>
                                        <span style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;padding:2px 6px;border-radius:4px;font-size:11px"><?= esc($p) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="padding:15px;text-align:center">
                            <span style="background:#e0e7ff;color:#3730a3;padding:4px 10px;border-radius:999px;font-weight:600;font-size:13px"><?= $r['user_count'] ?></span>
                        </td>
                        <td style="padding:15px;text-align:right">
                            <div style="display:flex;gap:8px;justify-content:flex-end;">
                                <button style="background:white;color:#3b82f6;border:1px solid #3b82f6;padding:6px 12px;border-radius:6px;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:4px;" onclick='openRoleModal(<?= json_encode($r) ?>)'>
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                
                                <?php if ($r['user_count'] == 0 && $r['id'] != 1): ?>
                                    <form action="<?= base_url('admin/roles/delete') ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus role ini secara permanen?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <button type="submit" style="background:#ef4444;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:4px;">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Tab Content: Parameter Dasar -->
<?php if (is_admin()): ?>
<div class="tab-content" id="tab-settings" style="display: <?= $activeTab == 'settings' ? 'block' : 'none' ?>;">
    <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); padding: 40px 50px;">
        <form action="<?= base_url('admin/settings/save') ?>" method="POST" id="settingsForm">
            <?= csrf_field() ?>
            
            <!-- Upaya Masuk Gagal Maks. -->
            <div class="form-row">
                <div class="form-label-col">
                    <span class="info-tooltip-trigger" data-tooltip="Jumlah maksimum percobaan login yang salah sebelum akun pengguna dikunci sementara.">
                        <i class="bi bi-info-circle"></i>
                    </span>
                    <label>Upaya Masuk Gagal Maks.</label>
                </div>
                <div class="form-control-col">
                    <div class="custom-select-container" id="select-max-attempts">
                        <input type="hidden" name="max_failed_attempts" id="input-max-attempts" value="<?= esc($settings['max_failed_attempts'] ?? '5') ?>">
                        <div class="custom-select-trigger" onclick="toggleDropdown('select-max-attempts')">
                            <span class="custom-select-text">5 kali</span>
                            <i class="bi bi-chevron-down custom-select-chevron"></i>
                        </div>
                        <div class="custom-select-options">
                            <div class="custom-select-option" data-value="1" onclick="selectOption('select-max-attempts', '1', '1 kali')">1 kali</div>
                            <div class="custom-select-option" data-value="2" onclick="selectOption('select-max-attempts', '2', '2 kali')">2 kali</div>
                            <div class="custom-select-option" data-value="3" onclick="selectOption('select-max-attempts', '3', '3 kali')">3 kali</div>
                            <div class="custom-select-option" data-value="4" onclick="selectOption('select-max-attempts', '4', '4 kali')">4 kali</div>
                            <div class="custom-select-option" data-value="5" onclick="selectOption('select-max-attempts', '5', '5 kali')">5 kali</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Durasi Penguncian -->
            <div class="form-row">
                <div class="form-label-col">
                    <span class="info-tooltip-trigger" data-tooltip="Durasi waktu akun pengguna akan dikunci setelah melebihi batas maksimum percobaan login.">
                        <i class="bi bi-info-circle"></i>
                    </span>
                    <label>Durasi Penguncian</label>
                </div>
                <div class="form-control-col">
                    <div class="custom-select-container" id="select-lockout-duration">
                        <input type="hidden" name="lockout_duration" id="input-lockout-duration" value="<?= esc($settings['lockout_duration'] ?? '10') ?>">
                        <div class="custom-select-trigger" onclick="toggleDropdown('select-lockout-duration')">
                            <span class="custom-select-text">10 menit</span>
                            <i class="bi bi-chevron-down custom-select-chevron"></i>
                        </div>
                        <div class="custom-select-options">
                            <div class="custom-select-option" data-value="10" onclick="selectOption('select-lockout-duration', '10', '10 menit')">10 menit</div>
                            <div class="custom-select-option" data-value="20" onclick="selectOption('select-lockout-duration', '20', '20 menit')">20 menit</div>
                            <div class="custom-select-option" data-value="30" onclick="selectOption('select-lockout-duration', '30', '30 menit')">30 menit</div>
                            <div class="custom-select-option" data-value="40" onclick="selectOption('select-lockout-duration', '40', '40 menit')">40 menit</div>
                            <div class="custom-select-option" data-value="50" onclick="selectOption('select-lockout-duration', '50', '50 menit')">50 menit</div>
                            <div class="custom-select-option" data-value="60" onclick="selectOption('select-lockout-duration', '60', '60 menit')">60 menit</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kekuatan Kata Sandi Minimum -->
            <div class="form-row">
                <div class="form-label-col">
                    <label>Kekuatan Kata Sandi Minimum</label>
                </div>
                <div class="form-control-col">
                    <div class="radio-group-horizontal">
                        <div class="radio-option">
                            <input type="radio" id="strength_weak" name="min_password_strength" value="Lemah" <?= ($settings['min_password_strength'] ?? 'Sedang') == 'Lemah' ? 'checked' : '' ?>>
                            <label for="strength_weak">Lemah</label>
                            <span class="info-tooltip-trigger info-radio" data-tooltip="Kriteria Lemah: Minimal 6 karakter tanpa batasan jenis karakter khusus.">
                                <i class="bi bi-info-circle"></i>
                            </span>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="strength_medium" name="min_password_strength" value="Sedang" <?= ($settings['min_password_strength'] ?? 'Sedang') == 'Sedang' ? 'checked' : '' ?>>
                            <label for="strength_medium">Sedang</label>
                            <span class="info-tooltip-trigger info-radio" data-tooltip="Kriteria Sedang: Minimal 8 karakter, wajib gabungan huruf dan angka.">
                                <i class="bi bi-info-circle"></i>
                            </span>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="strength_strong" name="min_password_strength" value="Kuat" <?= ($settings['min_password_strength'] ?? 'Sedang') == 'Kuat' ? 'checked' : '' ?>>
                            <label for="strength_strong">Kuat</label>
                            <span class="info-tooltip-trigger info-radio" data-tooltip="Kriteria Kuat: Minimal 10 karakter, wajib gabungan huruf besar, kecil, angka, dan simbol.">
                                <i class="bi bi-info-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aktifkan Masa Berlaku Kata Sandi Maksimum -->
            <div class="form-row">
                <div class="form-label-col">
                    <label>Aktifkan Masa Berlaku Kata Sandi Maksimum</label>
                </div>
                <div class="form-control-col">
                    <label class="toggle-switch">
                        <input type="checkbox" name="enable_max_password_lifetime" id="enable_max_password_lifetime" value="1" <?= ($settings['enable_max_password_lifetime'] ?? '0') == '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Detail Masa Berlaku Kata Sandi (Collapsible) -->
            <div id="password-lifetime-details" style="display: <?= ($settings['enable_max_password_lifetime'] ?? '0') == '1' ? 'block' : 'none' ?>; border-left: 3px solid #e5e7eb; padding-left: 20px; margin-left: 20px; margin-bottom: 24px;">
                <!-- Kata Sandi Akan Kedaluwarsa Dalam -->
                <div class="form-row" style="margin-bottom: 12px;">
                    <div class="form-label-col">
                        <label>Kata Sandi Akan Kedaluwarsa Dalam</label>
                    </div>
                    <div class="form-control-col">
                        <div class="custom-select-container" id="select-pwd-lifetime">
                            <input type="hidden" name="password_lifetime_type" id="input-pwd-lifetime" value="<?= esc($settings['password_lifetime_type'] ?? '1') ?>">
                            <div class="custom-select-trigger" onclick="toggleDropdown('select-pwd-lifetime')">
                                <span class="custom-select-text">1 bulan</span>
                                <i class="bi bi-chevron-down custom-select-chevron"></i>
                            </div>
                            <div class="custom-select-options">
                                <div class="custom-select-option" data-value="1" onclick="selectPasswordLifetimeOption('select-pwd-lifetime', '1', '1 bulan')">1 bulan</div>
                                <div class="custom-select-option" data-value="2" onclick="selectPasswordLifetimeOption('select-pwd-lifetime', '2', '2 bulan')">2 bulan</div>
                                <div class="custom-select-option" data-value="3" onclick="selectPasswordLifetimeOption('select-pwd-lifetime', '3', '3 bulan')">3 bulan</div>
                                <div class="custom-select-option" data-value="6" onclick="selectPasswordLifetimeOption('select-pwd-lifetime', '6', '6 bulan')">6 bulan</div>
                                <div class="custom-select-option" data-value="12" onclick="selectPasswordLifetimeOption('select-pwd-lifetime', '12', '12 bulan')">12 bulan</div>
                                <div class="custom-select-option" data-value="Kustom" onclick="selectPasswordLifetimeOption('select-pwd-lifetime', 'Kustom', 'Kustom')">Kustom</div>
                            </div>
                        </div>

                        <!-- Input Kustom jika memilih Kustom -->
                        <div id="custom-pwd-time-wrapper" style="margin-top: 10px; display: <?= ($settings['password_lifetime_type'] ?? '') == 'Kustom' ? 'block' : 'none' ?>;">
                            <div class="suffix-input-wrapper">
                                <input type="number" min="1" max="120" name="password_lifetime_custom" class="form-control-timeout" <?= ($settings['password_lifetime_type'] ?? '') == 'Kustom' ? 'required' : '' ?> value="<?= esc($settings['password_lifetime_custom'] ?? '1') ?>">
                                <span class="input-suffix">bln</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hari hingga Peringatan Sebelum Kedaluwarsa Sandi -->
                <div class="form-row" style="margin-bottom: 12px;">
                    <div class="form-label-col">
                        <span class="info-tooltip-trigger" data-tooltip="Jumlah hari sebelum kata sandi kedaluwarsa di mana sistem akan mulai memberikan peringatan kepada pengguna.">
                            <i class="bi bi-info-circle"></i>
                        </span>
                        <label><span style="color: #ef4444; margin-right: 2px;">*</span>Hari hingga Peringatan Sebelum Kedaluwarsa Sandi</label>
                    </div>
                    <div class="form-control-col">
                        <div class="suffix-input-wrapper">
                            <input type="number" min="0" max="365" name="password_expiration_warning_days" class="form-control-timeout" required value="<?= esc($settings['password_expiration_warning_days'] ?? '14') ?>">
                            <span class="input-suffix">h</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Web Kedaluwarsa Jika Tidak Ada Tindakan Dalam -->
            <div class="form-row">
                <div class="form-label-col">
                    <label>Login Web Kedaluwarsa Jika Tidak Ada Tindakan Dalam <span style="color: #ef4444; margin-left: 2px;">*</span></label>
                </div>
                <div class="form-control-col">
                    <div class="suffix-input-wrapper">
                        <input type="number" min="1" max="1440" name="web_session_timeout" class="form-control-timeout" required value="<?= esc($settings['web_session_timeout'] ?? '30') ?>">
                        <span class="input-suffix">mnt</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr style="border: 0; height: 1px; background: #e5e7eb; margin: 35px 0;">

            <!-- Buttons -->
            <div class="form-row">
                <div></div> <!-- Spacer -->
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-primary" id="saveSettingsBtn" style="background: #3b82f6; color: white; padding: 10px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: opacity 0.2s;">
                        <i class="bi bi-check-lg"></i> Simpan Kebijakan
                    </button>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline" style="border: 1px solid #d1d5db; color: #4b5563; background: white; padding: 10px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 6px; justify-content: center; text-align: center;">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>
<?php endif; ?>

<!-- Modals for Users and Departments -->

<!-- Modal User -->
<div id="user-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center">
    <div style="background:white;width:600px;border-radius:12px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);padding:25px;max-height:90vh;overflow-y:auto">
        <form action="<?= base_url('admin/users/save') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="u-m-id">
            <div id="u-m-title" style="font-size:18px;font-weight:bold;margin-bottom:20px;color:#111827">Tambah User</div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px">
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Nama Lengkap</label>
                    <input type="text" name="name" id="u-m-name" class="form-control" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;box-sizing:border-box">
                </div>
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Email</label>
                    <input type="email" name="email" id="u-m-email" class="form-control" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;box-sizing:border-box">
                </div>
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Password</label>
                <div style="position:relative">
                    <input type="password" name="password" id="u-m-pw" class="form-control" placeholder="Kosongkan jika tidak diganti / biarkan untuk default" style="width:100%;padding:10px 40px 10px 10px;border-radius:8px;border:1px solid #d1d5db;box-sizing:border-box">
                    <button type="button" onclick="togglePasswordVisibility()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-eye" id="u-m-pw-icon" style="font-size:1.1rem"></i>
                    </button>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px">
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Role</label>
                    <select name="role_id" id="u-m-role" class="form-select" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                        <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>"><?= esc($r['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Departemen</label>
                    <select name="dept_id" id="u-m-dept" class="form-select" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                        <?php foreach ($depts as $d): ?><option value="<?= $d['id'] ?>"><?= esc($d['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:25px">
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Jenis Kelamin</label>
                    <select name="gender" id="u-m-gender" class="form-select" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                        <option value="L">Laki-laki</option><option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">No. Telepon</label>
                    <input type="text" name="phone" id="u-m-phone" class="form-control" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;box-sizing:border-box">
                </div>
            </div>

            <div id="u-perm-section" style="margin-bottom:20px">
                <label style="display:block;margin-bottom:8px;font-size:13px;font-weight:600;border-bottom:1px solid #e5e7eb;padding-bottom:5px">
                    Izin Khusus User <span style="font-size:11px;color:#6b7280;font-weight:400">(opsional — override izin dari role)</span>
                </label>
                <div style="background:#f9fafb;padding:12px;border-radius:8px;border:1px solid #f3f4f6;display:grid;grid-template-columns:1fr 1fr;gap:6px">
                    <?php foreach ($availablePermissions as $pName => $pDesc): ?>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
                            <input type="checkbox" name="permissions[]" value="<?= esc($pName) ?>" class="cb-user-perm" style="transform:scale(1.1)">
                            <?= esc($pDesc) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top:6px;font-size:11px;color:#9ca3af">Jika tidak ada yang dicentang, user mengikuti izin dari role-nya.</div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" style="background:white;color:#6b7280;border:1px solid #d1d5db;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer" onclick="closeUserModal()">Batal</button>
                <button type="submit" style="background:#3b82f6;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah/Edit Departemen -->
<div id="deptModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center">
    <div style="background:white;border-radius:16px;padding:30px;width:460px;max-width:90vw;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
        <div style="font-size:18px;font-weight:700;margin-bottom:20px;display:flex;justify-content:space-between">
            <span id="modalTitle">Tambah Departemen</span>
            <button onclick="closeDeptModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#9ca3af">&times;</button>
        </div>
        <form action="<?= base_url('admin/departments/save') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="deptId">
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Nama Departemen *</label>
                <input type="text" name="name" id="deptName" required placeholder="Contoh: IT Support" style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;box-sizing:border-box">
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" onclick="closeDeptModal()" style="background:white;color:#6b7280;border:1px solid #d1d5db;padding:10px 20px;border-radius:8px;font-weight:600;cursor:pointer">Batal</button>
                <button type="submit" style="background:#3b82f6;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:600;cursor:pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi dropdown custom
        document.querySelectorAll('.custom-select-container').forEach(container => {
            const input = container.querySelector('input[type="hidden"]');
            const triggerText = container.querySelector('.custom-select-text');
            const val = input.value;
            const options = container.querySelectorAll('.custom-select-option');
            
            options.forEach(opt => {
                if (opt.getAttribute('data-value') === val) {
                    opt.classList.add('selected');
                    triggerText.textContent = opt.textContent;
                }
            });
        });

        // Tutup dropdown jika klik di luar area
        document.addEventListener('click', function (e) {
            document.querySelectorAll('.custom-select-container').forEach(container => {
                if (!container.contains(e.target)) {
                    container.classList.remove('open');
                }
            });
        });

        // Validasi form saat submit
        const form = document.getElementById('settingsForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                const btn = document.getElementById('saveSettingsBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
                }
            });
        }

        // Toggle detail masa berlaku password
        const togglePwd = document.getElementById('enable_max_password_lifetime');
        const detailsPwd = document.getElementById('password-lifetime-details');
        if (togglePwd && detailsPwd) {
            togglePwd.addEventListener('change', function () {
                if (this.checked) {
                    detailsPwd.style.display = 'block';
                } else {
                    detailsPwd.style.display = 'none';
                }
            });
        }
    });

    function toggleDropdown(id) {
        const container = document.getElementById(id);
        // Tutup dropdown lain yang sedang terbuka
        document.querySelectorAll('.custom-select-container').forEach(c => {
            if (c.id !== id) c.classList.remove('open');
        });
        container.classList.toggle('open');
    }

    function selectOption(containerId, value, text) {
        const container = document.getElementById(containerId);
        const input = container.querySelector('input[type="hidden"]');
        const triggerText = container.querySelector('.custom-select-text');
        
        input.value = value;
        triggerText.textContent = text;
        
        container.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('selected');
            if (opt.getAttribute('data-value') === value) {
                opt.classList.add('selected');
            }
        });
        
        container.classList.remove('open');
    }

    function selectPasswordLifetimeOption(containerId, value, text) {
        selectOption(containerId, value, text);
        const customWrapper = document.getElementById('custom-pwd-time-wrapper');
        if (customWrapper) {
            if (value === 'Kustom') {
                customWrapper.style.display = 'block';
                customWrapper.querySelector('input').setAttribute('required', 'required');
            } else {
                customWrapper.style.display = 'none';
                customWrapper.querySelector('input').removeAttribute('required');
            }
        }
    }

    // Tab Switching Logic
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
            btn.style.color = '#6b7280';
            btn.style.borderBottomColor = 'transparent';
        });
        
        const targetTab = document.getElementById('tab-' + tabId);
        if (targetTab) {
            targetTab.style.display = 'block';
        }
        
        const activeBtn = document.getElementById('btn-tab-' + tabId);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.style.color = 'var(--primary)';
            activeBtn.style.borderBottomColor = 'var(--primary)';
        }
    }

    // Modal Role Logic
    function openRoleModal(r = null) {
        document.getElementById('m-id').value = r ? r.id : '';
        document.getElementById('m-code').value = r ? r.code : '';
        document.getElementById('m-name').value = r ? r.name : '';
        document.getElementById('m-title').innerText = r ? 'Edit Role' : 'Tambah Role';

        // Set flags
        document.getElementById('m-is-staff').checked = r ? (r.is_staff == 1 || r.is_staff === '1') : false;
        document.getElementById('m-is-technician').checked = r ? (r.is_technician == 1 || r.is_technician === '1') : false;

        // Reset checkboxes
        const checkboxes = document.querySelectorAll('.cb-perm');
        checkboxes.forEach(cb => cb.checked = false);

        // Set checkboxes if editing
        if (r && r.permissions) {
            try {
                const perms = JSON.parse(r.permissions);
                if (Array.isArray(perms)) {
                    checkboxes.forEach(cb => {
                        if (perms.includes(cb.value)) cb.checked = true;
                    });
                }
            } catch (e) {}
        }

        if (r && r.id == 1) {
            document.getElementById('perm-section').style.display = 'none';
            document.getElementById('admin-warn').style.display = 'block';
        } else {
            document.getElementById('perm-section').style.display = 'block';
            document.getElementById('admin-warn').style.display = 'none';
        }

        document.getElementById('role-modal').style.display = 'flex';
    }
    
    function closeRoleModal() { 
        document.getElementById('role-modal').style.display = 'none'; 
    }

    // Modal User Logic
    function openUserModal(u = null) {
        document.getElementById('u-m-id').value = u ? u.id : '';
        document.getElementById('u-m-name').value = u ? u.name : '';
        document.getElementById('u-m-email').value = u ? u.email : '';
        document.getElementById('u-m-role').value = u ? u.role_id : '3';
        document.getElementById('u-m-dept').value = u ? u.dept_id : '1';
        document.getElementById('u-m-gender').value = u ? u.gender : 'L';
        document.getElementById('u-m-phone').value = u ? u.phone : '';
        document.getElementById('u-m-title').innerText = u ? 'Edit User' : 'Tambah User';

        // Reset & set permissions checkboxes
        const cbs = document.querySelectorAll('.cb-user-perm');
        cbs.forEach(cb => cb.checked = false);
        if (u && u.permissions) {
            try {
                const perms = JSON.parse(u.permissions);
                if (Array.isArray(perms)) cbs.forEach(cb => { if (perms.includes(cb.value)) cb.checked = true; });
            } catch(e) {}
        }

        // Reset password visibility
        const pwInput = document.getElementById('u-m-pw');
        pwInput.type = 'password';
        pwInput.value = '';
        const pwIcon = document.getElementById('u-m-pw-icon');
        pwIcon.classList.remove('bi-eye-slash');
        pwIcon.classList.add('bi-eye');
        
        document.getElementById('user-modal').style.display = 'flex';
    }

    function closeUserModal() { 
        document.getElementById('user-modal').style.display = 'none'; 
    }

    function togglePasswordVisibility() {
        const pwInput = document.getElementById('u-m-pw');
        const pwIcon = document.getElementById('u-m-pw-icon');
        if (pwInput.type === 'password') {
            pwInput.type = 'text';
            pwIcon.classList.remove('bi-eye');
            pwIcon.classList.add('bi-eye-slash');
        } else {
            pwInput.type = 'password';
            pwIcon.classList.remove('bi-eye-slash');
            pwIcon.classList.add('bi-eye');
        }
    }

    // Modal Departemen Logic
    function openDeptModal(d = null) {
        document.getElementById('deptModal').style.display = 'flex';
        if (d) {
            document.getElementById('modalTitle').textContent = 'Edit Departemen';
            document.getElementById('deptId').value = d.id;
            document.getElementById('deptName').value = d.name;
        } else {
            document.getElementById('modalTitle').textContent = 'Tambah Departemen';
            document.getElementById('deptId').value = '';
            document.getElementById('deptName').value = '';
        }
    }

    function closeDeptModal() {
        document.getElementById('deptModal').style.display = 'none';
    }

    // Close Modals on Outer Click
    window.onclick = function(e) {
        if (e.target.id === 'deptModal') {
            closeDeptModal();
        } else if (e.target.id === 'user-modal') {
            closeUserModal();
        } else if (e.target.id === 'role-modal') {
            closeRoleModal();
        }
    };
</script>

<?= $this->endSection() ?>
