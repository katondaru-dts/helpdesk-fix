<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= (isset($unreadNotifications) && $unreadNotifications > 0) ? '(' . $unreadNotifications . ') ' : '' ?><?= $pageTitle ?? 'Helpdesk Pusim' ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Caveat:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>

<body>

    <div class="app-wrapper">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="<?= base_url('images/logo.png') ?>" alt="Logo"
                    style="width: 42px; height: 42px; border-radius: 6px; object-fit: cover; margin-right: 8px;">
                <span>Helpdesk Pusim</span>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">Menu Utama</div>
                <?php
                $role = session()->get('role_id');
                $isStaff = ($role == 1 || $role == 2 || $role == 4);
                ?>
                <a href="<?= base_url('dashboard') ?>" class="<?= $activePage == 'dashboard' ? 'active' : '' ?>"><i
                        class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="<?= base_url('tickets') ?>" class="<?= $activePage == 'tickets' ? 'active' : '' ?>"><i
                        class="bi bi-ticket-detailed"></i> <?= $isStaff ? 'Semua Tiket' : 'Tiket Saya' ?></a>

                <?php if (has_permission('Buat Tiket') || $role == 4): ?>
                    <a href="<?= base_url('tickets/create') ?>"
                        class="<?= $activePage == 'ticket-create' ? 'active' : '' ?>"><i class="bi bi-plus-circle"></i> Buat
                        Tiket</a>
                <?php endif; ?>

                <?php if ($isStaff): ?>
                    <div class="nav-section" style="margin-top: 16px;">Administrasi</div>
                    <?php if (has_permission('Kelola User')): ?>
                        <a href="<?= base_url('admin/users') ?>" class="<?= $activePage == 'admin-users' ? 'active' : '' ?>"><i
                                class="bi bi-people"></i> Kelola User</a>
                    <?php endif; ?>
                    <?php if (has_permission('Kelola Departemen')): ?>
                        <a href="<?= base_url('admin/departments') ?>"
                            class="<?= $activePage == 'admin-departments' ? 'active' : '' ?>"><i class="bi bi-building"></i>
                            Departemen</a>
                    <?php endif; ?>
                    <?php if (has_permission('Kelola Kategori')): ?>
                        <a href="<?= base_url('admin/categories') ?>"
                            class="<?= $activePage == 'admin-categories' ? 'active' : '' ?>"><i class="bi bi-tag"></i>
                            Kategori</a>
                    <?php endif; ?>
                    <?php if (has_permission('Kelola Role')): ?>
                        <a href="<?= base_url('admin/roles') ?>" class="<?= $activePage == 'admin-roles' ? 'active' : '' ?>"><i
                                class="bi bi-shield-check"></i> Role & Izin</a>
                    <?php endif; ?>
                    <?php if (has_permission('Lihat Laporan')): ?>
                        <a href="<?= base_url('admin/reports') ?>"
                            class="<?= $activePage == 'admin-reports' ? 'active' : '' ?>"><i class="bi bi-graph-up"></i>
                            Laporan</a>
                    <?php endif; ?>
                    <?php if (has_permission('Lihat Audit Log')): ?>
                        <a href="<?= base_url('admin/audit-logs') ?>"
                            class="<?= $activePage == 'admin-audit-logs' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i>
                            Audit Log</a>
                    <?php endif; ?>
                    <a href="<?= base_url('admin/knowledge-base') ?>"
                        class="<?= $activePage == 'admin-kb' ? 'active' : '' ?>"><i class="bi bi-book-half"></i> Kelola
                        KB</a>
                <?php endif; ?>

                <div class="nav-section" style="margin-top: 16px;">Akun</div>
                <a href="<?= base_url('knowledge-base') ?>"
                    class="<?= $activePage == 'knowledge-base' ? 'active' : '' ?>"><i class="bi bi-book"></i> Knowledge
                    Base</a>
                <a href="<?= base_url('notifications') ?>"
                    class="<?= $activePage == 'notifications' ? 'active' : '' ?>">
                    <i class="bi bi-bell"></i> Notifikasi
                    <span id="notif-badge-sidebar" class="nav-badge"
                        style="background: #ef4444; <?= (isset($unreadNotifications) && $unreadNotifications > 0) ? '' : 'display:none;' ?>">
                        <?= $unreadNotifications ?? 0 ?>
                    </span>
                </a>
                <a href="<?= base_url('profile') ?>" class="<?= $activePage == 'profile' ? 'active' : '' ?>"><i
                        class="bi bi-person-circle"></i> Profil Saya</a>
                <a href="<?= base_url('logout') ?>" style="color: #f87171;"><i class="bi bi-box-arrow-right"></i>
                    Keluar</a>
            </nav>
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <button class="btn-icon sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="page-title" style="flex:1;">
                    <?= $pageTitle ?? 'Halaman' ?>
                </div>
                <div style="display:flex;align-items:center;gap:12px;">
                    <a href="<?= base_url('notifications') ?>"
                        style="position:relative;display:inline-flex;align-items:center;color:var(--gray-500);text-decoration:none;"
                        title="Notifikasi">
                        <i id="notif-icon-topbar"
                            class="bi bi-bell<?= (isset($unreadNotifications) && $unreadNotifications > 0) ? '-fill' : '' ?>"
                            style="font-size:18px;<?= (isset($unreadNotifications) && $unreadNotifications > 0) ? 'color:#f59e0b;' : '' ?>"></i>
                        <span id="notif-badge-topbar"
                            style="position:absolute;top:-6px;right:-8px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;min-width:16px;height:16px;border-radius:8px;<?= (isset($unreadNotifications) && $unreadNotifications > 0) ? 'display:flex;' : 'display:none;' ?>align-items:center;justify-content:center;padding:0 3px;line-height:1;">
                            <?= (isset($unreadNotifications) && $unreadNotifications > 99) ? '99+' : ($unreadNotifications ?? 0) ?>
                        </span>
                    </a>
                    <div class="user-dropdown">
                        <div class="user-info-trigger">
                            <span
                                style="font-size:13px; font-weight:600; color:var(--gray-700);"><?= session()->get('name') ?></span>
                            <i class="bi bi-chevron-down" style="font-size:12px; color:var(--gray-400);"></i>
                        </div>
                        <div class="dropdown-content" style="min-width: 120px;">
                            <a href="<?= base_url('logout') ?>" style="color: #ef4444;"><i
                                    class="bi bi-box-arrow-right"></i> Keluar</a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="main-content">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"
                        style="background:#D1FAE5;border:1px solid #6EE7B7;color:#065F46;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error"
                        style="background:#FEE2E2;border:1px solid #FCA5A5;color:#991B1B;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ── AI CHAT WIDGET ── -->
    <style>
        .ai-fab-wrap {
            position: fixed;
            bottom: 28px;
            right: 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            z-index: 9999;
        }

        .ai-fab-label {
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            animation: aiPopIn .3s cubic-bezier(.34, 1.56, .64, 1);
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, .1));
        }

        .ai-fab-label button {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(0, 0, 0, .08);
            color: #64748B;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            padding: 0;
            line-height: 1;
            transition: background .15s, color .15s;
        }

        .ai-fab-label button:hover {
            background: rgba(0, 0, 0, .15);
            color: #1E293B;
        }

        .ai-fab {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 20px rgba(37, 99, 235, .45);
            transition: transform .2s, box-shadow .2s;
        }

        .ai-fab:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 28px rgba(37, 99, 235, .55);
        }

        .ai-window {
            position: fixed;
            bottom: 94px;
            right: 28px;
            width: 440px;
            height: 680px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, .18);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 999;
            animation: aiPopIn .25s cubic-bezier(.34, 1.56, .64, 1);
        }

        @keyframes aiPopIn {
            from {
                transform: scale(.85) translateY(20px);
                opacity: 0;
            }

            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        .ai-header {
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .ai-header-av {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #fff;
            flex-shrink: 0;
        }

        .ai-header-info .name {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .ai-header-info .status {
            font-size: 11px;
            color: rgba(255, 255, 255, .8);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .ai-header-info .status::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #4ADE80;
            border-radius: 50%;
            display: inline-block;
        }

        .ai-chips {
            padding: 8px 12px;
            background: #F8FAFC;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .ai-chip {
            padding: 3px 9px;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            font-size: 11.5px;
            color: #2563EB;
            cursor: pointer;
            font-weight: 500;
            transition: all .15s;
            white-space: nowrap;
        }

        .ai-chip:hover {
            background: #DBEAFE;
            border-color: #2563EB;
        }

        .ai-msgs {
            flex: 1;
            overflow-y: auto;
            padding: 14px 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ai-msgs::-webkit-scrollbar {
            width: 4px;
        }

        .ai-msgs::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }

        .ai-msg {
            display: flex;
            gap: 7px;
            max-width: 90%;
        }

        .ai-msg.bot {
            align-self: flex-start;
        }

        .ai-msg.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .ai-msg-av {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #fff;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .ai-bubble {
            padding: 11px 14px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.7;
        }

        .ai-msg.bot .ai-bubble {
            background: #F1F5F9;
            color: #1E293B;
            border-bottom-left-radius: 4px;
            max-width: 100%;
            word-break: break-word;
            text-align: left;
        }
        .ai-msg.bot .ai-bubble br + br {
            display: block;
            content: '';
            margin-top: 6px;
        }
        .ai-msg.bot .ai-bubble li {
            margin-bottom: 4px;
        }
        .ai-msg.bot .ai-bubble strong {
            display: inline;
            font-weight: 600;
        }
        .ai-msg.bot .ai-bubble strong.ai-heading {
            display: block;
            margin-top: 8px;
            margin-bottom: 2px;
        }

        .ai-msg.user .ai-bubble {
            background: #2563EB;
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .ai-source {
            margin-top: 6px;
            padding: 6px 9px;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 7px;
            font-size: 11.5px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .ai-source a {
            color: #2563EB;
            font-weight: 500;
            text-decoration: underline;
        }

        .ai-typing {
            display: flex;
            gap: 4px;
            align-items: center;
            padding: 9px 12px;
            background: #F1F5F9;
            border-radius: 14px;
            border-bottom-left-radius: 4px;
            width: fit-content;
        }

        .ai-typing span {
            width: 6px;
            height: 6px;
            background: #94A3B8;
            border-radius: 50%;
            animation: aiBounce 1.2s infinite;
        }

        .ai-typing span:nth-child(2) {
            animation-delay: .2s;
        }

        .ai-typing span:nth-child(3) {
            animation-delay: .4s;
        }

        @keyframes aiBounce {

            0%,
            60%,
            100% {
                transform: translateY(0);
            }

            30% {
                transform: translateY(-5px);
            }
        }

        .ai-input-area {
            padding: 10px 12px;
            border-top: 1px solid #E2E8F0;
            background: #fff;
            flex-shrink: 0;
        }

        .ai-input-row {
            display: flex;
            align-items: flex-end;
            gap: 7px;
            background: #F1F5F9;
            border-radius: 12px;
            padding: 7px 9px 7px 12px;
            border: 1.5px solid #E2E8F0;
            transition: border-color .2s;
        }

        .ai-input-row:focus-within {
            border-color: #2563EB;
        }

        .ai-input-row textarea {
            flex: 1;
            border: none;
            background: transparent;
            resize: none;
            font-family: inherit;
            font-size: 13px;
            color: #1E293B;
            outline: none;
            max-height: 70px;
            line-height: 1.5;
        }

        .ai-input-row textarea::placeholder {
            color: #94A3B8;
        }

        .ai-send {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: #2563EB;
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            transition: background .15s;
        }

        .ai-send:hover {
            background: #1D4ED8;
        }

        .ai-hint {
            font-size: 10.5px;
            color: #94A3B8;
            text-align: center;
            margin-top: 5px;
        }

        .ai-model-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 10px;
            background: #EFF6FF;
            color: #2563EB;
            border-radius: 5px;
            padding: 2px 6px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        @media (max-width: 480px) {
            .ai-window {
                width: calc(100vw - 20px);
                right: 10px;
                bottom: 80px;
                height: 65vh;
            }

            .ai-fab-wrap {
                bottom: 18px;
                right: 14px;
            }
        }
    </style>

    <div class="ai-fab-wrap">
        <div class="ai-fab-label" id="aiFabLabel">
            <svg width="130" height="54" viewBox="0 0 130 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- garis lengkung kecil bawah teks (pena) -->
                <path d="M18 40 Q65 48 112 40" stroke="#94A3B8" stroke-width="1.2" stroke-linecap="round" fill="none" />
                <!-- teks kursif tinta hitam -->
                <text x="65" y="31" text-anchor="middle" font-family="'Caveat', 'Segoe Script', cursive" font-size="26"
                    font-weight="700" fill="#1E293B">Let's Talk</text>
                <!-- titik-titik di belakang teks -->
                <circle cx="108" cy="22" r="2.2" fill="#94A3B8" opacity=".7" />
                <circle cx="116" cy="22" r="1.6" fill="#94A3B8" opacity=".5" />
                <circle cx="122" cy="22" r="1.1" fill="#94A3B8" opacity=".35" />
            </svg>
            <button onclick="document.getElementById('aiFabLabel').style.display='none'" title="Tutup"><i
                    class="bi bi-x"></i></button>
        </div>
        <button class="ai-fab" id="aiFab" title="Tanya AI Assistant">
            <i class="bi bi-stars" id="aiFabIcon"></i>
        </button>
    </div>

    <div class="ai-window" id="aiWindow">
        <div class="ai-header">
            <div class="ai-header-av"><i class="bi bi-stars"></i></div>
            <div class="ai-header-info" style="flex:1">
                <div class="name">Helpdesk AI Pusim</div>
                <div class="status" id="aiStatusLabel">Powered by Gemini</div>
            </div>
            <button onclick="toggleAiChat()"
                style="width:28px;height:28px;border:none;background:rgba(255,255,255,.15);border-radius:7px;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;margin-left:6px"><i
                    class="bi bi-x-lg"></i></button>
        </div>
        <div class="ai-chips">
            <span class="ai-chip" onclick="aiAsk(this)">📋 Cara buat tiket</span>
            <span class="ai-chip" onclick="aiAsk(this)">🔑 Reset password</span>
            <span class="ai-chip" onclick="aiAsk(this)">📶 Masalah WiFi</span>
            <span class="ai-chip" onclick="aiAsk(this)">🖨️ Printer error</span>
        </div>
        <div class="ai-msgs" id="aiMsgs">
            <div class="ai-msg bot">
                <div class="ai-msg-av"><i class="bi bi-stars"></i></div>
                <div>
                    <div class="ai-bubble">Halo! Saya <span style="font-weight:600">Helpdesk AI Pusim</span> 👋<br>Tanya apa saja seputar layanan helpdesk, saya siap membantu!</div>
                </div>
            </div>
            <div class="ai-msg bot" id="aiTyping" style="display:none">
                <div class="ai-msg-av"><i class="bi bi-stars"></i></div>
                <div class="ai-typing"><span></span><span></span><span></span></div>
            </div>
        </div>
        <div class="ai-input-area">
            <div class="ai-input-row">
                <textarea id="aiInput" rows="1" placeholder="Ketik pertanyaan Anda..."></textarea>
                <button class="ai-send" id="aiSendBtn"><i class="bi bi-send-fill"></i></button>
            </div>
            <div class="ai-hint">Jawaban berdasarkan Knowledge Base Helpdesk Pusim</div>
        </div>
    </div>

    <script>
        (function () {
            const fab = document.getElementById('aiFab');
            const win = document.getElementById('aiWindow');
            const fabIcon = document.getElementById('aiFabIcon');
            const msgs = document.getElementById('aiMsgs');
            const typing = document.getElementById('aiTyping');
            const input = document.getElementById('aiInput');
            const sendBtn = document.getElementById('aiSendBtn');

            const chatUrl = '<?= base_url('ai/chat') ?>';
            const kbUrl = '<?= base_url('knowledge-base/') ?>';
            const ticketUrl = '<?= base_url('tickets/create') ?>';

            // ── Buka / tutup window ──
            function setOpen(open) {
                win.style.display = open ? 'flex' : 'none';
                fabIcon.className = open ? 'bi bi-x-lg' : 'bi bi-stars';
            }
            window.openAiChat = function () { setOpen(true); };
            window.toggleAiChat = function () { setOpen(win.style.display !== 'flex'); };
            fab.addEventListener('click', window.toggleAiChat);

            input.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 70) + 'px';
            });

            function disableAllFeedbackBtns() {
                msgs.querySelectorAll('.ai-feedback-btn').forEach(function (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.4';
                    btn.style.cursor = 'default';
                });
            }

            function addMsg(role, html, sources, showFeedback, suggestTicket, modelUsed) {
                const div = document.createElement('div');
                div.className = 'ai-msg ' + role;
                let inner = role === 'bot' ? '<div class="ai-msg-av"><i class="bi bi-stars"></i></div>' : '';
                inner += '<div>';

                // Badge nama model (hanya untuk pesan bot yang dari API)
                if (role === 'bot' && modelUsed) {
                    inner += '<div class="ai-model-badge"><i class="bi bi-cpu"></i> ' + modelUsed + '</div>';
                }

                inner += '<div class="ai-bubble">' + html + '</div>';

                if (sources && sources.length) {
                    var s = sources[0];
                    inner += '<div class="ai-source"><i class="bi bi-book" style="color:#2563EB"></i> Sumber: <a href="' + kbUrl + (s.slug || '') + '" target="_blank">' + s.title + '</a></div>';
                }

                if (showFeedback) {
                    inner += '<div class="ai-feedback" style="margin-top:8px;display:flex;align-items:center;gap:6px;flex-wrap:wrap">'
                        + '<span style="font-size:12px;color:#64748B">Apakah jawaban ini membantu?</span>'
                        + '<button class="ai-feedback-btn" onclick="aiFeedback(this,true)" style="padding:3px 10px;border-radius:6px;border:1px solid #22c55e;background:#f0fdf4;color:#16a34a;font-size:12px;cursor:pointer;font-weight:600">Ya</button>'
                        + '<button class="ai-feedback-btn" onclick="aiFeedback(this,false)" style="padding:3px 10px;border-radius:6px;border:1px solid #ef4444;background:#fef2f2;color:#dc2626;font-size:12px;cursor:pointer;font-weight:600">Tidak</button>'
                        + '</div>';
                }

                if (suggestTicket) {
                    inner += '<div style="margin-top:8px;padding:8px 10px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;font-size:12px;color:#1D4ED8">'
                        + '<i class="bi bi-info-circle"></i> Masalah ini sepertinya butuh penanganan langsung dari tim kami.'
                        + '<div style="margin-top:6px"><a href="' + ticketUrl + '" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#2563EB;color:#fff;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none"><i class="bi bi-plus-circle"></i> Buat Tiket Sekarang</a></div>'
                        + '</div>';
                }

                inner += '</div>';
                div.innerHTML = inner;
                msgs.insertBefore(div, typing);
                msgs.scrollTop = msgs.scrollHeight;
            }

            window.aiFeedback = function (btn, satisfied) {
                disableAllFeedbackBtns();
                if (satisfied) {
                    btn.style.opacity = '1'; btn.style.background = '#22c55e'; btn.style.color = '#fff';
                    addMsg('bot', 'Senang bisa membantu! 😊 Jika ada pertanyaan lain, silakan tanya kapan saja.', null, false, false);
                } else {
                    btn.style.opacity = '1'; btn.style.background = '#ef4444'; btn.style.color = '#fff';
                    addMsg('bot', 'Maaf jawaban saya kurang membantu 🙏<br>Yuk buat tiket agar tim kami bisa membantu langsung!', null, false, true);
                }
            };

            function sendMsg() {
                const text = input.value.trim();
                if (!text) return;
                disableAllFeedbackBtns();
                addMsg('user', text.replace(/</g, '&lt;'), null, false);
                input.value = ''; input.style.height = 'auto';
                typing.style.display = 'flex';
                msgs.scrollTop = msgs.scrollHeight;

                fetch(chatUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ message: text })
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        typing.style.display = 'none';
                        const answer = parseMd(data.answer || 'Maaf, terjadi kesalahan.');
                        addMsg('bot', answer, data.sources || [], true, data.suggest_ticket || false, data.model_used || null);
                    })
                    .catch(function () {
                        typing.style.display = 'none';
                        addMsg('bot', 'Maaf, tidak dapat terhubung ke AI saat ini.', null, false, true);
                    });
            }

            function parseMd(text) {
                text = text.replace(/^[ \t]+/gm, '').trim();
                // Proses inline formatting dulu
                text = text
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                    .replace(/^### (.+)$/gm, '<strong class="ai-heading" style="font-size:13px">$1</strong>')
                    .replace(/^## (.+)$/gm, '<strong class="ai-heading" style="font-size:14px">$1</strong>')
                    .replace(/^# (.+)$/gm, '<strong class="ai-heading" style="font-size:15px">$1</strong>')
                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.+?)\*/g, '<em>$1</em>')
                    .replace(/_(.+?)_/g, '<em>$1</em>')
                    .replace(/`([^`]+)`/g, '<code style="background:#e2e8f0;padding:1px 5px;border-radius:4px;font-size:12px">$1</code>')
                    .replace(/^\s*[-*] (.+)$/gm, '<li style="margin-left:16px;list-style:disc">$1</li>')
                    .replace(/^\s*\d+\. (.+)$/gm, '<li style="margin-left:16px;list-style:decimal">$1</li>');
                // Bungkus tiap blok paragraf (dipisah double newline) dengan <p>
                return text.split(/\n{2,}/)
                    .map(function(p) { return '<p style="margin:0 0 8px 0">' + p.replace(/\n/g, '<br>') + '</p>'; })
                    .join('');
            }

            sendBtn.addEventListener('click', sendMsg);
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); }
            });
            window.aiAsk = function (chip) { input.value = chip.textContent.replace(/^[^\w]+/, '').trim(); sendMsg(); };
        })();
    </script>

    <script>
        (function () {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (toggle && sidebar && overlay) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('show');
                });
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                });
            }
        })();
    </script>

    <script>
        (function () {
            // --- Config from server ---
            const isLoggedIn = <?= session()->get('id') ? 'true' : 'false' ?>;
            const soundEnabled = <?= (session()->get('notif_sound_enabled') ?? 1) ? 'true' : 'false' ?>;
            const soundType = "<?= session()->get('notif_sound_type') ?? 'default' ?>";
            const notifCountUrl = "<?= base_url('notifications/unread-count') ?>";
            const logoUrl = "<?= base_url('images/logo.png') ?>";

            let audioCtx = null;
            let userInteracted = false;

            function getAudioContext() {
                if (!audioCtx) {
                    try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch (e) { }
                }
                return audioCtx;
            }

            window.playBeepSound = function (overrideType) {
                const ctx = getAudioContext();
                if (!ctx) return;
                if (ctx.state === 'suspended') {
                    ctx.resume().then(function () { doPlay(ctx, overrideType); });
                } else {
                    doPlay(ctx, overrideType);
                }
            };

            function doPlay(ctx, overrideType) {
                const notes = {
                    'default': [880, 1100],
                    'bell': [523, 659, 784],
                    'beep': [1000],
                    'chime': [523, 659, 784, 1047]
                };
                const currentType = overrideType || (typeof soundType !== 'undefined' ? soundType : 'default');
                const freqs = notes[currentType] || notes['default'];
                let time = ctx.currentTime;
                freqs.forEach(function (freq, i) {
                    const osc = ctx.createOscillator();
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
            document.addEventListener('DOMContentLoaded', function () {
                unlockAudio(); // Try to unlock as early as possible
                document.querySelectorAll('.alert').forEach(function (el) {
                    setTimeout(function () {
                        el.style.transition = 'opacity 0.3s';
                        el.style.opacity = '0';
                        setTimeout(function () { el.remove(); }, 300);
                    }, 4000);
                });
            });

            if (!isLoggedIn) return;

            // --- State ---
            let lastNotifCount = <?= (int) ($unreadNotifications ?? 0) ?>;

            // --- Desktop Notification ---
            if ("Notification" in window && Notification.permission === "default") {
                Notification.requestPermission();
            }

            function showDesktopNotification(title, message) {
                if (!("Notification" in window) || Notification.permission !== "granted") return;
                try {
                    new Notification(title, { body: message, icon: logoUrl, silent: true });
                } catch (e) { }
            }

            // --- UI Update ---
            function updateUI(count) {
                const baseTitle = "<?= $pageTitle ?? 'Helpdesk Pusim' ?>";
                const badgeSidebar = document.getElementById('notif-badge-sidebar');
                const badgeTopbar = document.getElementById('notif-badge-topbar');
                const iconTopbar = document.getElementById('notif-icon-topbar');

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
                        window.playBeepSound(soundType);
                    }
                    if (data.latest) {
                        showDesktopNotification(data.latest.title, data.latest.message);
                    }
                    window.dispatchEvent(new CustomEvent('new-notification', { detail: data }));
                }
                lastNotifCount = newCount;
                updateUI(newCount);
            }

            // --- BroadcastChannel: hanya 1 tab yang poll, tab lain terima hasilnya ---
            const POLL_INTERVAL = 30000; // 30 detik
            let pollTimer = null;
            let isLeader = false;

            const bc = (typeof BroadcastChannel !== 'undefined')
                ? new BroadcastChannel('notif_poll')
                : null;

            if (bc) {
                bc.onmessage = function (e) {
                    if (e.data && e.data.type === 'notif_data') {
                        handleResponse(e.data.payload);
                    }
                };
            }

            function pollNow() {
                fetch(notifCountUrl, { method: 'GET', credentials: 'same-origin', cache: 'no-store' })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        handleResponse(data);
                        // Broadcast ke tab lain agar tidak perlu poll sendiri
                        if (bc) bc.postMessage({ type: 'notif_data', payload: data });
                    })
                    .catch(function () { });
            }

            function startPolling() {
                isLeader = true;
                pollNow();
                pollTimer = setInterval(pollNow, POLL_INTERVAL);
            }

            // Leader election sederhana via localStorage timestamp
            const LEADER_KEY = 'notif_leader_ts';
            const LEADER_TTL = 35000; // ms

            function tryBecomeLeader() {
                const now = Date.now();
                const ts = parseInt(localStorage.getItem(LEADER_KEY) || '0');
                if (now - ts > LEADER_TTL) {
                    localStorage.setItem(LEADER_KEY, now);
                    if (!isLeader) startPolling();
                } else if (!isLeader) {
                    // Bukan leader — update UI dari nilai awal saja
                    updateUI(lastNotifCount);
                }
            }

            // Renew kepemimpinan setiap interval
            function renewLeader() {
                if (isLeader) localStorage.setItem(LEADER_KEY, Date.now());
            }

            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    tryBecomeLeader();
                    setInterval(function () {
                        tryBecomeLeader();
                        renewLeader();
                    }, POLL_INTERVAL);
                }, 1500);
            });

            // Saat tab ditutup, hapus klaim leader agar tab lain bisa ambil alih
            window.addEventListener('beforeunload', function () {
                if (isLeader) localStorage.removeItem(LEADER_KEY);
            });

        })();
    </script>

</body>

</html>
