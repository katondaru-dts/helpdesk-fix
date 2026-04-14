<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-header-title">Kelola Kategori</div>
        <div class="page-header-sub"><?= count($categories) ?> kategori tersedia</div>
    </div>
    <button class="btn btn-primary" onclick="openCatModal()"><i class="bi bi-tag"></i> Tambah Kategori</button>
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
                <th style="padding:15px 20px">Nama Kategori</th>
                <th style="padding:15px">Deskripsi</th>
                <th style="padding:15px;text-align:center">Tiket Terkait</th>
                <th style="padding:15px">Status</th>
                <th style="padding:15px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $c): ?>
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:15px 20px;font-weight:600;color:#111827"><?= esc($c['name']) ?></td>
                    <td style="padding:15px;font-size:14px;color:#374151"><?= esc($c['description']) ?></td>
                    <td style="padding:15px;text-align:center">
                        <span style="background:#e0e7ff;color:#3730a3;padding:4px 10px;border-radius:999px;font-weight:600;font-size:13px"><?= $c['ticket_count'] ?></span>
                    </td>
                    <td style="padding:15px">
                        <?php if ($c['is_active']): ?>
                            <span class="badge" style="background:#d1fae5;color:#065f46;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:600">Aktif</span>
                        <?php else: ?>
                            <span class="badge" style="background:#fee2e2;color:#991b1b;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:600">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:15px;display:flex;gap:5px">
                        <button style="background:white;color:#3b82f6;border:1px solid #3b82f6;padding:4px 8px;border-radius:6px;cursor:pointer" onclick='openCatModal(<?= json_encode($c) ?>)'><i class="bi bi-pencil"></i></button>
                        
                        <form action="<?= base_url('admin/categories/toggle_status') ?>" method="POST" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <input type="hidden" name="current" value="<?= $c['is_active'] ?>">
                            <?php if ($c['is_active']): ?>
                                <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Nonaktifkan</button>
                            <?php else: ?>
                                <button type="submit" style="background:#d1fae5;color:#065f46;border:none;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">Aktifkan</button>
                            <?php endif; ?>
                        </form>

                        <?php if ($c['ticket_count'] == 0): ?>
                            <form action="<?= base_url('admin/categories/delete') ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus kategori ini?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
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
<div id="cat-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center">
    <div style="background:white;width:500px;border-radius:12px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);padding:25px">
        <form action="<?= base_url('admin/categories/save') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="m-id">
            <div id="m-title" style="font-size:18px;font-weight:bold;margin-bottom:20px;color:#111827">Tambah Kategori</div>
            
            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Nama Kategori <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" id="m-name" class="form-control" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
            </div>

            <div style="margin-bottom:25px">
                <label style="display:block;margin-bottom:5px;font-size:13px;font-weight:600">Deskripsi</label>
                <textarea name="description" id="m-desc" class="form-control" rows="3" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db"></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" style="background:white;color:#6b7280;border:1px solid #d1d5db;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer" onclick="closeCatModal()">Batal</button>
                <button type="submit" style="background:#3b82f6;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCatModal(c = null) {
    document.getElementById('m-id').value = c ? c.id : '';
    document.getElementById('m-name').value = c ? c.name : '';
    document.getElementById('m-desc').value = c ? c.description : '';
    document.getElementById('m-title').innerText = c ? 'Edit Kategori' : 'Tambah Kategori';
    document.getElementById('cat-modal').style.display = 'flex';
}
function closeCatModal() { document.getElementById('cat-modal').style.display = 'none'; }
</script>

<?= $this->endSection() ?>
