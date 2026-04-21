<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - Helpdesk Pusim</title>
    <link rel="icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a1628 0%, #0f2847 40%, #1a3a6e 70%, #0f2847 100%);
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(59,130,246,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(59,130,246,0.06) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .bg-pattern {
            position: absolute;
            inset: 0;
            z-index: 0;
            opacity: 0.08;
            pointer-events: none;
        }
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 40px 36px 32px;
            background: rgba(180, 200, 230, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        .logo-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            margin: -76px auto 20px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(200,215,240,0.3), rgba(150,175,210,0.2));
            border: 2px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        .logo-wrapper img {
            width: 85px;
            height: 85px;
            border-radius: 12px;
            object-fit: cover;
        }
        h1 {
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 28px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #1a1a2e;
            outline: none;
            transition: all 0.3s ease;
        }
        .form-group input::placeholder { color: #8a8fa8; }
        .form-group input:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: #5b9cf6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .btn-login {
            width: 100%;
            padding: 13px;
            margin-top: 6px;
            background: linear-gradient(135deg, #4a8af4 0%, #5b9cf6 50%, #6aacfa 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74, 138, 244, 0.35);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #3a7ae4 0%, #4b8cf6 50%, #5a9cfa 100%);
            box-shadow: 0 6px 20px rgba(74, 138, 244, 0.5);
            transform: translateY(-1px);
        }
        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(74, 138, 244, 0.3);
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            text-align: left;
        }
        .footer-text {
            position: relative;
            z-index: 1;
            margin-top: 24px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.45);
        }
        .sparkle {
            position: fixed;
            bottom: 32px;
            right: 40px;
            z-index: 1;
            opacity: 0.2;
            animation: sparkle-pulse 3s ease-in-out infinite;
        }
        @keyframes sparkle-pulse {
            0%, 100% { opacity: 0.15; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(1.1); }
        }
        .divider {
            position: relative;
            margin: 24px 0;
            text-align: center;
        }
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }
        .divider span {
            position: relative;
            background: rgba(30, 50, 85, 0.8);
            padding: 0 12px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
        }
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.95);
            color: #1a1a2e;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .btn-google:hover {
            background: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }
        .btn-google svg {
            width: 20px;
            height: 20px;
        }
        @media (max-width: 480px) {
            .login-card { margin: 0 20px; padding: 36px 24px 28px; }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <svg class="bg-pattern" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
        <defs>
            <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                <path d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="0.5"/>
            </pattern>
            <pattern id="dots" width="30" height="30" patternUnits="userSpaceOnUse">
                <circle cx="15" cy="15" r="1" fill="rgba(255,255,255,0.3)"/>
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)"/>
        <rect width="100%" height="100%" fill="url(#dots)"/>
        <line x1="0" y1="100%" x2="30%" y2="50%" stroke="rgba(59,130,246,0.12)" stroke-width="1"/>
        <line x1="70%" y1="0" x2="100%" y2="60%" stroke="rgba(59,130,246,0.08)" stroke-width="1"/>
    </svg>

    <div class="login-card">
        <div class="logo-wrapper">
            <img src="<?= base_url('images/logo.png') ?>" alt="Helpdesk Pusim Logo">
        </div>
        <h1>Helpdesk Pusim</h1>
        <p class="subtitle">Login ke akun Anda</p>

        <?php if (isset($error)): ?>
            <div class="alert-error"><?= esc($error) ?></div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="user@unmer.ac.id" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div style="position:relative">
                    <input type="password" name="password" id="password" placeholder="**********" required style="padding-right: 45px;">
                    <button type="button" id="togglePassword" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center;z-index:10">
                        <i class="bi bi-eye" id="eyeIcon" style="font-size:1.1rem"></i>
                    </button>
                </div>
            </div>

            <?php if (!empty($captcha_required)): ?>
                <div class="form-group" id="captcha-block" style="animation: fadeIn .3s ease;">
                    <label for="captcha_answer">Verifikasi Keamanan</label>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="
                            background: rgba(255,255,255,0.12) repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255,255,255,0.05) 5px, rgba(255,255,255,0.05) 10px);
                            border: 1px solid rgba(255,255,255,0.25);
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
                            transform: skewX(-10deg);
                            user-select: none;
                        "><?= esc($captcha_question ?? '') ?></div>
                        <input type="text" name="captcha_answer" id="captcha_answer"
                               placeholder="Ketik kode captcha" required autocomplete="off"
                               style="flex:1;padding-right:14px; text-transform: uppercase;">
                    </div>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-login" id="btnLogin">Masuk</button>
        </form>

        <script>
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const eyeIcon = document.querySelector('#eyeIcon');

            togglePassword.addEventListener('click', function (e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                eyeIcon.classList.toggle('bi-eye');
                eyeIcon.classList.toggle('bi-eye-slash');
            });
        </script>

        <div class="divider">
            <span>atau masuk dengan</span>
        </div>

        <a href="<?= base_url('auth/google') ?>" class="btn-google">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Login dengan SSO
        </a>
    </div>

    <p class="footer-text">Lupa Kata Sandi? Hubungi Administrator.</p>

    <svg class="sparkle" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 0L23.5 16.5L40 20L23.5 23.5L20 40L16.5 23.5L0 20L16.5 16.5L20 0Z" fill="rgba(140,180,255,0.8)"/>
    </svg>
</body>
</html>
