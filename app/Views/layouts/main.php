<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($unreadNotifications) && $unreadNotifications > 0) ? '(' . $unreadNotifications . ') ' : '' ?><?= $pageTitle ?? 'Helpdesk Pusim' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>

<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="<?= base_url('images/logo.png') ?>" alt="Logo" style="width: 42px; height: 42px; border-radius: 6px; object-fit: cover; margin-right: 8px;">
            <span>Helpdesk Pusim</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>
            <?php
            $role = session()->get('role_id');
            $isStaff = ($role == 1 || $role == 2 || $role == 4);
            ?>
            <a href="<?= base_url('dashboard') ?>" class="<?= $activePage == 'dashboard' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="<?= base_url('tickets') ?>" class="<?= $activePage == 'tickets' ? 'active' : '' ?>"><i class="bi bi-ticket-detailed"></i> <?= $isStaff ? 'Semua Tiket' : 'Tiket Saya' ?></a>

            <?php if (has_permission('Buat Tiket') || $role == 4): ?>
            <a href="<?= base_url('tickets/create') ?>" class="<?= $activePage == 'ticket-create' ? 'active' : '' ?>"><i class="bi bi-plus-circle"></i> Buat Tiket</a>
            <?php endif; ?>

            <?php if ($isStaff): ?>
            <div class="nav-section" style="margin-top: 16px;">Administrasi</div>
            <?php if (has_permission('Kelola User')): ?>
            <a href="<?= base_url('admin/users') ?>" class="<?= $activePage == 'admin-users' ? 'active' : '' ?>"><i class="bi bi-people"></i> Kelola User</a>
            <?php endif; ?>
            <?php if (has_permission('Kelola Departemen')): ?>
            <a href="<?= base_url('admin/departments') ?>" class="<?= $activePage == 'admin-departments' ? 'active' : '' ?>"><i class="bi bi-building"></i> Departemen</a>
            <?php endif; ?>
            <?php if (has_permission('Kelola Kategori')): ?>
            <a href="<?= base_url('admin/categories') ?>" class="<?= $activePage == 'admin-categories' ? 'active' : '' ?>"><i class="bi bi-tag"></i> Kategori</a>
            <?php endif; ?>
            <?php if (has_permission('Kelola Role')): ?>
            <a href="<?= base_url('admin/roles') ?>" class="<?= $activePage == 'admin-roles' ? 'active' : '' ?>"><i class="bi bi-shield-check"></i> Role & Izin</a>
            <?php endif; ?>
            <?php if (has_permission('Lihat Laporan')): ?>
            <a href="<?= base_url('admin/reports') ?>" class="<?= $activePage == 'admin-reports' ? 'active' : '' ?>"><i class="bi bi-graph-up"></i> Laporan</a>
            <?php endif; ?>
            <?php if (has_permission('Lihat Audit Log')): ?>
            <a href="<?= base_url('admin/audit-logs') ?>" class="<?= $activePage == 'admin-audit-logs' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i> Audit Log</a>
            <?php endif; ?>
            <?php endif; ?>

            <div class="nav-section" style="margin-top: 16px;">Akun</div>
            <a href="<?= base_url('notifications') ?>" class="<?= $activePage == 'notifications' ? 'active' : '' ?>">
                <i class="bi bi-bell"></i> Notifikasi
                <span id="notif-badge-sidebar" class="nav-badge" style="background: #ef4444; <?= (isset($unreadNotifications) && $unreadNotifications > 0) ? '' : 'display:none;' ?>">
                    <?= $unreadNotifications ?? 0 ?>
                </span>
            </a>
            <a href="<?= base_url('profile') ?>" class="<?= $activePage == 'profile' ? 'active' : '' ?>"><i class="bi bi-person-circle"></i> Profil Saya</a>
            <a href="<?= base_url('logout') ?>" style="color: #f87171;"><i class="bi bi-box-arrow-right"></i> Keluar</a>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="page-title" style="flex:1;">
                <?= $pageTitle ?? 'Halaman' ?>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <a href="<?= base_url('notifications') ?>" style="position:relative;display:inline-flex;align-items:center;color:var(--gray-500);text-decoration:none;" title="Notifikasi">
                    <i id="notif-icon-topbar" class="bi bi-bell<?= (isset($unreadNotifications) && $unreadNotifications > 0) ? '-fill' : '' ?>" style="font-size:18px;<?= (isset($unreadNotifications) && $unreadNotifications > 0) ? 'color:#f59e0b;' : '' ?>"></i>
                    <span id="notif-badge-topbar" style="position:absolute;top:-6px;right:-8px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;min-width:16px;height:16px;border-radius:8px;<?= (isset($unreadNotifications) && $unreadNotifications > 0) ? 'display:flex;' : 'display:none;' ?>align-items:center;justify-content:center;padding:0 3px;line-height:1;">
                        <?= (isset($unreadNotifications) && $unreadNotifications > 99) ? '99+' : ($unreadNotifications ?? 0) ?>
                    </span>
                </a>
                <div class="user-dropdown">
                    <div class="user-info-trigger">
                        <span style="font-size:13px; font-weight:600; color:var(--gray-700);"><?= session()->get('name') ?></span>
                        <i class="bi bi-chevron-down" style="font-size:12px; color:var(--gray-400);"></i>
                    </div>
                    <div class="dropdown-content" style="min-width: 120px;">
                        <a href="<?= base_url('logout') ?>" style="color: #ef4444;"><i class="bi bi-box-arrow-right"></i> Keluar</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" style="background:#D1FAE5;border:1px solid #6EE7B7;color:#065F46;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error" style="background:#FEE2E2;border:1px solid #FCA5A5;color:#991B1B;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<script>
