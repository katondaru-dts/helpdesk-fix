<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-header-title">Kelola User</div>
        <div class="page-header-sub"><?= count($users) ?> user terdaftar</div>
    </div>
    <button class="btn btn-primary" onclick="openUserModal()"><i class="bi bi-person-plus"></i> Tambah User</button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-4" style="display:flex;align-items:center;gap:10px;background:#d1fae5;color:#065f46;padding:15px;border-radius:8px;">
        <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-4" style="display:flex;align-items:center;gap:10px;background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;">
        <i class="bi bi-shield-lock-fill"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="card mb-4" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:15px 20px;">
    <form action="<?= base_url('admin/users') ?>" method="GET" style="display:flex; gap:10px; align-items:center">
        <div style="flex:1; position:relative">
            <i class="bi bi-search" style="position:absolute;left:10px;top:10px;color:#9ca3af"></i>
            <input type="text" name="search" value="<?= esc($search) ?>" class="form-control" placeholder="Cari nama atau email..." style="width:100%;padding:8px 8px 8px 35px;border-radius:8px;border:1px solid #d1d5db">
        </div>
        <select name="f-role" class="form-select" style="width:160px;padding:8px;border-radius:8px;border:1px solid #d1d5db">
            <option value="">Semua Role</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $f_role == $r['id'] ? 'selected' : '' ?>><?= esc($r['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary" style="background:#3b82f6;color:white;padding:8px 15px;border:none;border-radius:8px;font-weight:bold;cursor:pointer">Cari</button>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline" style="background:white;color:#6b7280;padding:8px 15px;border:1px solid #d1d5db;border-radius:8px;font-weight:bold;cursor:pointer"><i class="bi bi-x-circle"></i></a>
    </form>
</div>

<div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);overflow:hidden">
    <table class="table" style="width:100%;border-collapse:collapse;text-align:left">
        <thead>
            <tr style="border-bottom:2px solid #f3f4f6;background:#f9fafb;color:#4b5563;font-size:13px">
                <th style="padding:15px 20px">User</th>
                <th style="padding:15px">Email</th>
                <th style="padding:15px">Role</th>
                <th style="padding:15px">Departemen</th>
                <th style="padding:15px">Status</th>
                <th style="padding:15px">Aksi</th>
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
                    <td style="padding:15px;display:flex;gap:5px">
                        <button style="background:white;color:#3b82f6;border:1px solid #3b82f6;padding:4px 8px;border-radius:6px;cursor:pointer" onclick='openUserModal(<?= json_encode($u) ?>)'><i class="bi bi-pencil"></i></button>
                        
                        <?php if ($u['role_id'] != 1): ?>
                            <form action="<?= base_url('admin/users/toggle_status') ?>" method="POST" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="current" value="<?= $u['is_active'] ?>">
                                <?php if ($u['is_active']): ?>
                                    <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Nonaktifkan</button>
                                <?php else: ?>
                                    <button type="submit" style="background:#d1fae5;color:#065f46;border:none;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Aktifkan</button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>

                        <?php if ($u['id'] != session()->get('id')): ?>
                            <form action="<?= base_url('admin/users/delete') ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" style="background:#ef4444;color:white;border:none;padding:5px 8px;border-radius:6px;cursor:pointer"><i class="bi bi-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="user-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center">
    <div style="background:white;width:600px;border-radius:12px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);padding:25px">
        <form action="<?= base_url('admin/users/save') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="m-id">
            <div id="m-title" style="font-size:18px;font-weight:bold;margin-bottom:20px;color:#111827">Tambah User</div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px">
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Nama Lengkap</label>
                    <input type="text" name="name" id="m-name" class="form-control" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                </div>
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Email</label>
                    <input type="email" name="email" id="m-email" class="form-control" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                </div>
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Password</label>
                <input type="password" name="password" id="m-pw" class="form-control" placeholder="Kosongkan jika tidak diganti / biarkan untuk default" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px">
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Role</label>
                    <select name="role_id" id="m-role" class="form-select" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                        <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>"><?= esc($r['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Departemen</label>
                    <select name="dept_id" id="m-dept" class="form-select" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                        <?php foreach ($depts as $d): ?><option value="<?= $d['id'] ?>"><?= esc($d['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:25px">
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Jenis Kelamin</label>
                    <select name="gender" id="m-gender" class="form-select" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                        <option value="L">Laki-laki</option><option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">No. Telepon</label>
                    <input type="text" name="phone" id="m-phone" class="form-control" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" style="background:white;color:#6b7280;border:1px solid #d1d5db;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer" onclick="closeUserModal()">Batal</button>
                <button type="submit" style="background:#3b82f6;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUserModal(u = null) {
    document.getElementById('m-id').value = u ? u.id : '';
    document.getElementById('m-name').value = u ? u.name : '';
    document.getElementById('m-email').value = u ? u.email : '';
    document.getElementById('m-email').readOnly = false;
    document.getElementById('m-role').value = u ? u.role_id : '3';
    document.getElementById('m-dept').value = u ? u.dept_id : '1';
    document.getElementById('m-gender').value = u ? u.gender : 'L';
    document.getElementById('m-phone').value = u ? u.phone : '';
    document.getElementById('m-title').innerText = u ? 'Edit User' : 'Tambah User';
    document.getElementById('user-modal').style.display = 'flex';
}
function closeUserModal() { document.getElementById('user-modal').style.display = 'none'; }
</script>

<?= $this->endSection() ?>
