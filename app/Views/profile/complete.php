<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Lengkapi Profil - Helpdesk' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #7c3aed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 20%, rgba(37,99,235,0.3) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(124,58,237,0.3) 0%, transparent 50%);
            animation: bgShift 8s ease-in-out infinite alternate;
        }

        @keyframes bgShift {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(20px, -20px) rotate(2deg); }
        }

        .card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
            padding: 48px 44px;
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
            animation: cardIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 24px rgba(37,99,235,0.35);
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 8px 24px rgba(37,99,235,0.35); }
            50% { box-shadow: 0 12px 32px rgba(37,99,235,0.55); }
        }

        .icon-wrap i {
            font-size: 32px;
            color: white;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 28px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        .user-name { font-weight: 600; font-size: 14px; color: #1e40af; }
        .user-email { font-size: 12px; color: #60a5fa; }

        .wa-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 24px;
            font-size: 12.5px;
            color: #15803d;
            line-height: 1.5;
        }

        .wa-notice i { font-size: 18px; color: #16a34a; flex-shrink: 0; margin-top: 1px; }

        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-label .required {
            color: #ef4444;
            margin-left: 3px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #111827;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }

        .phone-input-wrap { position: relative; }

        .phone-prefix {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .phone-input-wrap .form-control {
            padding-left: 46px;
        }

        .gender-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .gender-option { position: relative; }

        .gender-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .gender-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.2s;
            user-select: none;
        }

        .gender-option label i { font-size: 20px; }

        .gender-option input[type="radio"]:checked + label {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .gender-option label:hover {
            border-color: #93c5fd;
            background: #f0f7ff;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            margin-top: 8px;
            box-shadow: 0 4px 15px rgba(37,99,235,0.35);
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.45);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .error-box ul { margin: 0; padding-left: 16px; }
        .error-box li { margin-bottom: 4px; }

        .steps-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 28px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .step-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        .step.done .step-dot { background: #22c55e; color: white; }
        .step.active .step-dot { background: #2563eb; color: white; }
        .step.done .step-label { color: #6b7280; }
        .step.active .step-label { color: #1d4ed8; }

        .step-line { width: 32px; height: 2px; background: #e5e7eb; border-radius: 1px; }
        .step-line.done { background: #22c55e; }

        @media (max-width: 520px) {
            .card { padding: 32px 24px; }
            .title { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- Icon -->
        <div class="icon-wrap">
            <i class="bi bi-person-check-fill"></i>
        </div>

        <!-- Steps indicator -->
        <div class="steps-indicator">
            <div class="step done">
                <div class="step-dot"><i class="bi bi-check-lg" style="font-size:10px"></i></div>
                <span class="step-label">Login</span>
            </div>
            <div class="step-line done"></div>
            <div class="step active">
                <div class="step-dot">2</div>
                <span class="step-label">Profil</span>
            </div>
            <div class="step-line"></div>
            <div class="step" style="opacity:0.4">
                <div class="step-dot" style="background:#e5e7eb;color:#9ca3af">3</div>
                <span class="step-label" style="color:#9ca3af">Selesai</span>
            </div>
        </div>

        <div class="title">Satu Langkah Lagi! 🎉</div>
        <div class="subtitle">
            Lengkapi profil Anda untuk mengaktifkan fitur tiket helpdesk.<br>
            Data ini diperlukan untuk komunikasi terkait laporan Anda.
        </div>

        <!-- User info -->
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr(session()->get('name') ?? 'U', 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= esc(session()->get('name')) ?></div>
                <div class="user-email"><?= esc(session()->get('email')) ?></div>
            </div>
        </div>

        <!-- Error -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?= base_url('profile/save-complete') ?>" method="POST" id="completeForm">
            <?= csrf_field() ?>

            <!-- Nomor Telepon -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-telephone-fill" style="color:#2563eb"></i>
                    Nomor Telepon WhatsApp
                    <span class="required">*</span>
                </label>
                <div class="phone-input-wrap">
                    <span class="phone-prefix">+62</span>
                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        class="form-control"
                        placeholder="8xx-xxxx-xxxx"
                        value="<?= esc(old('phone', ltrim($user['phone'] ?? '', '+620'))) ?>"
                        maxlength="15"
                        autocomplete="tel"
                        required
                    >
                </div>

                <!-- Peringatan WhatsApp -->
                <div class="wa-notice" style="margin-top:10px;margin-bottom:0">
                    <i class="bi bi-whatsapp"></i>
                    <span>
                        <strong>Penting:</strong> Nomor ini wajib <strong>aktif dan terhubung dengan WhatsApp</strong>,
                        karena akan digunakan untuk <em>follow-up</em> terkait tiket yang Anda ajukan.
                    </span>
                </div>
            </div>

            <!-- Jenis Kelamin -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-person-fill" style="color:#2563eb"></i>
                    Jenis Kelamin
                    <span class="required">*</span>
                </label>
                <div class="gender-group">
                    <div class="gender-option">
                        <input type="radio" name="gender" id="gender_L" value="L"
                            <?= old('gender', $user['gender'] ?? '') == 'L' ? 'checked' : '' ?>>
                        <label for="gender_L">
                            <i class="bi bi-gender-male" style="color:#2563eb"></i>
                            Laki-laki
                        </label>
                    </div>
                    <div class="gender-option">
                        <input type="radio" name="gender" id="gender_P" value="P"
                            <?= old('gender', $user['gender'] ?? '') == 'P' ? 'checked' : '' ?>>
                        <label for="gender_P">
                            <i class="bi bi-gender-female" style="color:#ec4899"></i>
                            Perempuan
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="bi bi-check-circle-fill"></i>
                Simpan & Lanjutkan ke Dashboard
            </button>
        </form>
    </div>

    <script>
        // Format nomor telepon Indonesia
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function () {
                // Hapus karakter selain angka
                let val = this.value.replace(/\D/g, '');
                // Hapus awalan 0 atau 62
                if (val.startsWith('62')) val = val.slice(2);
                if (val.startsWith('0')) val = val.slice(1);
                this.value = val;
            });
        }

        // Simpan nomor asli dengan +62 saat submit
        const form = document.getElementById('completeForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                const btn = document.getElementById('submitBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
                }

                // Tambahkan +62 ke nomor sebelum submit
                const phone = phoneInput.value.replace(/\D/g, '');
                if (phone && !phone.startsWith('62')) {
                    phoneInput.value = '62' + phone;
                }
            });
        }
    </script>
</body>
</html>
