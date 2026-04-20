<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <div class="page-header-title">Profil Saya</div>
        <div class="page-header-sub">Kelola informasi akun Anda</div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="display:flex;align-items:center;gap:10px;background:#d1fae5;color:#065f46;padding:15px;border-radius:8px;margin-bottom:20px">
        <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php elseif (session()->getFlashdata('error')): ?>
    <div style="display:flex;align-items:center;gap:10px;background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;margin-bottom:20px">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="grid g2">
    <!-- Profile Info -->
    <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:30px">
        <div style="display:flex;align-items:center;gap:20px;margin-bottom:25px;padding-bottom:20px;border-bottom:1px solid #f3f4f6">
            <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;color:white;flex-shrink:0">
                <?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?>
            </div>
            <div>
                <div style="font-size:20px;font-weight:700;color:#111827"><?= esc($user['name']) ?></div>
                <div style="font-size:13px;color:#6b7280"><?= esc($user['email']) ?></div>
                <div style="margin-top:5px">
                    <span style="background:#eff6ff;color:#3b82f6;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600"><?= esc($user['role_name'] ?? '-') ?></span>
                </div>
            </div>
        </div>

        <form action="<?= base_url('profile/update') ?>" method="POST">
            <?= csrf_field() ?>
            <div style="display:grid;gap:18px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama Lengkap</label>
                    <input type="text" name="name" value="<?= esc($user['name']) ?>" required style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Email</label>
                    <input type="email" name="email" value="<?= esc($user['email']) ?>" required style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:<?= session()->get('role_id') == 1 ? 'white' : '#f3f4f6' ?>;box-sizing:border-box" <?= session()->get('role_id') != 1 ? 'readonly' : '' ?>>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">No. Telepon</label>
                    <input type="text" name="phone" value="<?= esc($user['phone'] ?? '') ?>" style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jenis Kelamin</label>
                    <select name="gender" style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:white;box-sizing:border-box">
                        <option value="L" <?= ($user['gender'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($user['gender'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Departemen</label>
                    <select name="dept_id" style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:<?= session()->get('role_id') == 1 ? 'white' : '#f3f4f6' ?>;box-sizing:border-box" <?= session()->get('role_id') != 1 ? 'disabled' : '' ?>>
                        <?php if (session()->get('role_id') == 1): ?>
                            <option value="">-- Pilih Departemen --</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $user['dept_id'] == $d['id'] ? 'selected' : '' ?>><?= esc($d['name']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?= $user['dept_id'] ?>" selected><?= esc($user['dept_name'] ?? '-') ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" style="background:#3b82f6;color:white;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;width:100%">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <div style="display:grid;gap:20px">
        <!-- Notification Settings -->
        <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:30px;height:fit-content">
            <div style="font-size:16px;font-weight:700;color:#111827;margin-bottom:20px;padding-bottom:15px;border-bottom:1px solid #f3f4f6">
                <i class="bi bi-bell" style="color:#f59e0b"></i> Pengaturan Notifikasi
            </div>
            <form action="<?= base_url('profile/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="name" value="<?= esc($user['name']) ?>">
                <input type="hidden" name="email" value="<?= esc($user['email']) ?>">
                <div style="display:grid;gap:18px">
                    <div style="display:flex;align-items:center;gap:10px;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #f3f4f6">
                        <input type="checkbox" name="notif_sound_enabled" id="notif_sound_enabled" value="1" <?= ($user['notif_sound_enabled'] ?? 1) ? 'checked' : '' ?> style="width:18px;height:18px;cursor:pointer">
                        <label for="notif_sound_enabled" style="font-size:14px;font-weight:600;color:#374151;cursor:pointer">Aktifkan Suara Notifikasi</label>
                    </div>
                    
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jenis Suara</label>
                        <div style="display:flex;gap:10px">
                            <select id="sound_type_select" name="notif_sound_type" style="flex:1;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:white;box-sizing:border-box">
                                <option value="default" <?= ($user['notif_sound_type'] ?? 'default') == 'default' ? 'selected' : '' ?>>Default (Digital)</option>
                                <option value="bell" <?= ($user['notif_sound_type'] ?? '') == 'bell' ? 'selected' : '' ?>>Bell (Classic)</option>
                                <option value="beep" <?= ($user['notif_sound_type'] ?? '') == 'beep' ? 'selected' : '' ?>>Beep (Short)</option>
                                <option value="chime" <?= ($user['notif_sound_type'] ?? '') == 'chime' ? 'selected' : '' ?>>Chime (Elegant)</option>
                            </select>
                            <button type="button" onclick="if(window.playBeepSound) { window.playBeepSound(document.getElementById('sound_type_select').value); } else { console.log('not found'); }" style="background:#e5e7eb;color:#374151;border:none;padding:10px 16px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
                                <i class="bi bi-play-circle-fill" style="color:#059669"></i> Tes
                            </button>
                        </div>
                    </div>

                    <button type="submit" style="background:#3b82f6;color:white;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;width:100%">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:30px;height:fit-content">
            <div style="font-size:16px;font-weight:700;color:#111827;margin-bottom:20px;padding-bottom:15px;border-bottom:1px solid #f3f4f6">
                <i class="bi bi-shield-lock" style="color:#3b82f6"></i> Ganti Password
            </div>
            <form action="<?= base_url('profile/change-password') ?>" method="POST">
                <?= csrf_field() ?>
                <div style="display:grid;gap:18px">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Password Lama</label>
                        <div style="position:relative">
                            <input type="password" name="old_password" required placeholder="Masukkan password lama" style="width:100%;padding:10px 40px 10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                            <button type="button" class="toggle-password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Password Baru</label>
                        <div style="position:relative">
                            <input type="password" name="new_password" required placeholder="Min. 8 karakter" style="width:100%;padding:10px 40px 10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            <button type="button" class="toggle-password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Konfirmasi Password</label>
                        <div style="position:relative">
                            <input type="password" name="confirm_password" required placeholder="Ulangi password baru" style="width:100%;padding:10px 40px 10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                            <button type="button" class="toggle-password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" style="background:#10b981;color:white;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;width:100%">
                        <i class="bi bi-lock"></i> Ganti Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
});
</script>

<?= $this->endSection() ?>
