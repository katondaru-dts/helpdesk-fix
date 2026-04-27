<?php
$content = file_get_contents('c:/Projects/helpdesk-v2/app/Controllers/Auth.php');
$out = "cat << 'ENDDEPLOY' > /var/www/html/app/Controllers/Auth.php\n" . $content . "\nENDDEPLOY\necho \"AUTH_DONE\"\n";
file_put_contents('c:/temp/helpdesk-tmp/push_auth3.sh', $out);
echo "Script created!";
