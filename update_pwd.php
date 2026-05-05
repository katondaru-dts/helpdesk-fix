<?php
// Simple script to update admin password
require 'app/Config/Boot/production.php';
\ = \Config\Database::connect();
\ = password_hash('071025@Unmer', PASSWORD_DEFAULT);
\->table('users')
   ->where('email', 'admin@helpdesk.id')
   ->update(['password' => \]);
echo " Update Success\n\;
