<?php

namespace App\Controllers;

use App\Helpers\GeminiHelper;
use App\Models\KbArticleModel;

class AiAssistant extends BaseController
{
    public function chat()
    {
        $input = $this->request->getJSON(true);
        $message = trim($input['message'] ?? '');

        if (!$message) {
            return $this->response->setJSON(['error' => 'Pesan tidak boleh kosong'], 400);
        }

        // Auto-select model berdasarkan jenis pertanyaan
        $isOperational = self::isOperational($message);
        $modelKey      = $isOperational ? 'flash25' : 'flash';

        try {
            $gemini       = new GeminiHelper($modelKey);
            $articleModel = new KbArticleModel();
            $cache        = \Config\Services::cache();

            // Cache key berdasarkan hash pesan + model yang dipilih
            $cacheKey = 'ai_rag_' . md5($modelKey . '|' . $message);
            $cachedResult = $cache->get($cacheKey);

            if ($cachedResult !== null) {
                $relevant = $cachedResult;
            } else {
                $allArticles = $articleModel->getForRag();
                $relevant = $gemini->findRelevant($message, $allArticles, 3);
                if (!empty($relevant)) $cache->save($cacheKey, $relevant, 3600); // cache 1 jam, hanya jika ada hasil
            }

            // Bangun konteks dari artikel relevan
            $context = '';
            $sources = [];
            $kbFound = !empty($relevant);
            foreach ($relevant as $article) {
                $plain = mb_substr(strip_tags($article['content']), 0, 600);
                $context .= "## {$article['title']}\n{$plain}\n\n";
                $sources[] = ['title' => $article['title'], 'id' => $article['id'], 'slug' => $article['slug']];
            }

            // Generate jawaban
            $answer = $gemini->chat($message, $context, $kbFound);

            return $this->response->setJSON([
                'answer' => $answer,
                'sources' => $sources,
                'kb_found' => $kbFound,
                'suggest_ticket' => $isOperational,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'AiAssistant::chat - ' . $e->getMessage());
            return $this->response->setJSON([
                'answer' => 'Maaf, layanan AI sedang tidak tersedia saat ini.',
                'sources' => [],
                'kb_found' => false,
                'suggest_ticket' => true,
            ]);
        }
    }

    public function clearCache()
    {
        $cache = \Config\Services::cache();
        // Hapus semua cache ai_rag_*
        $path = WRITEPATH . 'cache/';
        $deleted = 0;
        foreach (glob($path . 'ai_rag_*') as $file) {
            unlink($file);
            $deleted++;
        }
        return $this->response->setJSON(['success' => true, 'deleted' => $deleted]);
    }

    /**
     * Deteksi apakah pertanyaan bersifat operasional/spesifik
     * yang butuh penanganan tim (bukan pertanyaan umum).
     */
    private static function isOperational(string $message): bool
    {
        $msg = mb_strtolower($message);

        // Indikator lokasi/waktu spesifik
        $locationTime = [
            'ruang',
            'lantai',
            'gedung',
            'lab ',
            'kantor',
            'tadi',
            'kemarin',
            'sejak',
            'dari tadi',
            'sudah lama',
            'berhari',
        ];

        // Indikator tindakan fisik yang butuh teknisi
        $physical = [
            'tidak bisa',
            'tidak nyala',
            'mati',
            'rusak',
            'error terus',
            'gagal terus',
            'tidak jalan',
            'tidak konek',
            'tidak connect',
            'tidak terdeteksi',
            'hilang',
            'terhapus',
            'kehilangan data',
            'tolong bantu',
            'mohon bantu',
            'butuh bantuan',
            'perlu bantuan',
        ];

        foreach (array_merge($locationTime, $physical) as $keyword) {
            if (str_contains($msg, $keyword))
                return true;
        }

        return false;
    }
}
