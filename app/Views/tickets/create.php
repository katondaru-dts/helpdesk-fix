<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-header-title">Buat Tiket Baru</div>
        <div class="page-header-sub">Laporkan gangguan atau masalah yang Anda hadapi</div>
    </div>
    <a href="<?= base_url('tickets') ?>" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<div class="grid" style="display:grid;grid-template-columns:1fr 340px;gap:20px">
    <div class="card" style="background:white;border-radius:12px;padding:30px">
        <?php if (session()->getFlashdata('error')): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:15px"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <form action="<?= base_url('tickets/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Judul Gangguan *</label>
                <input type="text" name="title" required maxlength="200" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;" placeholder="Contoh: Printer ruang finance tidak bisa print">
            </div>
            <?php $isStaff = in_array(session()->get('role_id'), [1, 2, 4]); ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
                <div style="<?= $isStaff ? '' : 'grid-column:span 2' ?>">
                    <label style="display:block;margin-bottom:8px;font-weight:600">Kategori *</label>
                    <select name="cat_id" required style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($isStaff): ?>
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:600">Prioritas</label>
                    <select name="priority" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;">
                        <option value="LOW">Low</option>
                        <option value="MEDIUM" selected>Medium</option>
                        <option value="HIGH">High</option>
                        <option value="URGENT">Urgent</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Lokasi Gangguan</label>
                <input type="text" name="location" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;" placeholder="Contoh: Lantai 2 - Ruang Finance">
            </div>
            <div style="margin-bottom:25px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Deskripsi Lengkap *</label>
                <textarea name="description" rows="6" required style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;" placeholder="Jelaskan masalah secara rinci..."></textarea>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" style="background:#3b82f6;color:white;padding:12px 25px;border:none;border-radius:8px;font-weight:bold;cursor:pointer"><i class="bi bi-send-fill"></i> Kirim Laporan</button>
                <button type="reset" style="background:white;color:#6b7280;padding:12px 25px;border:1px solid #d1d5db;border-radius:8px;font-weight:bold;cursor:pointer">Reset</button>
            </div>
        </form>
    </div>
    <div>
        <div class="card" style="background:white;border-radius:12px;padding:20px">
            <div style="font-weight:bold;margin-bottom:15px"><i class="bi bi-lightbulb" style="color:#f59e0b"></i> Tips Pelaporan</div>
            <ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:12px">
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-check-circle" style="color:#3b82f6"></i> Pilih kategori yang tepat agar ditangani lebih cepat.</li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563"><i class="bi bi-geo-alt" style="color:#3b82f6"></i> Cantumkan lokasi fisik perangkat yang bermasalah.</li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>