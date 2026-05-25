<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<!-- Cropper.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>


<div class="page-header">
    <div>
        <div class="page-header-title">Profil Saya</div>
        <div class="page-header-sub">Kelola informasi akun Anda</div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div
        style="display:flex;align-items:center;gap:10px;background:#d1fae5;color:#065f46;padding:15px;border-radius:8px;margin-bottom:20px">
        <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php elseif (session()->getFlashdata('error')): ?>
    <div
        style="display:flex;align-items:center;gap:10px;background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;margin-bottom:20px">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="grid g2">
    <!-- Profile Info -->
    <div class="card" style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:30px">
        <div
            style="display:flex;align-items:center;gap:20px;margin-bottom:25px;padding-bottom:20px;border-bottom:1px solid #f3f4f6">
            <!-- Profile Picture (WhatsApp Style) -->
            <div style="position:relative;width:90px;height:90px;flex-shrink:0">
                <div id="profile-pic-container"
                    style="width:90px;height:90px;border-radius:50%;overflow:hidden;background:#f3f4f6;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 0 10px rgba(0,0,0,0.1);cursor:pointer;position:relative">
                    <?php
                    $picUrl = get_profile_pic_url($user['profile_pic'] ?? '');
                    ?>
                    <img id="profile-pic-img" src="<?= $picUrl ?>" style="width:100%;height:100%;object-fit:cover">

                    <!-- Overlay on Hover -->
                    <div class="pic-overlay"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:flex;flex-direction:column;align-items:center;justify-content:center;color:white;opacity:0;transition:opacity 0.3s">
                        <i class="bi bi-camera-fill" style="font-size:24px"></i>
                        <span style="font-size:10px;font-weight:600;text-transform:uppercase">Ubah</span>
                    </div>
                </div>

                <!-- Hidden Form for Image Upload -->
                <form id="profile-pic-form" action="<?= base_url('profile/update-photo') ?>" method="POST"
                    enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="file" name="profile_pic" id="profile-pic-input" accept="image/*" style="display:none">
                </form>
            </div>

            <div>
                <div style="font-size:20px;font-weight:700;color:#111827"><?= esc($user['name']) ?></div>
                <div style="font-size:13px;color:#6b7280"><?= esc($user['email']) ?></div>
                <div style="margin-top:5px">
                    <span
                        style="background:#eff6ff;color:#3b82f6;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600"><?= esc($user['role_name'] ?? '-') ?></span>
                </div>
            </div>
        </div>

        <style>
            #profile-pic-container:hover .pic-overlay {
                opacity: 1 !important;
            }
        </style>

        <form action="<?= base_url('profile/update') ?>" method="POST">
            <?= csrf_field() ?>
            <div style="display:grid;gap:18px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama
                        Lengkap</label>
                    <input type="text" name="name" value="<?= esc($user['name']) ?>" required
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                </div>
                <div>
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Email</label>
                    <input type="email" name="email" value="<?= esc($user['email']) ?>" required
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:<?= is_admin() ? 'white' : '#f3f4f6' ?>;box-sizing:border-box"
                        <?= !is_admin() ? 'readonly' : '' ?>>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">No.
                        Telepon</label>
                    <input type="text" name="phone" value="<?= esc($user['phone'] ?? '') ?>"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jenis
                        Kelamin</label>
                    <select name="gender"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:white;box-sizing:border-box">
                        <option value="L" <?= ($user['gender'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($user['gender'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Departemen</label>
                    <select name="dept_id"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:<?= is_admin() ? 'white' : '#f3f4f6' ?>;box-sizing:border-box"
                        <?= !is_admin() ? 'disabled' : '' ?>>
                        <?php if (is_admin()): ?>
                            <option value="">-- Pilih Departemen --</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $user['dept_id'] == $d['id'] ? 'selected' : '' ?>>
                                    <?= esc($d['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?= $user['dept_id'] ?>" selected><?= esc($user['dept_name'] ?? '-') ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit"
                    style="background:#3b82f6;color:white;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;width:100%">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <div style="display:grid;gap:20px">
        <!-- Notification Settings -->
        <div class="card"
            style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:30px;height:fit-content">
            <div
                style="font-size:16px;font-weight:700;color:#111827;margin-bottom:20px;padding-bottom:15px;border-bottom:1px solid #f3f4f6">
                <i class="bi bi-bell" style="color:#f59e0b"></i> Pengaturan Notifikasi
            </div>
            <form action="<?= base_url('profile/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="name" value="<?= esc($user['name']) ?>">
                <input type="hidden" name="email" value="<?= esc($user['email']) ?>">
                <div style="display:grid;gap:18px">
                    <div
                        style="display:flex;align-items:center;gap:10px;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #f3f4f6">
                        <input type="checkbox" name="notif_sound_enabled" id="notif_sound_enabled" value="1"
                            <?= ($user['notif_sound_enabled'] ?? 1) ? 'checked' : '' ?>
                            style="width:18px;height:18px;cursor:pointer">
                        <label for="notif_sound_enabled"
                            style="font-size:14px;font-weight:600;color:#374151;cursor:pointer">Aktifkan Suara
                            Notifikasi</label>
                    </div>

                    <div>
                        <label
                            style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jenis
                            Suara</label>
                        <div style="display:flex;gap:10px">
                            <select id="sound_type_select" name="notif_sound_type"
                                style="flex:1;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:white;box-sizing:border-box">
                                <option value="default" <?= ($user['notif_sound_type'] ?? 'default') == 'default' ? 'selected' : '' ?>>Default (Digital)</option>
                                <option value="bell" <?= ($user['notif_sound_type'] ?? '') == 'bell' ? 'selected' : '' ?>>
                                    Bell (Classic)</option>
                                <option value="beep" <?= ($user['notif_sound_type'] ?? '') == 'beep' ? 'selected' : '' ?>>
                                    Beep (Short)</option>
                                <option value="chime" <?= ($user['notif_sound_type'] ?? '') == 'chime' ? 'selected' : '' ?>>Chime (Elegant)</option>
                            </select>
                            <button type="button"
                                onclick="if(window.playBeepSound) { window.playBeepSound(document.getElementById('sound_type_select').value); } else { console.log('not found'); }"
                                style="background:#e5e7eb;color:#374151;border:none;padding:10px 16px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
                                <i class="bi bi-play-circle-fill" style="color:#059669"></i> Tes
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        style="background:#3b82f6;color:white;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;width:100%">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <?php $isSsoUser = (session()->get('auth_provider') === 'google'); ?>
        <div class="card"
            style="background:white;border-radius:12px;box-shadow:0 2px 4px rgba(0,0,0,0.05);padding:30px;height:fit-content;<?= $isSsoUser ? 'opacity:0.65;' : '' ?>">
            <div
                style="font-size:16px;font-weight:700;color:<?= $isSsoUser ? '#9ca3af' : '#111827' ?>;margin-bottom:20px;padding-bottom:15px;border-bottom:1px solid #f3f4f6">
                <i class="bi bi-shield-lock" style="color:<?= $isSsoUser ? '#9ca3af' : '#3b82f6' ?>"></i> Ganti Password
                <?php if ($isSsoUser): ?>
                    <span
                        style="margin-left:8px;background:#f3f4f6;color:#6b7280;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:500">Tidak
                        Tersedia</span>
                <?php endif; ?>
            </div>

            <?php if ($isSsoUser): ?>
                <!-- Banner info SSO -->
                <div
                    style="display:flex;align-items:flex-start;gap:12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px;margin-bottom:8px">
                    <div style="flex-shrink:0;margin-top:2px">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                fill="#4285F4" />
                            <path
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                fill="#34A853" />
                            <path
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                                fill="#FBBC05" />
                            <path
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                fill="#EA4335" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#1d4ed8;margin-bottom:4px">Akun Google SSO Aktif
                        </div>
                        <div style="font-size:12px;color:#3b82f6;line-height:1.5">Password Anda dikelola oleh Google. Untuk
                            mengubah password, silakan kunjungi <a href="https://myaccount.google.com/security"
                                target="_blank" style="color:#1d4ed8;font-weight:600">Akun Google Anda</a>.</div>
                    </div>
                </div>

                <!-- Form disabled (visual) -->
                <div style="display:grid;gap:18px;pointer-events:none;user-select:none">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#9ca3af;margin-bottom:6px">Password
                            Lama</label>
                        <input type="password" disabled placeholder="••••••••"
                            style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;box-sizing:border-box;background:#f9fafb;color:#9ca3af;cursor:not-allowed">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#9ca3af;margin-bottom:6px">Password
                            Baru</label>
                        <input type="password" disabled placeholder="••••••••"
                            style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;box-sizing:border-box;background:#f9fafb;color:#9ca3af;cursor:not-allowed">
                    </div>
                    <div>
                        <label
                            style="display:block;font-size:13px;font-weight:600;color:#9ca3af;margin-bottom:6px">Konfirmasi
                            Password</label>
                        <input type="password" disabled placeholder="••••••••"
                            style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;box-sizing:border-box;background:#f9fafb;color:#9ca3af;cursor:not-allowed">
                    </div>
                    <button type="button" disabled
                        style="background:#e5e7eb;color:#9ca3af;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:not-allowed;width:100%">
                        <i class="bi bi-lock"></i> Ganti Password
                    </button>
                </div>

            <?php else: ?>
                <!-- Form aktif untuk login manual -->
                <form action="<?= base_url('profile/change-password') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div style="display:grid;gap:18px">
                        <div>
                            <label
                                style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Password
                                Lama</label>
                            <div style="position:relative">
                                <input type="password" name="old_password" required placeholder="Masukkan password lama"
                                    style="width:100%;padding:10px 40px 10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box">
                                <button type="button" class="toggle-password"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Password
                                Baru</label>
                            <div style="position:relative">
                                <input type="password" name="new_password" required placeholder="Min. 8 karakter"
                                    style="width:100%;padding:10px 40px 10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                                <button type="button" class="toggle-password"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Konfirmasi
                                Password</label>
                            <div style="position:relative">
                                <input type="password" name="confirm_password" required placeholder="Ulangi password baru"
                                    style="width:100%;padding:10px 40px 10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box">
                                <button type="button" class="toggle-password"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit"
                            style="background:#10b981;color:white;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;width:100%">
                            <i class="bi bi-lock"></i> Ganti Password
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- ── Modal Cropper (WhatsApp Style) ── -->
<div id="cropperModal"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(6px)">
    <div
        style="background:white;border-radius:16px;max-width:500px;width:100%;box-shadow:0 25px 60px rgba(0,0,0,0.5);animation:modalIn .25s ease-out;overflow:hidden">
        <div
            style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:8px">
                <i class="bi bi-crop" style="color:#3b82f6;font-size:18px"></i>
                <span style="font-weight:700;font-size:15px">Sesuaikan Foto Profil</span>
            </div>
            <button type="button" id="closeCropper"
                style="background:none;border:none;font-size:24px;color:#9ca3af;cursor:pointer">&times;</button>
        </div>

        <div style="padding:20px;background:#f3f4f6;position:relative;height:350px">
            <img id="imageToCrop" style="max-width:100%;display:block">
        </div>

        <div
            style="padding:16px 20px;background:white;display:flex;justify-content:center;gap:15px;border-top:1px solid #e5e7eb">
            <button type="button" onclick="cropper.zoom(0.1)"
                style="border:none;background:#f3f4f6;width:40px;height:40px;border-radius:50%;cursor:pointer"><i
                    class="bi bi-plus-lg"></i></button>
            <button type="button" onclick="cropper.zoom(-0.1)"
                style="border:none;background:#f3f4f6;width:40px;height:40px;border-radius:50%;cursor:pointer"><i
                    class="bi bi-dash-lg"></i></button>
            <button type="button" onclick="cropper.rotate(-90)"
                style="border:none;background:#f3f4f6;width:40px;height:40px;border-radius:50%;cursor:pointer"><i
                    class="bi bi-arrow-counterclockwise"></i></button>
        </div>

        <div style="padding:16px 20px;display:flex;gap:12px;justify-content:flex-end">
            <button type="button" id="cancelCrop"
                style="padding:10px 20px;background:white;color:#6b7280;border:1px solid #d1d5db;border-radius:8px;font-weight:600;cursor:pointer">Batal</button>
            <button type="button" id="saveCrop"
                style="padding:10px 25px;background:#3b82f6;color:white;border:none;border-radius:8px;font-weight:700;cursor:pointer">
                <i class="bi bi-check-lg"></i> Terapkan
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes modalIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .cropper-view-box,
    .cropper-face {
        border-radius: 50%;
    }

    /* WhatsApp Circle Style */
</style>

<script>

    // Profile Picture Upload Logic (WhatsApp Style with Cropper)
    const profilePicContainer = document.getElementById('profile-pic-container');
    const profilePicInput = document.getElementById('profile-pic-input');
    const profilePicForm = document.getElementById('profile-pic-form');
    const cropperModal = document.getElementById('cropperModal');
    const imageToCrop = document.getElementById('imageToCrop');
    const saveCrop = document.getElementById('saveCrop');
    const cancelCrop = document.getElementById('cancelCrop');
    const closeCropper = document.getElementById('closeCropper');
    let cropper;

    if (profilePicContainer && profilePicInput) {
        profilePicContainer.addEventListener('click', () => profilePicInput.click());

        profilePicInput.addEventListener('change', (e) => {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];

                // Validasi size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 10MB.');
                    profilePicInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    imageToCrop.src = event.target.result;
                    cropperModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';

                    if (cropper) cropper.destroy();
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        guides: false,
                        center: true,
                        highlight: false,
                        cropBoxMovable: false,
                        cropBoxResizable: false,
                        toggleDragModeOnDblclick: false,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        const hideModal = () => {
            cropperModal.style.display = 'none';
            document.body.style.overflow = '';
            profilePicInput.value = '';
            if (cropper) cropper.destroy();
        };

        cancelCrop.addEventListener('click', hideModal);
        closeCropper.addEventListener('click', hideModal);

        saveCrop.addEventListener('click', () => {
            const canvas = cropper.getCroppedCanvas({
                width: 500,
                height: 500
            });

            canvas.toBlob((blob) => {
                const formData = new FormData();
                formData.append('profile_pic', blob, 'profile_pic.jpg');

                // Baca CSRF token SEBELUM hideModal() me-reset form
                const csrfTokenName = '<?= csrf_token() ?>';
                const csrfInput = profilePicForm.querySelector('input[name="' + csrfTokenName + '"]');
                const csrfValue = csrfInput ? csrfInput.value : '';
                if (csrfValue) {
                    formData.append(csrfTokenName, csrfValue);
                }

                // Show loading
                profilePicContainer.style.opacity = '0.5';
                hideModal();

                // Kirim via fetch supaya tidak reload seluruh halaman
                fetch('<?= base_url('profile/update-photo') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfValue
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            window.location.reload(); // Refresh untuk melihat hasil
                        } else {
                            alert('Gagal mengunggah foto. Silakan coba lagi.');
                            profilePicContainer.style.opacity = '1';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan saat mengunggah.');
                        profilePicContainer.style.opacity = '1';
                    });
            }, 'image/jpeg', 0.9);
        });
    }

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
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