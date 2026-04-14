// ============================================================
// DATA.JS - LocalStorage Data Layer + Seed Data
// ============================================================
const DB = {
    ROLES: 'hd2_roles', DEPTS: 'hd2_depts', CATS: 'hd2_cats', USERS: 'hd2_users',
    TICKETS: 'hd2_tickets', T_STATUS: 'hd2_tstatus', T_MSGS: 'hd2_tmsgs',
    T_RATINGS: 'hd2_tratings', NOTIFS: 'hd2_notifs',
    SEQ_U: 'hd2_seq_u', SEQ_T: 'hd2_seq_t', INIT: 'hd2_init',
};

function dbGet(k) { try { return JSON.parse(localStorage.getItem(k)) || []; } catch { return []; } }
function dbSet(k, v) { localStorage.setItem(k, JSON.stringify(v)); }
function nextSeq(sk, pfx, pad = 4) { const n = (parseInt(localStorage.getItem(sk) || '0') + 1); localStorage.setItem(sk, n); return pfx + String(n).padStart(pad, '0'); }

const getRoles = () => dbGet(DB.ROLES);
const getDepts = () => dbGet(DB.DEPTS);
const getCats = () => dbGet(DB.CATS);
const getUsers = () => dbGet(DB.USERS);
const getTickets = () => dbGet(DB.TICKETS);
const getTStatuses = () => dbGet(DB.T_STATUS);
const getTMsgs = () => dbGet(DB.T_MSGS);
const getTRatings = () => dbGet(DB.T_RATINGS);
const getNotifs = () => dbGet(DB.NOTIFS);

const saveRoles = d => dbSet(DB.ROLES, d);
const saveDepts = d => dbSet(DB.DEPTS, d);
const saveCats = d => dbSet(DB.CATS, d);
const saveUsers = d => dbSet(DB.USERS, d);
const saveTickets = d => dbSet(DB.TICKETS, d);
const saveTStatuses = d => dbSet(DB.T_STATUS, d);
const saveTMsgs = d => dbSet(DB.T_MSGS, d);
const saveTRatings = d => dbSet(DB.T_RATINGS, d);
const saveNotifs = d => dbSet(DB.NOTIFS, d);

const nextUserId = () => nextSeq(DB.SEQ_U, 'USR');
const nextTicketId = () => nextSeq(DB.SEQ_T, 'HD');
function nextId(arr) { return arr.length ? Math.max(...arr.map(x => x.id)) + 1 : 1; }

// ── DML helpers ──
function getUserById(id) { return getUsers().find(u => u.id === id); }
function getTicketById(id) { return getTickets().find(t => t.id === id); }
function getCatById(id) { return getCats().find(c => c.id === id); }
function getDeptById(id) { return getDepts().find(d => d.id === id); }
function getRoleById(id) { return getRoles().find(r => r.id === id); }
function getTicketCurrentStatus(tid) {
    const hist = getTStatuses().filter(s => s.ticket_id === tid);
    if (!hist.length) return 'OPEN';
    return hist.sort((a, b) => new Date(b.changed_at) - new Date(a.changed_at))[0].status;
}
function getTicketHistory(tid) { return getTStatuses().filter(s => s.ticket_id === tid).sort((a, b) => new Date(a.changed_at) - new Date(b.changed_at)); }
function getTicketMessages(tid) { return getTMsgs().filter(m => m.ticket_id === tid).sort((a, b) => new Date(a.sent_at) - new Date(b.sent_at)); }
function getTicketRating(tid) { return getTRatings().find(r => r.ticket_id === tid); }
function getUserNotifs(uid) { return getNotifs().filter(n => n.user_id === uid).sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); }
function unreadCount(uid) { return getUserNotifs(uid).filter(n => !n.is_read).length; }

