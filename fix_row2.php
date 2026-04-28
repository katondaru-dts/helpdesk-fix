<?php
$file = '/var/www/html/app/Views/dashboard/admin.php';
$content = file_get_contents($file);

// Replace the broken pattern with correct one
// Broken: repeat(<?=  ?>,1fr)
// Fixed:  repeat(<?= $gridCols ?>,1fr)
$broken = 'grid-template-columns:repeat(<?=  ?>,1fr)';
$fixed  = 'grid-template-columns:repeat(<?= $gridCols ?>,1fr)';

$count = substr_count($content, $broken);
echo "Found broken pattern: $count times\n";

if ($count > 0) {
    $new = str_replace($broken, $fixed, $content);
    file_put_contents($file, $new);
    echo "FIXED OK\n";
} else {
    // Check what is there on line 316
    $lines = explode("\n", $content);
    echo "Line 316: " . ($lines[315] ?? 'NOT FOUND') . "\n";
    echo "Total lines: " . count($lines) . "\n";
    
    // Try to fix by replacing any repeat(<?= with $gridCols inside
    $broken2 = 'grid-template-columns:repeat(<?=';
    $pos = strpos($content, $broken2);
    if ($pos !== false) {
        // Find the end of this tag
        $end = strpos($content, '?>', $pos);
        $oldTag = substr($content, $pos, $end - $pos + 2);
        echo "Found: $oldTag\n";
        $newTag = 'grid-template-columns:repeat(<?= $gridCols ?>';
        $new = str_replace($oldTag, $newTag, $content);
        file_put_contents($file, $new);
        echo "FIXED via fallback\n";
    }
}
