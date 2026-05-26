<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class KbMigrateToMinio extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'App';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'kb:migrate-minio';
    protected $description = 'Migrate local KB markdown files to MinIO storage';
    protected $usage = 'kb:migrate-minio';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $minio = new \App\Libraries\MinioStorage();
        $articles = $db->table('kb_articles')->where('md_key', null)->get()->getResultArray();

        if (empty($articles)) {
            CLI::write('Semua artikel sudah memiliki md_key atau tidak ada artikel.', 'green');
            return;
        }

        $localDir = ROOTPATH . 'artikel/';
        if (!is_dir($localDir)) {
            CLI::error("Folder lokal tidak ditemukan: {$localDir}");
            return;
        }

        $success = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($articles as $article) {
            CLI::write("Memproses: {$article['title']}...");

            // Cari file lokal yang cocok
            // Prioritas 1: Berdasarkan slug (slug.md)
            // Prioritas 2: Berdasarkan title match? (agak berisiko, pakai slug saja)
            $filename = $article['slug'] . '.md';
            $localPath = $localDir . $filename;

            // Jika tidak ketemu dengan slug, coba scan file yang mungkin mirip? 
            // Untuk sekarang kita asumsikan slug.md adalah standar.
            if (!file_exists($localPath)) {
                // Fallback: coba cari file yang contains title atau slug di folder
                $files = glob($localDir . "*.md");
                $found = false;
                foreach ($files as $f) {
                    if (str_contains(basename($f), $article['slug'])) {
                        $localPath = $f;
                        $filename = basename($f);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    CLI::write("  - File tidak ditemukan untuk slug: {$article['slug']}", 'yellow');
                    $skipped++;
                    continue;
                }
            }

            try {
                $minioFilename = $article['slug'] . '.md'; // Standarisasi nama di MinIO
                $minio->upload($localPath, $minioFilename, 'documentation');

                $db->table('kb_articles')
                    ->where('id', $article['id'])
                    ->update(['md_key' => $minioFilename]);

                CLI::write("  - Berhasil diupload sebagai: documentation/{$minioFilename}", 'green');
                $success++;
            } catch (\Exception $e) {
                CLI::error("  - Gagal upload: " . $e->getMessage());
                $failed++;
            }
        }

        CLI::write("\nMigrasi Selesai!", 'blue');
        CLI::write("Berhasil: {$success}");
        CLI::write("Gagal: {$failed}");
        CLI::write("Skipped: {$skipped}");
    }
}
