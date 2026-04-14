<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-header-title">Kelola Role & Izin</div>
        <div class="page-header-sub"><?= count($roles) ?> role sistem terkonfigurasi</div>
    </div>
    <button class="btn btn-primary" onclick="openRoleModal()"><i class="bi bi-shield-plus"></i> Tambah Role</button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-4" style="display:flex;align-items:center;gap:10px;background:#d1fae5;color:#065f46;padding:15px;border-radius:8px;">
        <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-4" style="display:flex;align-items:center;gap:10px;background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);overflow:hidden">
    <table class="table" style="width:100%;border-collapse:collapse;text-align:left">
        <thead>
            <tr style="border-bottom:2px solid #f3f4f6;background:#f9fafb;color:#4b5563;font-size:13px">
                <th style="padding:15px 20px">Kode Role</th>
                <th style="padding:15px">Nama Role</th>
                <th style="padding:15px">Izin Akses</th>
                <th style="padding:15px;text-align:center">User</th>
                <th style="padding:15px">Aksi</th>
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
                    <td style="padding:15px;display:flex;gap:5px">
                        <button style="background:white;color:#3b82f6;border:1px solid #3b82f6;padding:4px 8px;border-radius:6px;cursor:pointer" onclick='openRoleModal(<?= json_encode($r) ?>)'><i class="bi bi-pencil"></i></button>
                        
                        <?php if ($r['user_count'] == 0 && $r['id'] != 1): ?>
                            <form action="<?= base_url('admin/roles/delete') ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus role ini secara permanen?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
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
<div id="role-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center">
    <div style="background:white;width:600px;border-radius:12px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);padding:25px;max-height:90vh;overflow-y:auto">
        <form action="<?= base_url('admin/roles/save') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="m-id">
            <div id="m-title" style="font-size:18px;font-weight:bold;margin-bottom:20px;color:#111827">Tambah Role</div>
            
            <div style="display:grid;grid-template-columns:1fr 2fr;gap:15px;margin-bottom:20px">
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Kode Role <span style="color:#ef4444">*</span></label>
                    <input type="text" name="code" id="m-code" class="form-control" required placeholder="Cth: STAFF" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;text-transform:uppercase">
                </div>
                <div>
                    <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Nama Role <span style="color:#ef4444">*</span></label>
                    <input type="text" name="name" id="m-name" class="form-control" required placeholder="Cth: Helpdesk Staff" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                </div>
            </div>

            <div id="perm-section">
                <label style="display:block;margin-bottom:10px;font-size:13px;font-weight:600;border-bottom:1px solid #e5e7eb;padding-bottom:5px">Hak Akses / Izin (Pilih yang sesuai)</label>
                <div style="display:grid;grid-template-columns:1fr;gap:8px;margin-bottom:25px;background:#f9fafb;padding:15px;border-radius:8px;border:1px solid #f3f4f6">
                    <?php foreach ($availablePermissions as $pName => $pDesc): ?>
                        <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer">
                            <input type="checkbox" name="permissions[]" value="<?= esc($pName) ?>" class="cb-perm" style="margin-top:3px;transform:scale(1.1)">
                            <div>
                                <div style="font-weight:600;font-size:13px;color:#374151"><?= esc($pDesc) ?></div>
                                <div style="font-size:12px;color:#6b7280"><?= esc($pName) ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="admin-warn" style="display:none;margin-bottom:20px;padding:15px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;color:#92400e;font-size:13px">
                <i class="bi bi-exclamation-circle-fill"></i> Role ini adalah Administrator Utama. Hak akses diatur otomatis oleh sistem dan tidak dapat diubah di sini.
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" style="background:white;color:#6b7280;border:1px solid #d1d5db;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer" onclick="closeRoleModal()">Batal</button>
                <button type="submit" style="background:#3b82f6;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer">Simpan Role</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRoleModal(r = null) {
    document.getElementById('m-id').value = r ? r.id : '';
    document.getElementById('m-code').value = r ? r.code : '';
    document.getElementById('m-name').value = r ? r.name : '';
    document.getElementById('m-title').innerText = r ? 'Edit Role' : 'Tambah Role';
    
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
function closeRoleModal() { document.getElementById('role-modal').style.display = 'none'; }
</script>

<?= $this->endSection() ?>
