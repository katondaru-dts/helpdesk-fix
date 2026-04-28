<?php
$f = '/var/www/html/app/Views/dashboard/admin.php';
$c = file_get_contents($f);
$c = str_replace(
    'class="scroll-area" style="max-height: 350px;"',
    'style="overflow:visible;"',
    $c
);
file_put_contents($f, $c);
echo "done\n";
