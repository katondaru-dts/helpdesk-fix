<?php
$start = microtime(true);
$password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
$end = microtime(true);
echo "Time taken for password_hash: " . ($end - $start) . " seconds\n";
