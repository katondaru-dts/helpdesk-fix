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

            // Cari file lokal yang cocok dengan logika lebih fleksibel
            $slug = $article['slug'];
            $normalizedSlug = str_replace(['-', '_'], '', $slug);
            $files = glob($localDir . "*.md");
            $foundPath = null;

            foreach ($files as $f) {
                $fname = strtolower(basename($f, '.md'));
                $normalizedFile = str_replace(['-', '_', ' '], '', $fname);

                if ($normalizedFile === $normalizedSlug || str_contains($normalizedFile, $normalizedSlug) || str_contains($normalizedSlug, $normalizedFile)) {
                    $foundPath = $f;
                    break;
                }
            }

            if (!$foundPath) {
                CLI::write("  - File tidak ditemukan untuk: {$article['title']}", 'yellow');
                $skipped++;
                continue;
            }

            try {
                $localPath = $foundPath;
                $minioFilename = $article['slug'] . '.md'; // Standarisasi nama di MinIO
                $minio->upload($localPath, $minioFilename, 'artikel');

                $db->table('kb_articles')
                    ->where('id', $article['id'])
                    ->update(['md_key' => $minioFilename]);

                CLI::write("  - Berhasil diupload sebagai: artikel/{$minioFilename}", 'green');
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
