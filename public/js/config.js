// ============================================================
// CONFIG.JS - Constants & App Settings
// ============================================================
const APP = { name: 'HelpDesk IT', version: '2.0', perPage: 10 };

const ROLE = { ADMIN: 1, SUPPORT: 2, USER: 3 };

const STATUS = {
    OPEN: { v: 'OPEN', label: 'Open', icon: 'bi-circle', cls: 'b-OPEN', tl: 'open' },
    IN_PROGRESS: { v: 'IN_PROGRESS', label: 'In Progress', icon: 'bi-arrow-repeat', cls: 'b-IN_PROGRESS', tl: 'in_progress' },
    PENDING: { v: 'PENDING', label: 'Pending', icon: 'bi-pause-circle', cls: 'b-PENDING', tl: 'pending' },
    RESOLVED: { v: 'RESOLVED', label: 'Resolved', icon: 'bi-check-circle', cls: 'b-RESOLVED', tl: 'resolved' },
    CLOSED: { v: 'CLOSED', label: 'Closed', icon: 'bi-x-circle', cls: 'b-CLOSED', tl: 'closed' },
};

const PRIORITY = {
    LOW: { v: 'LOW', label: 'Low', cls: 'bp-LOW' },
    MEDIUM: { v: 'MEDIUM', label: 'Medium', cls: 'bp-MEDIUM' },
    HIGH: { v: 'HIGH', label: 'High', cls: 'bp-HIGH' },
    CRITICAL: { v: 'CRITICAL', label: 'Critical', cls: 'bp-CRITICAL' },
};

function statusBadge(s) {
    const d = STATUS[s] || STATUS.OPEN;
    return `<span class="badge ${d.cls}">${d.label}</span>`;
}
function priorityBadge(p) {
    const d = PRIORITY[p] || PRIORITY.MEDIUM;
    return `<span class="badge-p ${d.cls}">${d.label}</span>`;
}
function statusLabel(s) { return (STATUS[s] || STATUS.OPEN).label; }
function priorityLabel(p) { return (PRIORITY[p] || PRIORITY.MEDIUM).label; }