(function() {
    // --- Config from server ---
    const isLoggedIn   = <?= session()->get('id') ? 'true' : 'false' ?>;
    const soundEnabled = <?= (session()->get('notif_sound_enabled') ?? 1) ? 'true' : 'false' ?>;
    const soundType    = "<?= session()->get('notif_sound_type') ?? 'default' ?>";
    const notifCountUrl = "<?= base_url('notifications/unread-count') ?>";
    const logoUrl       = "<?= base_url('images/logo.png') ?>";

    let audioCtx       = null;
    let userInteracted = false;

    function getAudioContext() {
        if (!audioCtx) {
            try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) {}
        }
        return audioCtx;
    }

    window.playBeepSound = function(overrideType) {
        const ctx = getAudioContext();
        if (!ctx) return;
        if (ctx.state === 'suspended') {
            ctx.resume().then(function() { doPlay(ctx, overrideType); });
        } else {
            doPlay(ctx, overrideType);
        }
    };

    function doPlay(ctx, overrideType) {
        const notes = {
            'default': [880, 1100],
            'bell':    [523, 659, 784],
            'beep':    [1000],
            'chime':   [523, 659, 784, 1047]
        };
        const currentType = overrideType || (typeof soundType !== 'undefined' ? soundType : 'default');
        const freqs = notes[currentType] || notes['default'];
        let time = ctx.currentTime;
        freqs.forEach(function(freq, i) {
            const osc  = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = currentType === 'beep' ? 'square' : 'sine';
            osc.frequency.setValueAtTime(freq, time + i * 0.18);
            gain.gain.setValueAtTime(0, time + i * 0.18);
            gain.gain.linearRampToValueAtTime(0.4, time + i * 0.18 + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.001, time + i * 0.18 + 0.35);
            osc.start(time + i * 0.18);
            osc.stop(time + i * 0.18 + 0.4);
        });
    }

    function unlockAudio() {
        if (userInteracted) return;
        const ctx = getAudioContext();
        if (ctx) {
            ctx.resume().then(() => {
                if (ctx.state === 'running') {
                    userInteracted = true;
                    // Play a silent buffer to "prime" the audio on some browsers
                    const buffer = ctx.createBuffer(1, 1, 22050);
                    const source = ctx.createBufferSource();
                    source.buffer = buffer;
                    source.connect(ctx.destination);
                    source.start(0);
                }
            });
        }
    }
    document.addEventListener('click', unlockAudio, { once: false });
    document.addEventListener('keydown', unlockAudio, { once: false });
    document.addEventListener('touchstart', unlockAudio, { once: false });

    // --- Alert auto-dismiss ---
    document.addEventListener('DOMContentLoaded', function() {
        unlockAudio(); // Try to unlock as early as possible
        document.querySelectorAll('.alert').forEach(function(el) {
            setTimeout(function() {
                el.style.transition = 'opacity 0.3s';
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 300);
            }, 4000);
        });
    });

    if (!isLoggedIn) return;

    // --- State ---
    let lastNotifCount = <?= (int)($unreadNotifications ?? 0) ?>;

    // --- Desktop Notification ---
    if ("Notification" in window && Notification.permission === "default") {
        Notification.requestPermission();
    }

    function showDesktopNotification(title, message) {
        if (!("Notification" in window) || Notification.permission !== "granted") return;
        try {
            new Notification(title, { body: message, icon: logoUrl, silent: true });
        } catch(e) {}
    }

    // --- UI Update ---
    function updateUI(count) {
        const baseTitle   = "<?= $pageTitle ?? 'Helpdesk Pusim' ?>";
        const badgeSidebar = document.getElementById('notif-badge-sidebar');
        const badgeTopbar  = document.getElementById('notif-badge-topbar');
        const iconTopbar   = document.getElementById('notif-icon-topbar');

        document.title = (count > 0 ? '(' + count + ') ' : '') + baseTitle;

        if (badgeSidebar) {
            badgeSidebar.innerText = count;
            badgeSidebar.style.display = count > 0 ? 'inline-flex' : 'none';
        }
        if (badgeTopbar && iconTopbar) {
            badgeTopbar.innerText = count > 99 ? '99+' : count;
            badgeTopbar.style.display = count > 0 ? 'flex' : 'none';
            if (count > 0) {
                iconTopbar.classList.remove('bi-bell');
                iconTopbar.classList.add('bi-bell-fill');
                iconTopbar.style.color = '#f59e0b';
            } else {
                iconTopbar.classList.remove('bi-bell-fill');
                iconTopbar.classList.add('bi-bell');
                iconTopbar.style.color = '';
            }
        }
    }

    // --- Poll Handler ---
    function handleResponse(data) {
        const newCount = parseInt(data.count) || 0;
        if (newCount > lastNotifCount) {
            if (soundEnabled) {
                console.log("Playing sound for new notification:", soundType);
                window.playBeepSound(soundType);
            }
            if (data.latest) {
                showDesktopNotification(data.latest.title, data.latest.message);
            }
            
            // Dispatch event for pages that want to react to new notifications
            window.dispatchEvent(new CustomEvent('new-notification', { detail: data }));
        }
        lastNotifCount = newCount;
        updateUI(newCount);
    }

    // --- Fast Polling every 10 seconds ---
    // Uses fetch with keepalive:false to avoid blocking navigation
    function pollNow() {
        fetch(notifCountUrl, { method: 'GET', credentials: 'same-origin', cache: 'no-store' })
            .then(function(res) { return res.json(); })
            .then(handleResponse)
            .catch(function() {}); // silently fail, don't disrupt UI
    }

    // Initial poll after page fully loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(pollNow, 1500);
        setInterval(pollNow, 5000);
    });

})();
</script>

</body>
</html>
