// ============================================================
// REPORTS.JS - Analytics & Export
// ============================================================

function getReportData(from = null, to = null) {
    let tickets = getTickets();
    if (from) tickets = tickets.filter(t => new Date(t.created_at) >= new Date(from));
    if (to) tickets = tickets.filter(t => new Date(t.created_at) <= new Date(to + 'T23:59:59'));

    const statuses = getTStatuses();
    const cats = getCats();
    const depts = getDepts();
    const users = getUsers();
    const ratings = getTRatings();

    function curStatus(tid) { return getTicketCurrentStatus(tid); }

    // Summary
    const total = tickets.length;
    const open = tickets.filter(t => curStatus(t.id) === 'OPEN').length;
    const inProgress = tickets.filter(t => curStatus(t.id) === 'IN_PROGRESS').length;
    const pending = tickets.filter(t => curStatus(t.id) === 'PENDING').length;
    const resolved = tickets.filter(t => curStatus(t.id) === 'RESOLVED').length;
    const closed = tickets.filter(t => curStatus(t.id) === 'CLOSED').length;

    // By category
    const byCat = cats.map(c => {
        const count = tickets.filter(t => t.cat_id === c.id).length;
        return { label: c.name, count };
    }).filter(x => x.count > 0);

    // By priority
    const byPriority = [
        { label: 'Low', count: tickets.filter(t => t.priority === 'LOW').length },
        { label: 'Medium', count: tickets.filter(t => t.priority === 'MEDIUM').length },
        { label: 'High', count: tickets.filter(t => t.priority === 'HIGH').length },
        { label: 'Critical', count: tickets.filter(t => t.priority === 'CRITICAL').length },
    ];

    // By department
    const byDept = depts.map(d => {
        const count = tickets.filter(t => t.dept_id === d.id).length;
        return { label: d.name, count };
    }).filter(x => x.count > 0);

    // By support
    const supports = users.filter(u => u.role_id === 2);
    const bySupport = supports.map(s => {
        const assigned = tickets.filter(t => t.assigned_to === s.id).length;
        const resolved2 = tickets.filter(t => t.assigned_to === s.id && ['RESOLVED', 'CLOSED'].includes(curStatus(t.id))).length;
        return { label: s.name, assigned, resolved: resolved2 };
    });

    // Monthly trend (last 6 months)
    const months = []; const now = new Date();
    for (let i = 5; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const lbl = d.toLocaleString('id-ID', { month: 'short', year: '2-digit' });
        const count = tickets.filter(t => {
            const td = new Date(t.created_at);
            return td.getFullYear() === d.getFullYear() && td.getMonth() === d.getMonth();
        }).length;
        months.push({ label: lbl, count });
    }

    // Average rating
    const avgRating = ratings.length ? (ratings.reduce((s, r) => s + r.rating, 0) / ratings.length).toFixed(1) : 0;

    return { total, open, inProgress, pending, resolved, closed, byCat, byPriority, byDept, bySupport, months, avgRating };
}

// ── Chart Colors ──
const CHART_COLORS = ['#2563EB', '#7C3AED', '#059669', '#D97706', '#DC2626', '#0891B2', '#DB2777'];

function renderStatusDonut(ctx, data) {
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'],
            datasets: [{
                data: [data.open, data.inProgress, data.pending, data.resolved, data.closed],
                backgroundColor: ['#EF4444', '#F59E0B', '#8B5CF6', '#10B981', '#94A3B8'],
                borderWidth: 0, hoverOffset: 6
            }]
        },
        options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'right', labels: { font: { size: 12 }, padding: 12 } }, tooltip: { callbacks: { label: l => `${l.label}: ${l.parsed} tiket` } } } }
    });
}

