// ============================================================
// UI.JS - Toast, Modal, Helpers
// ============================================================

// ── Toast ──
function toast(msg, type = 'info', dur = 3500) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', info: 'bi-info-circle-fill' };
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `<i class="bi ${icons[type] || icons.info}"></i><span class="toast-msg">${msg}</span>`;
  container.appendChild(el);
  setTimeout(() => el.remove(), dur);
}

// ── Confirm Modal ──
function confirmModal(title, msg, onConfirm, danger = true) {
  const id = 'modal-confirm-' + Date.now();
  const overlay = document.createElement('div');
  overlay.className = 'modal-overlay'; overlay.id = id;
  overlay.innerHTML = `
    <div class="modal-box">
      <div class="modal-title">${title}</div>
      <p style="color:var(--gray-600);font-size:14px">${msg}</p>
      <div class="modal-actions">
        <button class="btn btn-outline" onclick="document.getElementById('${id}').remove()">Batal</button>
        <button class="btn ${danger ? 'btn-danger' : 'btn-primary'}" id="mc-ok-${id}">Ya, Lanjutkan</button>
      </div>
    </div>`;
  document.body.appendChild(overlay);
  document.getElementById('mc-ok-' + id).onclick = () => { overlay.remove(); onConfirm(); };
}

// ── Date Formatters ──
function fmtDate(dt) {
  if (!dt) return '-';
  return new Date(dt).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
function fmtDateTime(dt) {
  if (!dt) return '-';
  return new Date(dt).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
function fmtAge(dt) {
  if (!dt) return '-';
  const diff = Date.now() - new Date(dt).getTime();
  const mins = Math.floor(diff / 60000), hrs = Math.floor(mins / 60), days = Math.floor(hrs / 24);
  if (days > 0) return days + 'h lalu';
  if (hrs > 0) return hrs + ' jam lalu';
  if (mins > 0) return mins + ' mnt lalu';
  return 'Baru saja';
}
function timeAgo(dt) { return fmtAge(dt); }
function isoNow() { return new Date().toISOString(); }

// ── Pagination ──
function renderPagination(containerId, total, perPage, currentPage, onChange) {
  const el = document.getElementById(containerId);
  if (!el) return;
  const pages = Math.ceil(total / perPage);
  if (pages <= 1) { el.innerHTML = ''; return; }
  let html = '<div class="pagination">';
  html += `<button class="pg-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="(${onChange})(${currentPage - 1})"><i class="bi bi-chevron-left"></i></button>`;
  for (let i = 1; i <= pages; i++) {
    html += `<button class="pg-btn ${i === currentPage ? 'active' : ''}" onclick="(${onChange})(${i})">${i}</button>`;
  }
  html += `<button class="pg-btn" ${currentPage === pages ? 'disabled' : ''} onclick="(${onChange})(${currentPage + 1})"><i class="bi bi-chevron-right"></i></button>`;
  html += '</div>';
  el.innerHTML = html;
}

// ── Build Sidebar HTML ──
function buildSidebar(activeUser) {
  const isStaff = activeUser.role_id === 1 || activeUser.role_id === 2;
  const isAdmin = activeUser.role_id === 1;
  const initials = activeUser.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
  return `
    <div class="sidebar-overlay"></div>
    <aside class="sidebar">
      <div class="sidebar-brand"><i class="bi bi-headset"></i> ${APP.name}</div>
      <nav class="sidebar-nav">
        <div class="nav-section">Menu Utama</div>
        ${!isStaff ? `<a href="dashboard-user.php" data-page="dashboard-user"><i class="bi bi-grid-fill"></i> Dashboard</a>` : ''}
        ${isStaff ? `<a href="dashboard-admin.php" data-page="dashboard-admin"><i class="bi bi-speedometer2"></i> Dashboard</a>` : ''}
        <a href="ticket-list.php" data-page="ticket-list"><i class="bi bi-ticket-detailed"></i> ${isStaff ? 'Semua Tiket' : 'Tiket Saya'}</a>
        ${!isStaff ? `<a href="ticket-create.php" data-page="ticket-create"><i class="bi bi-plus-circle"></i> Buat Tiket</a>` : ''}
        <a href="notification.php" data-page="notification"><i class="bi bi-bell"></i> Notifikasi <span class="nav-badge notif-badge" style="display:none"></span></a>
        ${isStaff ? `
        <div class="nav-section">Administrasi</div>
        <a href="report.php" data-page="report"><i class="bi bi-bar-chart-fill"></i> Laporan</a>
        ${isAdmin ? `
        <a href="user-management.php" data-page="user-management"><i class="bi bi-people-fill"></i> Kelola User</a>
        <a href="department-management.php" data-page="department-management"><i class="bi bi-building"></i> Departemen</a>
        <a href="category-management.php" data-page="category-management"><i class="bi bi-tags-fill"></i> Kategori</a>
        <a href="role-management.php" data-page="role-management"><i class="bi bi-shield-fill"></i> Role</a>
        `: ''}
        `: ''}
        <div class="nav-section">Akun</div>
        <a href="profile.php" data-page="profile"><i class="bi bi-person-circle"></i> Profil Saya</a>
      </nav>
      <div class="sidebar-footer">
        <div class="user-card" onclick="window.location='profile.php'">
          <div class="avatar user-avatar">${initials}</div>
          <div style="min-width:0">
            <div class="un truncate user-name">${activeUser.name}</div>
            <div class="ur user-role-lbl">${activeUser.role_name}</div>
          </div>
          <i class="bi bi-box-arrow-right ms-auto" style="color:var(--sidebar-text);font-size:16px" onclick="event.stopPropagation();logout()" title="Logout"></i>
        </div>
      </div>
    </aside>`;
}

// ── Build Topbar HTML ──
function buildTopbar(title, activeUser) {
  const initials = activeUser.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
  const cnt = unreadCount(activeUser.id);
  return `
    <header class="topbar">
      <button class="btn-icon sidebar-toggle"><i class="bi bi-list"></i></button>
      <span class="page-title">${title}</span>
      <div class="d-flex ai-center gap-2">
        <button class="btn-icon" onclick="window.location='notification.php'" title="Notifikasi">
          <i class="bi bi-bell"></i>
          ${cnt ? `<span class="notif-dot"></span>` : ''}
        </button>
        <div class="avatar sm user-avatar" style="cursor:pointer" onclick="window.location='profile.php'">${initials}</div>
      </div>
    </header>`;
}

// ── Init Page (inject sidebar + topbar) ──
function initPage(pageKey, pageTitle, requiredRole = 'any') {
  let u;
  if (requiredRole === 'admin') u = requireAdmin();
  else if (requiredRole === 'superadmin') u = requireSuperAdmin();
  else u = requireAuth();
  if (!u) return null;

  const wrap = document.getElementById('app-wrap');
  if (wrap) {
    wrap.innerHTML = buildSidebar(u) + `<div class="main-wrapper">` + buildTopbar(pageTitle, u) + `<main class="main-content" id="main-content"></main></div>`;
  }

  // Set active nav
  document.querySelectorAll('.sidebar-nav a').forEach(a => {
    if (a.dataset.page === pageKey) a.classList.add('active');
  });

  // Update notif badges
  const cnt = unreadCount(u.id);
  document.querySelectorAll('.notif-badge').forEach(el => { el.textContent = cnt; el.style.display = cnt ? '' : 'none'; });

  // Sidebar toggle
  const toggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  if (toggle && sidebar) {
    toggle.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay && overlay.classList.toggle('show'); });
    overlay && overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });
  }
  return u;
}

// ── Empty State HTML ──
function emptyState(icon, title, desc, btnHtml = '') {
  return `<div class="empty-state"><i class="bi ${icon}"></i><h4>${title}</h4><p>${desc}</p>${btnHtml}</div>`;
}

// ── Stars HTML ──
function starsHtml(rating, max = 5) {
  let h = '';
  for (let i = 1; i <= max; i++) h += `<i class="bi bi-star${i <= rating ? '-fill' : ''}" style="color:${i <= rating ? '#F59E0B' : '#CBD5E1'};font-size:14px"></i>`;
  return h;
}
