<?php
// Patch script to add Deskripsi Gangguan and Link Dokumentasi columns to report view
$file = '/var/www/html/app/Views/admin/reports/index.php';
$content = file_get_contents($file);

// Fix 1: Add new header columns before Tanggal
$old_header = '<th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Tanggal</th>';
$new_header = '<th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Deskripsi Gangguan</th>
                <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Link Dokumentasi</th>
                <th style="padding:10px;text-align:left;border-bottom:2px solid #e5e7eb;font-size:12px;color:#374151">Tanggal</th>';
$content = str_replace($old_header, $new_header, $content);

// Fix 2: Add new data cells before Tanggal cell
$old_row = "<td style=\"padding:10px;font-size:13px\"><?= date('d/m/Y', strtotime(\$t['created_at'])) ?></td>";
$new_row = '<td style="padding:10px;font-size:11px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc($t[\'description\'] ?? \'\') ?>"><?= esc($t[\'description\'] ?? \'-\') ?></td>
                <td style="padding:10px;font-size:11px"><?php if(!empty($t[\'drive_link\'])): ?><a href="<?= esc($t[\'drive_link\']) ?>" target="_blank" style="color:#3b82f6;text-decoration:none;font-weight:600">Buka Link</a><?php else: ?>-<?php endif; ?></td>
                <td style="padding:10px;font-size:13px"><?= date(\'d/m/Y\', strtotime($t[\'created_at\'])) ?></td>';
$content = str_replace($old_row, $new_row, $content);

// Fix 3: Fix colspan
$content = str_replace('colspan="7"', 'colspan="9"', $content);

file_put_contents($file, $content);
echo "PATCH DONE\n";
echo "Kategori count: " . substr_count($content, 'Kategori') . "\n";
echo "Deskripsi count: " . substr_count($content, 'Deskripsi') . "\n";
