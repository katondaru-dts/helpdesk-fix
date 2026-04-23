System.Object[]EOF_MAIN
cat << 'EOF_LOGIN' > /var/www/html/app/Views/auth/login.php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - Helpdesk</title>
    <link rel="icon" href="<?= base_url('images/app_icon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            width: 80px;
            height: 80px;
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
            width: 52px;
            height: 52px;
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
        @media (max-width: 480px) {
            .login-card { margin: 0 20px; padding: 36px 24px 28px; }
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
            <img src="<?= base_url('images/logo.png') ?>" alt="Helpdesk Logo">
        </div>
        <h1>HelpDesk</h1>
        <p class="subtitle">Login ke akun Anda</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="user@unmer.ac.id" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="**********" required>
            </div>
            <button type="submit" class="btn-login" id="btnLogin">Masuk</button>
        </form>
    </div>

    <p class="footer-text">Lupa Kata Sandi? Hubungi Administrator.</p>
    <p class="footer-text" style="margin-top: 8px;">&copy; 2026 Universitas Merdeka Malang</p>

    <svg class="sparkle" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 0L23.5 16.5L40 20L23.5 23.5L20 40L16.5 23.5L0 20L16.5 16.5L20 0Z" fill="rgba(140,180,255,0.8)"/>
    </svg>
</body>
</html>

EOF_LOGIN
echo "DONE"
