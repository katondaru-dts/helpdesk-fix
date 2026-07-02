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
        color: #B8935A; /* Orange/light brown */
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
        color: #B8935A;
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
        border-color: #B8935A;
        box-shadow: 0 0 0 3px rgba(184, 147, 90, 0.15);
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
        <div class="page-header-title">Parameter Dasar</div>
        <div class="page-header-sub">Kelola konfigurasi keamanan dan parameter dasar aplikasi</div>
    </div>
</div>

<hr style="border: 0; height: 1px; background: #e5e7eb; margin: 20px 0 35px;">

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
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline" style="border: 1px solid #d1d5db; color: #4b5563; background: white; padding: 10px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 6px; justify-content: center; text-align: center;">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>
        </div>

    </form>
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
</script>

<?= $this->endSection() ?>
