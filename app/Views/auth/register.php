<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Daftar — Helpdesk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: sans-serif; background: #f3f4f6; margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h1 { font-size: 24px; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 14px; margin-bottom: 5px; font-weight: 500; }
        input, select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; box-sizing: border-box; }
        .btn { padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 8px; width: 100%; font-weight: 600; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Daftar Akun</h1>
        <form action="/register" method="POST">
            <div class="form-group"><label>Nama Lengkap</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group">
                <label>Password</label>
                <div style="position:relative">
                    <input type="password" name="password" id="m-pw" required style="padding-right: 40px;">
                    <button type="button" onclick="togglePasswordVisibility()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-eye" id="m-pw-icon"></i>
                    </button>
                </div>
            </div>
            <div class="form-group"><label>Departemen</label>
                <select name="dept_id" required>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= esc($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Jenis Kelamin</label>
                <input type="radio" name="gender" value="L" checked> Laki-laki 
                <input type="radio" name="gender" value="P"> Perempuan
            </div>
            <button type="submit" class="btn">Daftar Sekarang</button>
        </form>
        <p style="text-align:center;margin-top:20px;font-size:14px">Sudah punya akun? <a href="/login">Login</a></p>
    </div>
    <script>
        function togglePasswordVisibility() {
            const pwInput = document.getElementById('m-pw');
            const pwIcon = document.getElementById('m-pw-icon');
            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                pwIcon.classList.remove('bi-eye');
                pwIcon.classList.add('bi-eye-slash');
            } else {
                pwInput.type = 'password';
                pwIcon.classList.remove('bi-eye-slash');
                pwIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>

