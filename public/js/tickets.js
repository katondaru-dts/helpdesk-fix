// ============================================================
// TICKETS.JS - Ticket Business Logic
// ============================================================

function createTicket(data) {
    const u = getSession();
    if (!u) return { ok: false, msg: 'Tidak terlogin.' };
    const { title, description, cat_id, priority, location } = data;
    if (!title || !description || !cat_id) return { ok: false, msg: 'Isi field wajib.' };

    const id = nextTicketId();
    const now = isoNow();
    const ticket = {
        id, title: title.trim(), description: description.trim(),
        cat_id: parseInt(cat_id), priority: priority || 'MEDIUM',
        reporter_id: u.id, assigned_to: null, dept_id: u.dept_id,
        location: location || '', created_at: now, updated_at: now, closed_at: null
    };
    const tickets = getTickets();
    tickets.push(ticket);
    saveTickets(tickets);

    // Initial status
    const statuses = getTStatuses();
    statuses.push({ id: nextId(statuses), ticket_id: id, status: 'OPEN', notes: 'Tiket dibuat.', changed_by: u.id, changed_at: now });
    saveTStatuses(statuses);

    // Notify admin/support
    notifyAll([1, 2], `Tiket Baru: ${id}`, `${title}`, 'NEW_TICKET', id);
    return { ok: true, id, msg: 'Tiket berhasil dibuat!' };
}

function updateTicketStatus(ticket_id, newStatus, notes = '') {
    const u = getSession();
    if (!u) return { ok: false, msg: 'Tidak terlogin.' };
    if (u.role_id === 3) return { ok: false, msg: 'Tidak diizinkan.' };
    const statuses = getTStatuses();
    const now = isoNow();
    statuses.push({ id: nextId(statuses), ticket_id, status: newStatus, notes, changed_by: u.id, changed_at: now });
    saveTStatuses(statuses);

    const tickets = getTickets();
    const idx = tickets.findIndex(t => t.id === ticket_id);
    if (idx > -1) {
        tickets[idx].updated_at = now;
        if (newStatus === 'CLOSED' || newStatus === 'RESOLVED') tickets[idx].closed_at = now;
        saveTickets(tickets);
    }

    // Notify reporter
    const t = getTicketById(ticket_id);
    if (t) notifyUser(t.reporter_id, `Status Tiket ${ticket_id} Diperbarui`, `Status tiket "${t.title}" berubah menjadi ${statusLabel(newStatus)}.`, 'STATUS_CHANGE', ticket_id);
    return { ok: true, msg: 'Status berhasil diperbarui!' };
}

function assignTicket(ticket_id, support_id) {
    const u = getSession();
    if (!u || u.role_id === 3) return { ok: false, msg: 'Tidak diizinkan.' };
    const tickets = getTickets();
    const idx = tickets.findIndex(t => t.id === ticket_id);
    if (idx === -1) return { ok: false, msg: 'Tiket tidak ditemukan.' };
    tickets[idx].assigned_to = support_id || null;
    tickets[idx].updated_at = isoNow();
    saveTickets(tickets);
    if (support_id) {
        const t = tickets[idx];
        notifyUser(support_id, `Tiket Ditugaskan: ${ticket_id}`, `Anda ditugaskan menangani tiket "${t.title}".`, 'ASSIGNED', ticket_id);
    }
    return { ok: true, msg: 'Tiket berhasil diassign.' };
}

function addMessage(ticket_id, message, is_internal = 0) {
    const u = getSession();
    if (!u) return { ok: false, msg: 'Tidak terlogin.' };
    if (!message.trim()) return { ok: false, msg: 'Pesan tidak boleh kosong.' };
    const msgs = getTMsgs();
    msgs.push({ id: nextId(msgs), ticket_id, sender_id: u.id, message: message.trim(), is_internal, sent_at: isoNow() });
    saveTMsgs(msgs);
    const t = getTicketById(ticket_id);
    if (t) {
        const target = u.id === t.reporter_id ? (t.assigned_to || null) : t.reporter_id;
        if (target) notifyUser(target, `Balasan Baru pada ${ticket_id}`, `Ada balasan baru pada tiket "${t.title}".`, 'NEW_MESSAGE', ticket_id);
    }
    return { ok: true, msg: 'Pesan terkirim.' };
}

function rateTicket(ticket_id, rating, feedback = '') {
    const u = getSession();
    if (!u) return { ok: false, msg: 'Tidak terlogin.' };
    const t = getTicketById(ticket_id);
    if (!t || t.reporter_id !== u.id) return { ok: false, msg: 'Tidak diizinkan.' };
    const existing = getTicketRating(ticket_id);
    if (existing) return { ok: false, msg: 'Sudah memberikan rating.' };
    const ratings = getTRatings();
    ratings.push({ id: nextId(ratings), ticket_id, rated_by: u.id, rating, feedback, rated_at: isoNow() });
    saveTRatings(ratings);
    return { ok: true, msg: 'Rating berhasil dikirim. Terima kasih!' };
}

function getTicketsForUser(userId, roleId) {
    const all = getTickets();
    if (roleId === 1) return all;
    if (roleId === 2) return all;
    return all.filter(t => t.reporter_id === userId);
}

function getTicketDetail(tid) {
    const t = getTicketById(tid);
    if (!t) return null;
    const cat = getCatById(t.cat_id);
    const reporter = getUserById(t.reporter_id);
    const assigned = t.assigned_to ? getUserById(t.assigned_to) : null;
    const dept = getDeptById(t.dept_id);
    const status = getTicketCurrentStatus(tid);
    const history = getTicketHistory(tid);
    const messages = getTicketMessages(tid);
    const rating = getTicketRating(tid);
    return { ...t, cat, reporter, assigned, dept, status, history, messages, rating };
}

// ── Notifications helpers ──
function notifyUser(uid, title, message, type, ref_id = null) {
    const notifs = getNotifs();
    notifs.push({ id: nextId(notifs), user_id: uid, title, message, type, ref_id, is_read: 0, created_at: isoNow() });
    saveNotifs(notifs);
}
function notifyAll(roleIds, title, message, type, ref_id = null) {
    const users = getUsers().filter(u => roleIds.includes(u.role_id) && u.is_active);
    users.forEach(u => notifyUser(u.id, title, message, type, ref_id));
}
function markNotifRead(id) {
    const notifs = getNotifs(); const idx = notifs.findIndex(n => n.id === id);
    if (idx > -1) { notifs[idx].is_read = 1; saveNotifs(notifs); }
}
function markAllNotifsRead(uid) {
    const notifs = getNotifs(); notifs.forEach(n => { if (n.user_id === uid) n.is_read = 1; }); saveNotifs(notifs);
}
