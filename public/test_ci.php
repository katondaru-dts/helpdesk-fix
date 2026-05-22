<?php
// test.php
require '/var/www/html/vendor/autoload.php';

// Try to use a CI class
try {
    echo "Autoloaded successfully\n";
    $config = new \Config\App();
    echo "App Config BaseURL: " . $config->baseURL . "\n";

    $roleModel = new \App\Models\RoleModel();
    echo "RoleModel created\n";

    // Test Minio if used
    if (class_exists('App\Libraries\MinioStorage')) {
        echo "MinioStorage exists\n";
        // $minio = new \App\Libraries\MinioStorage();
        // echo "MinioStorage initialized\n";
    }

    echo "ALL OK\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
