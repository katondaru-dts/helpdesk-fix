<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <div class="page-header-title">Kelola Departemen</div>
        <div class="page-header-sub"><?= count($departments) ?> departemen tersedia</div>
    </div>
    <button class="btn btn-primary" onclick="openDeptModal()"><i class="bi bi-plus-circle"></i> Tambah Departemen</button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="display:flex;align-items:center;gap:10px;background:#d1fae5;color:#065f46;padding:15px;border-radius:8px;margin-bottom:16px">
        <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php elseif (session()->getFlashdata('error')): ?>
    <div style="display:flex;align-items:center;gap:10px;background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;margin-bottom:16px">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);overflow:hidden">
    <table class="table" style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="border-bottom:2px solid #f3f4f6;background:#f9fafb;color:#4b5563;font-size:13px">
                <th style="padding:15px 20px">Nama Departemen</th>
                <th style="padding:15px;text-align:center">User Terdaftar</th>
                <th style="padding:15px">Status</th>
                <th style="padding:15px">Aksi</th>
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
                <td style="padding:15px;display:flex;gap:5px">
                    <button style="background:white;color:#3b82f6;border:1px solid #3b82f6;padding:4px 8px;border-radius:6px;cursor:pointer" onclick='openDeptModal(<?= json_encode($d) ?>)'><i class="bi bi-pencil"></i></button>
                    <?php if ($d['id'] != 1): ?>
                        <form action="<?= base_url('admin/departments/toggle_status') ?>" method="POST" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                            <input type="hidden" name="current" value="<?= $d['is_active'] ?>">
                            <?php if ($d['is_active']): ?>
                                <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Nonaktifkan</button>
                            <?php else: ?>
                                <button type="submit" style="background:#d1fae5;color:#065f46;border:none;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Aktifkan</button>
                            <?php endif; ?>
                        </form>
                        <form action="<?= base_url('admin/departments/delete') ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus departemen ini?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                            <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer"><i class="bi bi-trash"></i></button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah/Edit Departemen -->
<div id="deptModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;display:none;align-items:center;justify-content:center">
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
function openDeptModal(d) {
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
window.onclick = function(e) {
    if (e.target.id === 'deptModal') closeDeptModal();
};
</script>

<?= $this->endSection() ?>
