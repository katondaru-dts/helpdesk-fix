<?php
$f = '/var/www/html/app/Views/admin/reports/index.php';
$c = file_get_contents($f);
$old = '<div class="report-stat-icon" style="background: #ede9fe; color: #7c3aed;"><i class="bi bi-star-fill"></i></div>
        <div>
            <div class="report-stat-label">Avg Rating</div>
            <div class="report-stat-value"><?= number_format($avgRating ?: 0, 1) ?><span style="font-size: 14px; font-weight: 500; color: #94a3b8;">/5</span></div>
        </div>';
$new = '<div class="report-stat-icon" style="background: #fee2e2; color: #dc2626;"><i class="bi bi-envelope-open-fill"></i></div>
        <div>
            <div class="report-stat-label">Tiket Open</div>
            <div class="report-stat-value"><?= number_format($stats[\'open_tickets\'] ?? 0) ?></div>
        </div>';
$count = substr_count($c, 'avgRating');
echo "Found avgRating: $count times\n";
$c2 = str_replace($old, $new, $c);
if ($c2 !== $c) {
    file_put_contents($f, $c2);
    echo "FIXED OK\n";
}
else {
    // fallback: replace just the line with avgRating
    $lines = explode("\n", $c);
    foreach ($lines as $i => &$line) {
        if (strpos($line, 'avgRating') !== false) {
            $line = '            <div class="report-stat-value"><?= number_format($stats[\'open_tickets\'] ?? 0) ?></div>';
            echo "Fixed line " . ($i + 1) . "\n";
        }
        if (strpos($line, 'Avg Rating') !== false) {
            $line = '            <div class="report-stat-label">Tiket Open</div>';
        }
        if (strpos($line, 'bi-star-fill') !== false) {
            $line = '        <div class="report-stat-icon" style="background: #fee2e2; color: #dc2626;"><i class="bi bi-envelope-open-fill"></i></div>';
        }
    }
    file_put_contents($f, implode("\n", $lines));
    echo "FIXED via line patch\n";
}