function renderCatBar(ctx, data) {
    return new Chart(ctx, {
        type: 'bar',
        data: { labels: data.map(d => d.label), datasets: [{ label: 'Jumlah Tiket', data: data.map(d => d.count), backgroundColor: CHART_COLORS, borderRadius: 6, borderSkipped: false }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
}

function renderTrendLine(ctx, data) {
    return new Chart(ctx, {
        type: 'line',
        data: { labels: data.map(d => d.label), datasets: [{ label: 'Tiket Masuk', data: data.map(d => d.count), borderColor: '#2563EB', backgroundColor: 'rgba(37,99,235,.1)', tension: .4, fill: true, pointRadius: 5, pointBackgroundColor: '#2563EB' }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
}

function renderPriorityBar(ctx, data) {
    return new Chart(ctx, {
        type: 'bar',
        data: { labels: data.map(d => d.label), datasets: [{ label: 'Jumlah', data: data.map(d => d.count), backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#991B1B'], borderRadius: 6 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
}

// ── Export Excel ──
function exportExcel(data, from, to) {
    const tickets = (() => {
        let t = getTickets();
        if (from) t = t.filter(x => new Date(x.created_at) >= new Date(from));
        if (to) t = t.filter(x => new Date(x.created_at) <= new Date(to + 'T23:59:59'));
        return t;
    })();
    const rows = tickets.map(t => ({
        'ID Tiket': t.id,
        'Judul': t.title,
        'Kategori': (getCatById(t.cat_id) || {}).name || '-',
        'Prioritas': t.priority,
        'Status': getTicketCurrentStatus(t.id),
        'Pelapor': (getUserById(t.reporter_id) || {}).name || '-',
        'Ditangani Oleh': t.assigned_to ? (getUserById(t.assigned_to) || {}).name || '-' : '-',
        'Departemen': (getDeptById(t.dept_id) || {}).name || '-',
        'Lokasi': t.location || '-',
        'Tanggal Buat': fmtDateTime(t.created_at),
        'Tanggal Tutup': t.closed_at ? fmtDateTime(t.closed_at) : '-',
    }));
    const ws = XLSX.utils.json_to_sheet(rows);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Tiket');
    XLSX.writeFile(wb, `Laporan_HelpDesk_${new Date().toISOString().split('T')[0]}.xlsx`);
}

// ── Export PDF ──
function exportPDF(data, from, to) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
    const W = doc.internal.pageSize.getWidth();
    let y = 15;

    // Header
    doc.setFillColor(37, 99, 235); doc.rect(0, 0, W, 30, 'F');
    doc.setTextColor(255, 255, 255); doc.setFont('helvetica', 'bold'); doc.setFontSize(16);
    doc.text('LAPORAN HELPDESK IT', W / 2, 14, { align: 'center' });
    doc.setFontSize(10); doc.setFont('helvetica', 'normal');
    doc.text(`Periode: ${from || 'Semua'} s/d ${to || 'Sekarang'}`, W / 2, 22, { align: 'center' });
    doc.text(`Dicetak: ${fmtDateTime(new Date().toISOString())}`, W / 2, 28, { align: 'center' });
    y = 40;

    // Summary boxes
    doc.setTextColor(0, 0, 0); doc.setFont('helvetica', 'bold'); doc.setFontSize(12);
    doc.text('Ringkasan Statistik', 14, y); y += 8;

    const sumItems = [
        { label: 'Total Tiket', val: data.total, color: [37, 99, 235] },
        { label: 'Open', val: data.open, color: [239, 68, 68] },
        { label: 'In Progress', val: data.inProgress, color: [245, 158, 11] },
        { label: 'Resolved', val: data.resolved, color: [16, 185, 129] },
        { label: 'Closed', val: data.closed, color: [100, 116, 139] },
    ];
    sumItems.forEach((s, i) => {
        const x = 14 + (i % 3) * 64, yy = (i < 3) ? y : y + 22;
        doc.setFillColor(...s.color); doc.roundedRect(x, yy, 58, 16, 3, 3, 'F');
        doc.setTextColor(255, 255, 255); doc.setFont('helvetica', 'bold'); doc.setFontSize(16);
        doc.text(String(s.val), x + 29, yy + 9, { align: 'center' });
        doc.setFontSize(8); doc.setFont('helvetica', 'normal');
        doc.text(s.label, x + 29, yy + 14, { align: 'center' });
    });
    y += 52;

    // By Category
    doc.setTextColor(0, 0, 0); doc.setFont('helvetica', 'bold'); doc.setFontSize(11);
    doc.text('Tiket per Kategori', 14, y); y += 6;
    doc.setFont('helvetica', 'normal'); doc.setFontSize(9);
    data.byCat.forEach(c => {
        doc.setFillColor(241, 245, 249); doc.rect(14, y, W - 28, 8, 'F');
        doc.setTextColor(51, 65, 85); doc.text(c.label, 16, y + 5.5);
        doc.setTextColor(37, 99, 235); doc.setFont('helvetica', 'bold');
        doc.text(String(c.count), W - 15, y + 5.5, { align: 'right' });
        doc.setFont('helvetica', 'normal');
        y += 10;
    });
    y += 4;

    // By Priority
    doc.setTextColor(0, 0, 0); doc.setFont('helvetica', 'bold'); doc.setFontSize(11);
    doc.text('Tiket per Prioritas', 14, y); y += 6;
    doc.setFont('helvetica', 'normal'); doc.setFontSize(9);
    const pColors = [[16, 185, 129], [245, 158, 11], [239, 68, 68], [155, 28, 28]];
    data.byPriority.forEach((p, i) => {
        doc.setFillColor(...pColors[i]); doc.circle(17, y + 3, 3, 'F');
        doc.setTextColor(51, 65, 85); doc.text(p.label, 22, y + 5);
        doc.setTextColor(37, 99, 235); doc.setFont('helvetica', 'bold');
        doc.text(String(p.count), W - 15, y + 5, { align: 'right' });
        doc.setFont('helvetica', 'normal'); y += 10;
    });

    doc.setFontSize(8); doc.setTextColor(148, 163, 184);
    doc.text(`HelpDesk IT v2.0 | Generated ${new Date().toLocaleDateString('id-ID')}`, W / 2, 290, { align: 'center' });
    doc.save(`Laporan_HelpDesk_${new Date().toISOString().split('T')[0]}.pdf`);
}
