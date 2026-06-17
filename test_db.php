<?php
require 'public/index.php'; // Boot CodeIgniter

$db = \Config\Database::connect();

// 1. Update IT Support Role to match user screenshot
$db->table('roles')->where('name', 'IT Support')->update([
    'permissions' => json_encode(['Lihat Laporan', 'Update Status Tiket', 'Tambah Solusi', 'Buat Tiket', 'Lihat Tiket Sendiri'])
]);

// 2. Update Katondaru's user permissions
$katondaru = $db->table('users')->where('email', 'katondaru@unmer.ac.id')->get()->getRowArray();
if ($katondaru) {
    // If Katondaru is an exception and needs to see all tickets, give them 'Tugaskan Support'
    // Let's merge it with their existing permissions if any
    $perms = json_decode($katondaru['permissions'] ?: '[]', true) ?: [];
    if (!in_array('Tugaskan Support', $perms)) {
        $perms[] = 'Tugaskan Support';
        // Also give them Full Access just in case, wait, 'Tugaskan Support' is enough to see all tickets and assign them.
        $db->table('users')->where('id', $katondaru['id'])->update([
            'permissions' => json_encode($perms)
        ]);
        echo "Updated Katondaru permissions.\n";
    }
}
echo "Role updated.\n";
