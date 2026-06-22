<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Helpdesk Pusim</title>
    <link rel="icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #050b14;
            position: relative;
            overflow: hidden;
            touch-action: manipulation;
        }

        /* Latar Belakang Jaringan Ultra-HD via Canvas */
        #network-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: radial-gradient(circle at 50% 50%, #0a1628 0%, #02050a 100%);
        }

        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
            padding: 44px 40px 36px;
            background: rgba(15, 25, 45, 0.45);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .logo-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            margin: -76px auto 20px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(200, 215, 240, 0.3), rgba(150, 175, 210, 0.2));
            border: 2px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .logo-wrapper img {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            background: radial-gradient(closest-side, #ffffff 45%, transparent 50%);
            object-fit: cover;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 28px;
        }

        /* ============================
           SSO PRIMARY BUTTON
        ============================ */
        .sso-section {
            margin-bottom: 8px;
        }

        .sso-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.35);
            margin-bottom: 12px;
        }

        .btn-sso-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            width: 100%;
            padding: 16px 20px;
            background: linear-gradient(135deg, #1a73e8 0%, #4285f4 50%, #5b9cf6 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 24px rgba(66, 133, 244, 0.5), inset 0 1px 0 rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.15);
            letter-spacing: 0.2px;
        }

        .btn-sso-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }

        .btn-sso-primary:hover::before {
            left: 100%;
        }

        .btn-sso-primary:hover {
            background: linear-gradient(135deg, #1565c0 0%, #1a73e8 50%, #4285f4 100%);
            box-shadow: 0 8px 30px rgba(66, 133, 244, 0.7), inset 0 1px 0 rgba(255,255,255,0.25);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .btn-sso-primary:active {
            transform: translateY(0px);
        }

        .sso-icon-wrapper {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sso-icon-wrapper svg {
            width: 18px;
            height: 18px;
        }

        .sso-text-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sso-text-main {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
        }

        .sso-text-sub {
            font-size: 11px;
            font-weight: 400;
            opacity: 0.75;
            letter-spacing: 0.3px;
        }

        /* Badge "Direkomendasikan" */
        .recommended-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(74, 222, 128, 0.15);
            border: 1px solid rgba(74, 222, 128, 0.35);
            color: #4ade80;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 20px;
            margin-bottom: 14px;
        }

        .recommended-badge i {
            font-size: 10px;
        }

        /* ============================
           DIVIDER
        ============================ */
        .divider {
            position: relative;
            margin: 22px 0 4px;
            text-align: center;
        }

        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
        }

        .divider span {
            position: relative;
            background: rgba(21, 35, 59, 1);
            padding: 0 12px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        /* ============================
           MANUAL LOGIN TOGGLE
        ============================ */
        .manual-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 10px 16px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.3s ease;
        }

        .manual-toggle-btn:hover {
            border-color: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.75);
            background: rgba(255, 255, 255, 0.04);
        }

        .manual-toggle-btn i {
            font-size: 14px;
            transition: transform 0.3s ease;
        }

        .manual-toggle-btn.open i.toggle-chevron {
            transform: rotate(180deg);
        }

        /* ============================
           MANUAL LOGIN FORM COLLAPSIBLE
        ============================ */
        .manual-login-section {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.45s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s ease;
            opacity: 0;
        }

        .manual-login-section.open {
            max-height: 600px;
            opacity: 1;
        }

        .manual-form-inner {
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            margin-top: 16px;
        }

        /* Jika ada error, langsung buka form manual */
        .manual-login-section.has-error {
            max-height: 600px;
            opacity: 1;
        }

        /* ============================
           FORM FIELDS
        ============================ */
        .form-group {
            text-align: left;
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.55);
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .form-group input {
            width: 100%;
            padding: 11px 14px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .form-group input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(91, 156, 246, 0.6);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Fix Chrome/Edge Autofill di tema gelap */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #141f33 inset !important;
            -webkit-text-fill-color: #ffffff !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            margin-top: 4px;
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: rgba(255, 255, 255, 0.13);
            border-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: left;
        }

        /* ============================
           FOOTER
        ============================ */
        .footer-text {
            position: relative;
            z-index: 1;
            margin-top: 24px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.35);
        }

        .footer-text a {
            color: rgba(255, 255, 255, 0.45);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-text a:hover {
            color: rgba(255, 255, 255, 0.7);
        }

        /* ============================
           SECURITY INFO BADGE
        ============================ */
        .sso-security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.3);
            margin-top: 10px;
        }

        .sso-security-note i {
            font-size: 11px;
        }

        /* ============================
           RESPONSIVE
        ============================ */
        @media (max-width: 480px) {
            .login-card {
                margin: 16px;
                padding: 32px 20px 24px;
            }

            h1 {
                font-size: 22px;
            }

            .logo-wrapper {
                width: 90px;
                height: 90px;
                margin-top: -62px;
            }

            .logo-wrapper img {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>

<body>

    <!-- Latar Belakang Animasi Jaringan Ultra-HD 4K (Canvas Render) -->
    <canvas id="network-canvas"></canvas>

    <div class="login-card">
        <div class="logo-wrapper">
            <img src="<?= base_url('images/logopusim.png') ?>" alt="Helpdesk Pusim Logo">
        </div>
        <h1>Helpdesk Pusim</h1>
        <p class="subtitle">Selamat datang — silakan masuk</p>

        <?php if (isset($error)): ?>
            <div class="alert-error"><?= esc($error) ?></div>
        <?php endif; ?>

        <!-- =====================
             SSO — UTAMA
        ===================== -->
        <div class="sso-section">

            <a href="<?= base_url('auth/google') ?>" class="btn-sso-primary" id="btnSSO">
                <div class="sso-icon-wrapper">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#ffffff" opacity="0.9"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#ffffff" opacity="0.9"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#ffffff" opacity="0.9"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#ffffff" opacity="0.9"/>
                    </svg>
                </div>
                <div class="sso-text-group">
                    <span class="sso-text-main">Masuk dengan SSO</span>
                    <span class="sso-text-sub">Akun Universitas Merdeka Malang</span>
                </div>
            </a>

            <div class="sso-security-note">
                <i class="bi bi-lock-fill"></i>
                Login aman via Google Workspace Unmer
            </div>
        </div>

        <?php 
            $isManualAttempt = old('email') !== null;
            $showManual = (isset($_GET['login']) && $_GET['login'] === 'admin') || ($isManualAttempt && isset($error)); 
        ?>
        <?php if ($showManual): ?>
            <!-- =====================
                 DIVIDER
            ===================== -->
            <div class="divider"><span>atau</span></div>

            <!-- =====================
                 MANUAL LOGIN — SEKUNDER
            ===================== -->
            <button type="button" class="manual-toggle-btn" id="manualToggle" aria-expanded="false" aria-controls="manualLoginSection">
                <i class="bi bi-person-fill"></i>
                Login dengan Email & Kata Sandi
                <i class="bi bi-chevron-down toggle-chevron"></i>
            </button>
        <?php endif; ?>

        <?php if ($showManual): ?>
        <div class="manual-login-section <?= $showManual ? 'open' : '' ?> <?= isset($error) ? 'has-error' : '' ?>" id="manualLoginSection">
            <div class="manual-form-inner">
                <form action="<?= base_url('login') ?>" method="POST" id="loginForm">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="user@unmer.ac.id" required
                            value="<?= isset($error) ? old('email') : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <div style="position:relative">
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                style="padding-right: 45px;">
                            <button type="button" id="togglePassword"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center;z-index:10">
                                <i class="bi bi-eye" id="eyeIcon" style="font-size:1.1rem"></i>
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($captcha_required)): ?>
                        <div class="form-group">
                            <label for="captcha_answer">Verifikasi Keamanan</label>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="
                                    background: rgba(255,255,255,0.08) repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255,255,255,0.03) 5px, rgba(255,255,255,0.03) 10px);
                                    border: 1px solid rgba(255,255,255,0.2);
                                    border-radius: 8px;
                                    padding: 10px 18px;
                                    font-size: 20px;
                                    font-weight: 800;
                                    font-family: 'Courier New', Courier, monospace;
                                    font-style: italic;
                                    color: #ffffff;
                                    letter-spacing: 6px;
                                    min-width: 140px;
                                    text-align: center;
                                    flex-shrink: 0;
                                    text-shadow: 2px 2px 0 rgba(74,138,244,0.5), -1px -1px 0 rgba(0,0,0,0.5);
                                    transform: skewX(-5deg);
                                    user-select: none;
                                "><?= esc($captcha_question ?? '') ?></div>
                                <input type="text" name="captcha_answer" id="captcha_answer" placeholder="Ketik captcha" required
                                    autocomplete="off" style="flex:1;padding-right:14px; text-transform: uppercase;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn-login" id="btnLogin">
                        <i class="bi bi-box-arrow-in-right" style="margin-right:6px;"></i>
                        Masuk
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <p class="footer-text" style="margin-top: 8px;">&copy; 2026 Universitas Merdeka Malang</p>

    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('bi-eye');
                eyeIcon.classList.toggle('bi-eye-slash');
            });
        }

        // Toggle manual login section
        const manualToggle = document.getElementById('manualToggle');
        const manualSection = document.getElementById('manualLoginSection');

        if (manualToggle && manualSection) {
            // Jika ada error, langsung tandai tombol sebagai open
            if (manualSection.classList.contains('open')) {
                manualToggle.classList.add('open');
                manualToggle.setAttribute('aria-expanded', 'true');
            }

            manualToggle.addEventListener('click', function() {
                const isOpen = manualSection.classList.toggle('open');
                manualToggle.classList.toggle('open', isOpen);
                manualToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        // SSO button loading state
        const btnSSO = document.getElementById('btnSSO');
        if (btnSSO) {
            btnSSO.addEventListener('click', function(e) {
                setTimeout(() => {
                    btnSSO.style.opacity = '0.75';
                    btnSSO.style.pointerEvents = 'none';
                    const textMain = btnSSO.querySelector('.sso-text-main');
                    if (textMain) textMain.textContent = 'Menghubungkan...';
                }, 10);
            });
        }
    </script>

    <!-- Script Jaringan Topologi Bintang Super HD Tanpa Pecah -->
    <script>
        const canvas = document.getElementById('network-canvas');
        const ctx = canvas.getContext('2d');
        let width, height;

        const particles = [];
        const properties = {
            bgColor: '#02050a',
            particleColor: 'rgba(74, 138, 244, 0.8)',
            particleRadius: 3,
            particleCount: 80,
            particleMaxVelocity: 0.5,
            lineLength: 160,
        };

        function resizeCanvas() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }

        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.velocityX = Math.random() * (properties.particleMaxVelocity * 2) - properties.particleMaxVelocity;
                this.velocityY = Math.random() * (properties.particleMaxVelocity * 2) - properties.particleMaxVelocity;
            }
            position() {
                this.x + this.velocityX > width && this.velocityX > 0 || this.x + this.velocityX < 0 && this.velocityX < 0 ? this.velocityX *= -1 : this.velocityX;
                this.y + this.velocityY > height && this.velocityY > 0 || this.y + this.velocityY < 0 && this.velocityY < 0 ? this.velocityY *= -1 : this.velocityY;
                this.x += this.velocityX;
                this.y += this.velocityY;
            }
            reDraw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, properties.particleRadius, 0, Math.PI * 2);
                ctx.closePath();
                ctx.fillStyle = properties.particleColor;
                ctx.fill();
                ctx.beginPath();
                ctx.arc(this.x, this.y, properties.particleRadius * 3, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(74, 138, 244, 0.1)';
                ctx.fill();
            }
        }

        function reDrawBackground() {
            ctx.clearRect(0, 0, width, height);
        }

        function drawLines() {
            let x1, y1, x2, y2, length, opacity;
            for (let i in particles) {
                for (let j in particles) {
                    x1 = particles[i].x;
                    y1 = particles[i].y;
                    x2 = particles[j].x;
                    y2 = particles[j].y;
                    length = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
                    if (length < properties.lineLength) {
                        opacity = 1 - length / properties.lineLength;
                        ctx.lineWidth = '0.5';
                        ctx.strokeStyle = 'rgba(74, 138, 244, ' + opacity + ')';
                        ctx.beginPath();
                        ctx.moveTo(x1, y1);
                        ctx.lineTo(x2, y2);
                        ctx.closePath();
                        ctx.stroke();
                    }
                }
            }
        }

        function drawParticles() {
            for (let i in particles) {
                particles[i].position();
                particles[i].reDraw();
            }
        }

        function loop() {
            reDrawBackground();
            drawLines();
            drawParticles();
            requestAnimationFrame(loop);
        }

        function init() {
            for (let i = 0; i < properties.particleCount; i++) {
                particles.push(new Particle());
            }
            loop();
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        init();
    </script>
</body>

</html>