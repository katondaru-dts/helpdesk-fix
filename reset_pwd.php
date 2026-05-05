<?php
include 'app/Config/Constants.php';
include 'vendor/autoload.php';
\ = mysqli_connect('db', 'root', 'root_password', 'helpdesk_v2');
\C:\Projects\helpdesk-v2 = password_hash('071025@Unmer', PASSWORD_DEFAULT);
mysqli_query(\, " UPDATE users SET password = \$pwd WHERE email = admin@helpdesk.id \);
echo \Password Admin telah direset ke: 071025@Unmer\n\;
