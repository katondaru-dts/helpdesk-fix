<?php

define('ENVIRONMENT', 'production');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// Bootstrapping CI4
$pathsConfig = 'app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

use App\Libraries\MinioStorage;

$minio = new MinioStorage();
$bucket = $minio->getBucket();
$folder = $minio->getFolder();

echo "Checking Bucket: $bucket\n";
echo "Current Folder Config: $folder\n\n";

$objects = $minio->listObjects();

if (empty($objects)) {
    echo "No objects found in prefix: $folder/\n";
} else {
    echo "Objects in $folder/:\n";
    foreach ($objects as $obj) {
        echo "- $obj\n";
    }
}

// Check the OLD folder too
echo "\nChecking OLD folder (documentation/):\n";
try {
    $reflection = new ReflectionClass($minio);
    $configProperty = $reflection->getProperty('config');
    $configProperty->setAccessible(true);
    $config = $configProperty->getValue($minio);

    $s3ClientProperty = $reflection->getProperty('client');
    $s3ClientProperty->setAccessible(true);
    $client = $s3ClientProperty->getValue($minio);

    $result = $client->listObjects([
        'Bucket' => $bucket,
        'Prefix' => 'documentation/',
    ]);

    if (isset($result['Contents'])) {
        foreach ($result['Contents'] as $obj) {
            echo "- " . $obj['Key'] . "\n";
        }
    } else {
        echo "No objects found in prefix: documentation/\n";
    }
} catch (Exception $e) {
    echo "Error checking old folder: " . $e->getMessage() . "\n";
}
