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
    <!-- Form -->
    <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:30px">
        <div style="font-weight:bold;margin-bottom:20px;font-size:18px;display:flex;align-items:center;gap:10px">
            <i class="bi bi-pencil-fill" style="color:var(--primary)"></i> Informasi Gangguan
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger mb-4" style="background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;display:flex;align-items:center;gap:10px">
                <i class="bi bi-exclamation-circle-fill"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('tickets/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:20px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Judul Gangguan <span style="color:#ef4444">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: Printer ruang finance tidak bisa print" required maxlength="200" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;">
                <div style="font-size:12px;color:#6b7280;margin-top:5px">Deskripsi singkat masalah (maks. 200 karakter)</div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
                <div class="form-group">
                    <label style="display:block;margin-bottom:8px;font-weight:600">Kategori <span style="color:#ef4444">*</span></label>
                    <select name="cat_id" class="form-select" required style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:block;margin-bottom:8px;font-weight:600">Prioritas</label>
                    <select name="priority" class="form-select" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;">
                        <option value="LOW">Low</option>
                        <option value="MEDIUM" selected>Medium</option>
                        <option value="HIGH">High</option>
                        <option value="URGENT">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:20px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Lokasi Gangguan</label>
                <div style="position:relative">
                    <i class="bi bi-geo-alt" style="position:absolute;left:12px;top:14px;color:#9ca3af"></i>
                    <input type="text" name="location" class="form-control" placeholder="Contoh: Lantai 2 → Ruang Finance" style="width:100%;padding:12px 12px 12px 40px;border-radius:8px;border:1px solid #d1d5db;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom:25px">
                <label style="display:block;margin-bottom:8px;font-weight:600">Deskripsi Lengkap <span style="color:#ef4444">*</span></label>
                <textarea name="description" class="form-control" rows="6" placeholder="Jelaskan masalah secara rinci..." required style="width:100%;padding:12px;border-radius:8px;border:1px solid #d1d5db;"></textarea>
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-primary" style="background:#3b82f6;color:white;padding:12px 25px;border:none;border-radius:8px;font-weight:bold;cursor:pointer">
                    <i class="bi bi-send-fill"></i> Kirim Laporan
                </button>
                <button type="reset" class="btn btn-outline" style="background:white;color:#6b7280;padding:12px 25px;border:1px solid #d1d5db;border-radius:8px;font-weight:bold;cursor:pointer">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Right Column (Tips) -->
    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:20px">
            <div style="font-weight:bold;margin-bottom:15px;font-size:16px"><i class="bi bi-lightbulb" style="color:#f59e0b"></i> Tips Pelaporan</div>
            <ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:12px">
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563">
                    <i class="bi bi-check-circle" style="color:#3b82f6;flex-shrink:0"></i>
                    Pilih kategori yang tepat agar ditangani lebih cepat.
                </li>
                <li style="display:flex;gap:10px;font-size:14px;color:#4b5563">
                    <i class="bi bi-geo-alt" style="color:#3b82f6;flex-shrink:0"></i>
                    Cantumkan lokasi fisik perangkat yang bermasalah.
                </li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
