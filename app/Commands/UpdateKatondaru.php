<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateKatondaru extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'custom:update-katondaru';
    protected $description = 'Update Katondaru permissions';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // 1. Update IT Support Role to match user screenshot
        $db->table('roles')->where('name', 'IT Support')->update([
            'permissions' => json_encode(['Lihat Laporan', 'Update Status Tiket', 'Tambah Solusi', 'Buat Tiket', 'Lihat Tiket Sendiri'])
        ]);
        CLI::write("Role IT Support updated.", "green");

        // 2. Update Katondaru's user permissions
        $katondaru = $db->table('users')->where('email', 'katondaru@unmer.ac.id')->get()->getRowArray();
        if ($katondaru) {
            $perms = json_decode($katondaru['permissions'] ?: '[]', true) ?: [];
            if (!in_array('Tugaskan Support', $perms)) {
                $perms[] = 'Tugaskan Support';
                $db->table('users')->where('id', $katondaru['id'])->update([
                    'permissions' => json_encode($perms)
                ]);
                CLI::write("Katondaru updated.", "green");
            } else {
                CLI::write("Katondaru already updated.", "yellow");
            }
        } else {
            CLI::write("User katondaru not found.", "red");
        }
    }
}