// ── Seed Data ──
function initSeedData() {
    if (localStorage.getItem(DB.INIT)) return;
    const now = new Date().toISOString();

    dbSet(DB.ROLES, [
        { id: 1, code: 'ADMIN', name: 'Administrator' },
        { id: 2, code: 'SUPPORT', name: 'IT Support' },
        { id: 3, code: 'USER', name: 'User' },
    ]);
    dbSet(DB.DEPTS, [
        { id: 1, name: 'IT', code: 'IT', is_active: 1 },
        { id: 2, name: 'Finance', code: 'FIN', is_active: 1 },
        { id: 3, name: 'HR', code: 'HR', is_active: 1 },
        { id: 4, name: 'Operations', code: 'OPS', is_active: 1 },
        { id: 5, name: 'Marketing', code: 'MKT', is_active: 1 },
    ]);
    dbSet(DB.CATS, [
        { id: 1, name: 'Hardware', description: 'Kerusakan perangkat keras', sla_hours: 8, is_active: 1 },
        { id: 2, name: 'Software', description: 'Masalah aplikasi/program', sla_hours: 24, is_active: 1 },
        { id: 3, name: 'Jaringan', description: 'Masalah koneksi & internet', sla_hours: 4, is_active: 1 },
        { id: 4, name: 'Akun & Akses', description: 'Login, password, hak akses', sla_hours: 2, is_active: 1 },
        { id: 5, name: 'Listrik', description: 'Daya & kelistrikan kantor', sla_hours: 2, is_active: 1 },
        { id: 6, name: 'Lainnya', description: 'Gangguan lain-lain', sla_hours: 48, is_active: 1 },
    ]);

    const users = [
        { id: 'USR0001', name: 'Administrator', email: 'admin@helpdesk.id', password: 'admin123', role_id: 1, dept_id: 1, gender: 'L', phone: '08120000001', birth_date: '1990-01-01', is_active: 1, created_at: now },
        { id: 'USR0002', name: 'Budi Santoso', email: 'budi@helpdesk.id', password: 'support123', role_id: 2, dept_id: 1, gender: 'L', phone: '08120000002', birth_date: '1992-05-10', is_active: 1, created_at: now },
        { id: 'USR0003', name: 'Rina Kurniawati', email: 'rina@helpdesk.id', password: 'support123', role_id: 2, dept_id: 1, gender: 'P', phone: '08120000003', birth_date: '1994-08-22', is_active: 1, created_at: now },
        { id: 'USR0004', name: 'Siti Rahayu', email: 'siti@helpdesk.id', password: 'user123', role_id: 3, dept_id: 3, gender: 'P', phone: '08120000004', birth_date: '1995-03-15', is_active: 1, created_at: now },
        { id: 'USR0005', name: 'Katondaru', email: 'katon@helpdesk.id', password: 'user123', role_id: 3, dept_id: 2, gender: 'L', phone: '08120000005', birth_date: '1993-11-20', is_active: 1, created_at: now },
        { id: 'USR0006', name: 'Dewi Anggraini', email: 'dewi@helpdesk.id', password: 'user123', role_id: 3, dept_id: 4, gender: 'P', phone: '08120000006', birth_date: '1997-07-08', is_active: 1, created_at: now },
    ];
    dbSet(DB.USERS, users);
    localStorage.setItem(DB.SEQ_U, '6');

    const tickets = [
        { id: 'HD0001', title: 'Printer Tidak Bisa Print', description: 'Printer di ruang Finance tidak bisa digunakan sejak pagi. Sudah restart tapi tetap tidak bisa print.', cat_id: 1, priority: 'HIGH', reporter_id: 'USR0004', assigned_to: 'USR0002', dept_id: 2, location: 'Lantai 2 - Ruang Finance', created_at: '2026-02-20T08:00:00', updated_at: '2026-02-21T14:00:00', closed_at: '2026-02-21T14:00:00' },
        { id: 'HD0002', title: 'VPN Kantor Tidak Bisa Connect', description: 'Tidak bisa connect ke VPN kantor dari rumah. Error: "Connection timed out". Sudah coba reinstall masih gagal.', cat_id: 3, priority: 'HIGH', reporter_id: 'USR0004', assigned_to: 'USR0002', dept_id: 3, location: 'Remote / WFH', created_at: '2026-02-22T09:30:00', updated_at: '2026-02-23T10:00:00', closed_at: null },
        { id: 'HD0003', title: 'Laptop Sangat Lambat', description: 'Laptop sangat lambat sejak kemarin. Butuh 15 menit hanya untuk booting. Sangat mengganggu produktivitas.', cat_id: 1, priority: 'MEDIUM', reporter_id: 'USR0005', assigned_to: null, dept_id: 2, location: 'Lantai 3 - Meja Kerja', created_at: '2026-02-25T10:00:00', updated_at: '2026-02-25T10:00:00', closed_at: null },
        { id: 'HD0004', title: 'Akun Email Tidak Bisa Login', description: 'Akun email kantor tidak bisa login. Selalu muncul "Invalid credentials" padahal password benar.', cat_id: 4, priority: 'CRITICAL', reporter_id: 'USR0005', assigned_to: 'USR0003', dept_id: 2, location: 'Lantai 3 - Meja Kerja', created_at: '2026-02-28T11:00:00', updated_at: '2026-03-01T09:00:00', closed_at: null },
        { id: 'HD0005', title: 'Excel Crash Saat Buka File Besar', description: 'Microsoft Excel selalu crash ketika membuka file ukuran besar. Sangat mengganggu pekerjaan harian.', cat_id: 2, priority: 'MEDIUM', reporter_id: 'USR0004', assigned_to: null, dept_id: 3, location: 'Lantai 2', created_at: '2026-03-01T08:30:00', updated_at: '2026-03-01T08:30:00', closed_at: null },
        { id: 'HD0006', title: 'Internet Lambat di Ruang Meeting', description: 'Koneksi internet sangat lambat di ruang meeting lantai 1. Video call sering terputus dan mengganggu rapat.', cat_id: 3, priority: 'HIGH', reporter_id: 'USR0006', assigned_to: 'USR0002', dept_id: 4, location: 'Lantai 1 - Ruang Meeting', created_at: '2026-03-02T07:00:00', updated_at: '2026-03-02T07:00:00', closed_at: null },
        { id: 'HD0007', title: 'Proyektor Ruang Presentasi Mati', description: 'Proyektor di ruang presentasi lantai 2 tidak mau menyala. Dibutuhkan untuk presentasi besok pagi.', cat_id: 1, priority: 'HIGH', reporter_id: 'USR0006', assigned_to: null, dept_id: 4, location: 'Lantai 2 - Ruang Presentasi', created_at: '2026-03-02T09:00:00', updated_at: '2026-03-02T09:00:00', closed_at: null },
        { id: 'HD0008', title: 'Antivirus Expired di Komputer Server', description: 'Lisensi antivirus di komputer server sudah expired sejak minggu lalu. Mohon segera diperpanjang/diganti.', cat_id: 2, priority: 'CRITICAL', reporter_id: 'USR0004', assigned_to: 'USR0003', dept_id: 1, location: 'Server Room Lt.1', created_at: '2026-03-02T10:00:00', updated_at: '2026-03-02T10:00:00', closed_at: null },
    ];
    dbSet(DB.TICKETS, tickets);
    localStorage.setItem(DB.SEQ_T, '8');

    const ts_id = { c: 1 };
    function ts(tid, status, notes, by, at) { return { id: ts_id.c++, ticket_id: tid, status, notes, changed_by: by, changed_at: at }; }
    dbSet(DB.T_STATUS, [
        ts('HD0001', 'OPEN', 'Tiket dibuat.', 'USR0004', '2026-02-20T08:00:00'),
        ts('HD0001', 'IN_PROGRESS', 'Sedang ditangani.', 'USR0002', '2026-02-20T10:00:00'),
        ts('HD0001', 'RESOLVED', 'Printer telah diperbaiki.', 'USR0002', '2026-02-21T13:00:00'),
        ts('HD0001', 'CLOSED', 'Tiket ditutup.', 'USR0001', '2026-02-21T14:00:00'),
        ts('HD0002', 'OPEN', 'Tiket dibuat.', 'USR0004', '2026-02-22T09:30:00'),
        ts('HD0002', 'IN_PROGRESS', 'Investigasi VPN server.', 'USR0002', '2026-02-23T10:00:00'),
        ts('HD0003', 'OPEN', 'Tiket dibuat.', 'USR0005', '2026-02-25T10:00:00'),
        ts('HD0004', 'OPEN', 'Tiket dibuat.', 'USR0005', '2026-02-28T11:00:00'),
        ts('HD0004', 'IN_PROGRESS', 'Sedang reset password.', 'USR0003', '2026-03-01T09:00:00'),
        ts('HD0005', 'OPEN', 'Tiket dibuat.', 'USR0004', '2026-03-01T08:30:00'),
        ts('HD0006', 'OPEN', 'Tiket dibuat.', 'USR0006', '2026-03-02T07:00:00'),
        ts('HD0006', 'IN_PROGRESS', 'Cek access point.', 'USR0002', '2026-03-02T08:00:00'),
        ts('HD0007', 'OPEN', 'Tiket dibuat.', 'USR0006', '2026-03-02T09:00:00'),
        ts('HD0008', 'OPEN', 'Tiket dibuat.', 'USR0004', '2026-03-02T10:00:00'),
        ts('HD0008', 'IN_PROGRESS', 'Proses perpanjangan lisensi.', 'USR0003', '2026-03-02T11:00:00'),
    ]);

    const msg_id = { c: 1 };
    function msg(tid, sid, message, at, internal = 0) { return { id: msg_id.c++, ticket_id: tid, sender_id: sid, message, is_internal: internal, sent_at: at }; }
    dbSet(DB.T_MSGS, [
        msg('HD0001', 'USR0002', 'Printer sudah dicek. Masalah kabel USB longgar dan driver perlu update. Sekarang sudah normal kembali.', '2026-02-21T13:00:00'),
        msg('HD0002', 'USR0002', 'Sedang investigasi konfigurasi VPN server. Mohon bersabar, estimasi selesai 2-3 jam.', '2026-02-23T10:30:00'),
        msg('HD0004', 'USR0003', 'Password email sudah direset. Silakan cek email alternatif untuk password sementara. Harap segera ganti setelah login.', '2026-03-01T09:30:00'),
        msg('HD0006', 'USR0002', 'Access point lantai 1 sedang dikonfigurasi ulang. Sementara gunakan kabel LAN jika perlu internet cepat.', '2026-03-02T08:30:00'),
        msg('HD0008', 'USR0003', 'Sedang menghubungi vendor untuk perpanjangan lisensi. Sementara gunakan Windows Defender sebagai backup.', '2026-03-02T11:30:00'),
    ]);

    dbSet(DB.T_RATINGS, [
        { id: 1, ticket_id: 'HD0001', rated_by: 'USR0004', rating: 5, feedback: 'Respon cepat dan solusi sangat membantu. Terima kasih!', rated_at: '2026-02-21T15:00:00' },
    ]);

    dbSet(DB.NOTIFS, [
        { id: 1, user_id: 'USR0004', title: 'Tiket HD0001 Selesai', message: 'Tiket Printer Tidak Bisa Print telah diselesaikan.', type: 'RESOLVED', ref_id: 'HD0001', is_read: 1, created_at: '2026-02-21T13:00:00' },
        { id: 2, user_id: 'USR0002', title: 'Tiket Baru Masuk', message: 'Tiket baru: VPN Kantor Tidak Bisa Connect.', type: 'NEW_TICKET', ref_id: 'HD0002', is_read: 0, created_at: '2026-02-22T09:30:00' },
        { id: 3, user_id: 'USR0001', title: 'Tiket Baru: HD0007', message: 'Proyektor Ruang Presentasi Mati - butuh perhatian segera.', type: 'NEW_TICKET', ref_id: 'HD0007', is_read: 0, created_at: '2026-03-02T09:00:00' },
        { id: 4, user_id: 'USR0001', title: 'Tiket CRITICAL: HD0008', message: 'Antivirus expired di Server - prioritas tinggi!', type: 'NEW_TICKET', ref_id: 'HD0008', is_read: 0, created_at: '2026-03-02T10:00:00' },
    ]);

    localStorage.setItem(DB.INIT, '1');
    console.log('[HelpDesk V2] Seed data loaded.');
}

initSeedData();
