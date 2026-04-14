<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Terlalu Banyak Permintaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; background: #f3f4f6; text-align: center; }
        .box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-width: 400px; }
        h1 { color: #ef4444; font-size: 24px; margin-bottom: 20px; }
        p { color: #4b5563; line-height: 1.5; }
        a { display: inline-block; margin-top: 20px; text-decoration: none; background: #3b82f6; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Batas Upaya Tercapai</h1>
        <p><?= esc($message ?? 'Terlalu banyak permintaan dari IP Anda. Silakan tunggu beberapa saat sebelum mencoba lagi.') ?></p>
        <a href="<?= base_url('login') ?>">Kembali ke Login</a>
    </div>
</body>
</html>
