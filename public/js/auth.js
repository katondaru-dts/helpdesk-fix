// ============================================================
// AUTH.JS - Session & Authentication
// ============================================================
const SESSION_KEY = 'hd2_session';

function saveSession(u) { sessionStorage.setItem(SESSION_KEY, JSON.stringify(u)); }
function getSession() { try { return JSON.parse(sessionStorage.getItem(SESSION_KEY)); } catch { return null; } }
function clearSession() { sessionStorage.removeItem(SESSION_KEY); }

function requireAuth() {
    const u = getSession();
    if (!u) { window.location.href = 'index.html'; return null; }
    return u;
}
function requireAdmin() {
    const u = requireAuth();
    if (!u) return null;
    if (u.role_id !== 1 && u.role_id !== 2) { window.location.href = 'dashboard-user.html'; return null; }
    return u;
}
function requireSuperAdmin() {
    const u = requireAuth();
    if (!u) return null;
    if (u.role_id !== 1) { window.location.href = 'dashboard-admin.html'; return null; }
    return u;
}
function redirectIfLoggedIn() {
    const u = getSession();
    if (!u) return;
    window.location.href = (u.role_id === 1 || u.role_id === 2) ? 'dashboard-admin.html' : 'dashboard-user.html';
}

function login(email, password) {
    const users = getUsers();
    const u = users.find(x => x.email.toLowerCase() === email.toLowerCase().trim());
    if (!u) return { ok: false, msg: 'Email tidak ditemukan.' };
    if (!u.is_active) return { ok: false, msg: 'Akun dinonaktifkan. Hubungi admin.' };
    if (u.password !== password) return { ok: false, msg: 'Password salah.' };
    const role = getRoleById(u.role_id);
    const dept = getDeptById(u.dept_id);
    const sess = {
        id: u.id, name: u.name, email: u.email, role_id: u.role_id,
        role_name: role ? role.name : 'User', dept_id: u.dept_id,
        dept_name: dept ? dept.name : '-', gender: u.gender, phone: u.phone
    };
    saveSession(sess);
    return { ok: true, user: sess };
}

function register(data) {
    const { name, email, password, role_id, dept_id, gender, phone, birth_date } = data;
    if (!name || !email || !password || !role_id || !dept_id || !gender)
        return { ok: false, msg: 'Semua field bertanda * wajib diisi.' };
    if (password.length < 6) return { ok: false, msg: 'Password minimal 6 karakter.' };
    const users = getUsers();
    if (users.find(u => u.email.toLowerCase() === email.toLowerCase().trim()))
        return { ok: false, msg: 'Email sudah terdaftar.' };
    const newUser = {
        id: nextUserId(), name: name.trim(), email: email.toLowerCase().trim(),
        password, role_id: parseInt(role_id), dept_id: parseInt(dept_id),
        gender, phone: phone || '', birth_date: birth_date || '', is_active: 1,
        created_at: new Date().toISOString()
    };
    users.push(newUser);
    saveUsers(users);
    return { ok: true, msg: 'Registrasi berhasil! Silakan login.' };
}

function logout() { clearSession(); window.location.href = 'index.html'; }

// ── UI Setup Sidebar ──
function setupSidebar(activePage) {
    const u = getSession();
    if (!u) return;
    // Avatar initial
    const initials = u.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
    document.querySelectorAll('.user-avatar').forEach(el => {
        el.textContent = initials;
    });
    document.querySelectorAll('.user-name').forEach(el => el.textContent = u.name);
    document.querySelectorAll('.user-role-lbl').forEach(el => el.textContent = u.role_name);

    // Show/hide admin nav
    if (u.role_id === 1 || u.role_id === 2) {
        document.querySelectorAll('.admin-only').forEach(el => el.style.display = '');
    } else {
        document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'none');
    }
    if (u.role_id === 1) {
        document.querySelectorAll('.superadmin-only').forEach(el => el.style.display = '');
    } else {
        document.querySelectorAll('.superadmin-only').forEach(el => el.style.display = 'none');
    }

    // Active page
    document.querySelectorAll('.sidebar-nav a').forEach(a => {
        if (a.dataset.page === activePage) a.classList.add('active');
    });

    // Notif badge
    const cnt = unreadCount(u.id);
    document.querySelectorAll('.notif-badge').forEach(el => {
        el.textContent = cnt; el.style.display = cnt ? '' : 'none';
    });
    document.querySelectorAll('.notif-dot').forEach(el => {
        el.style.display = cnt ? '' : 'none';
    });

    // Sidebar toggle
    const toggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay && overlay.classList.toggle('show');
        });
        overlay && overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }
}
