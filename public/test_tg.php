<?php
define('ROOTPATH', '/var/www/html/');
define('APPPATH', '/var/www/html/app/');
define('SYSTEMPATH', '/var/www/html/system/');

require ROOTPATH . 'system/bootstrap.php';
require APPPATH . 'Config/Paths.php';
$paths = new \Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Initialize DotEnv
if (file_exists(ROOTPATH . '.env')) {
    $env = new \CodeIgniter\Config\DotEnv(ROOTPATH);
    $env->load();
}

helper('telegram');
$result = send_telegram("🚀 <b>Test Notifikasi Server</b>\nSistem Helpdesk Unmer berhasil mengirim pesan percobaan ini!");
echo $result ? 'SUCCESS' : 'FAILED';
