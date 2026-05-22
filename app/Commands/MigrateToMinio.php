<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TicketModel;
use App\Libraries\MinioStorage;

class MigrateToMinio extends BaseCommand
{
    protected $group = 'Helpdesk';
    protected $name = 'minio:migrate';
    protected $description = 'Migrasi file foto tiket dari local storage (public/uploads/tickets/) ke MinIO object storage.';
    protected $usage = 'minio:migrate';
    protected $arguments = [];

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $minio = new MinioStorage();
        $ticketModel = new TicketModel();

        CLI::write('Memulai migrasi file foto ke MinIO...', 'yellow');
        CLI::newLine();

        // Ambil semua tiket yang memiliki foto (local path atau sudah MinIO key)
        $tickets = $ticketModel
            ->groupStart()
                ->where('photo IS NOT NULL')
                ->where('photo !=', '')
            ->groupEnd()
            ->orGroupStart()
                ->where('photo2 IS NOT NULL')
                ->where('photo2 !=', '')
            ->groupEnd()
            ->findAll();

        if (empty($tickets)) {
            CLI::write('Tidak ada tiket dengan foto untuk dimigrasi.', 'green');
            return;
        }

        $localPath = FCPATH . 'uploads/tickets/';
        $totalMigrated = 0;
        $totalSkipped = 0;
        $totalFailed = 0;

        $progress = CLI::showProgress(0, count($tickets));

        foreach ($tickets as $i => $ticket) {
            CLI::showProgress($i + 1, count($tickets));

            foreach (['photo', 'photo2'] as $field) {
                $value = $ticket[$field] ?? '';

                if (empty($value)) {
                    continue;
                }

                // Skip if already a MinIO key (no slashes means just a filename = already migrated)
                if (!str_contains($value, '/')) {
                    $totalSkipped++;
                    continue;
                }

                // Extract just the filename from the stored path (e.g., "uploads/tickets/photo_HD0001_xyz.jpg")
                $filename = basename($value);
                $sourceFile = $localPath . $filename;

                if (!file_exists($sourceFile)) {
                    CLI::error("  [GAGAL] File tidak ditemukan: {$sourceFile}");
                    $totalFailed++;
                    continue;
                }

                try {
                    $minio->upload($sourceFile, $filename);

                    // Update DB: replace full path with just the filename (MinIO key)
                    $ticketModel->update($ticket['id'], [$field => $filename]);

                    CLI::write("  [OK] Tiket #{$ticket['id']} {$field}: {$filename}", 'green');
                    $totalMigrated++;
                } catch (\Exception $e) {
                    CLI::error("  [GAGAL] Tiket #{$ticket['id']} {$field}: " . $e->getMessage());
                    $totalFailed++;
                }
            }
        }

        CLI::showProgress(false);
        CLI::newLine();
        CLI::write('══════════════════════════════════════', 'cyan');
        CLI::write('  HASIL MIGRASI MINIO', 'cyan');
        CLI::write('══════════════════════════════════════', 'cyan');
        CLI::write("  Total tiket diproses : " . count($tickets));
        CLI::write("  Berhasil dimigrasi   : {$totalMigrated}", 'green');
        CLI::write("  Sudah di MinIO       : {$totalSkipped}", 'yellow');
        CLI::write("  Gagal                : {$totalFailed}", $totalFailed > 0 ? 'red' : 'green');
        CLI::write('══════════════════════════════════════', 'cyan');
        CLI::newLine();

        if ($totalFailed > 0) {
            CLI::warning('Beberapa file gagal dimigrasi. Cek log untuk detail.');
        } else {
            CLI::write('Migrasi selesai!', 'green');
        }
    }
}
