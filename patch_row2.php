<?php
$file = '/var/www/html/app/Views/dashboard/admin.php';
$content = file_get_contents($file);

// Find and replace the ROW 2 section
$old = '    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <?php if ($showUsers): ?>
        <div class="dash-card card-users" onclick="window.location=\'<?= base_url(\'admin/users\') ?>\'" style="cursor:pointer;">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">User Aktif</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#8B5CF6; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-people-fill"></i></div>
                <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats[\'users\'] ?></div>
            </div>
        </div>
        <?php endif; ?>
        <div class="dash-card card-unassigned">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Belum Diassign</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#F59E0B; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-person-plus-fill"></i></div>
                <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats[\'unassigned\'] ?></div>
            </div>
        </div>
        <div class="dash-card card-urgent">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Urgent / High</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#EF4444; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-fire"></i></div>
                <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats[\'urgent\'] ?></div>
            </div>
        </div>
        <div class="dash-card card-reports" onclick="window.location=\'<?= base_url(\'admin/reports\') ?>\'" style="cursor:pointer;">
            <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Laporan</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="background:#10B981; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                <div style="font-size:13px; font-weight:600; color:#111827;">Lihat Detail</div>
            </div>
        </div>
    </div>';

$new = '    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

        <!-- Kiri: Stat Cards -->
        <div style="display:grid;grid-template-columns:repeat(<?= $gridCols ?>,1fr);gap:12px;">
            <?php if ($showUsers): ?>
            <div class="dash-card card-users" onclick="window.location=\'<?= base_url(\'admin/users\') ?>\'" style="cursor:pointer;">
                <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">User Aktif</div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="background:#8B5CF6; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-people-fill"></i></div>
                    <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats[\'users\'] ?></div>
                </div>
            </div>
            <?php endif; ?>
            <div class="dash-card card-unassigned">
                <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Belum Diassign</div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="background:#F59E0B; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-person-plus-fill"></i></div>
                    <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats[\'unassigned\'] ?></div>
                </div>
            </div>
            <div class="dash-card card-urgent">
                <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Urgent / High</div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="background:#EF4444; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-fire"></i></div>
                    <div style="font-size:22px; font-weight:700; color:#111827;"><?= $stats[\'urgent\'] ?></div>
                </div>
            </div>
            <div class="dash-card card-reports" onclick="window.location=\'<?= base_url(\'admin/reports\') ?>\'" style="cursor:pointer;">
                <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Laporan</div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="background:#10B981; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                    <div style="font-size:13px; font-weight:600; color:#111827;">Lihat Detail</div>
                </div>
            </div>
            <?php if ($showAudit): ?>
            <div class="dash-card card-audit">
                <div class="card-title-main" style="font-size:13px;margin-bottom:8px;">Audit Log</div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="background:#64748b; color:white; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-shield-lock-fill"></i></div>
                    <div style="font-size:13px; font-weight:600; color:#111827;">Log Admin</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Kanan: Tiket Urgent/High List -->
        <div class="dash-card">
            <div style="display:flex;align-items:center;gap:8px;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid #F3F4F6;">
                <div style="background:#FEE2E2; padding:6px; border-radius:8px; display:inline-flex;"><i class="bi bi-fire" style="color:#DC2626;"></i></div>
                <span class="card-title-main" style="margin-bottom:0; flex:1;">Tiket Urgent / High</span>
                <a href="<?= base_url(\'tickets\') ?>" class="btn btn-outline-secondary btn-sm">Lihat Semua</a>
            </div>
            <div class="scroll-area" style="max-height:200px;">
                <?php if(!empty($urgentTickets)): foreach($urgentTickets as $t): ?>
                    <div class="ticket-row" onclick="window.location=\'<?= base_url(\'tickets/detail/\'.$t[\'id\']) ?>\'">
                        <div style="min-width:0;flex:1;">
                            <div style="font-weight:600;font-size:13.5px;"><?= esc($t[\'title\']) ?></div>
                            <div style="font-size:11.5px;color:#6B7280;">#<?= $t[\'id\'] ?> &middot; <?= esc($t[\'reporter_name\'] ?? \'\') ?></div>
                        </div>
                        <span class="badge" style="background:#EF444415; color:#EF4444; border:1px solid #EF444430; font-size:10px;"><?= $t[\'priority\'] ?></span>
                    </div>
                <?php endforeach; else: ?>
                    <div style="padding:16px;text-align:center;color:#9CA3AF;font-size:13px;">Tidak ada tiket mendesak.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>';

if (strpos($content, trim(explode("\n", $old)[1])) !== false) {
    $result = str_replace($old, $new, $content);
    if ($result !== $content) {
        file_put_contents($file, $result);
        echo "SUCCESS: ROW 2 patched\n";
    }
    else {
        echo "ERROR: str_replace found no match\n";
    // Try line-by-line approach
    }
}
else {
    echo "NOT FOUND - trying alternative\n";
}
