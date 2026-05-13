<?php

namespace App\Helpers;

class GeminiHelper
{
    private string $apiKey;
    private array $apiKeys = [];
    private string $embedModel = 'gemini-embedding-001';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    /**
     * Daftar model yang tersedia untuk chat.
     * Format: 'id' => ['label' => '...', 'model' => '...', 'description' => '...']
     */
    public static array $availableModels = [
        'flash' => [
            'label' => 'Gemini Flash',
            'model' => 'gemini-2.0-flash',
            'description' => 'Cepat & efisien, cocok untuk pertanyaan umum',
            'icon' => '⚡',
        ],
        'flash25' => [
            'label' => 'Gemini 2.5 Flash',
            'model' => 'gemini-2.5-flash',
            'description' => 'Lebih cerdas, terbaik untuk analisis mendalam',
            'icon' => '🧠',
        ],
        'pro' => [
            'label' => 'Gemini Pro',
            'model' => 'gemini-1.5-pro',
            'description' => 'Paling lengkap, untuk pertanyaan kompleks',
            'icon' => '🚀',
        ],
    ];

    /**
     * Urutan fallback: jika model utama gagal, coba model berikutnya.
     */
    private array $fallbackChain = ['flash25', 'flash', 'pro'];

    private string $selectedModelKey;

    public function __construct(string $modelKey = 'flash25')
    {
        // Kumpulkan semua API key yang tersedia (GEMINI_API_KEY, GEMINI_API_KEY_2, GEMINI_API_KEY_3)
        foreach (['GEMINI_API_KEY', 'GEMINI_API_KEY_2', 'GEMINI_API_KEY_3', 'GEMINI_API_KEY_4'] as $envKey) {
            $val = env($envKey, '');
            if (!empty($val)) $this->apiKeys[] = $val;
        }
        $this->apiKey = $this->apiKeys[0] ?? '';
        log_message('debug', 'GeminiHelper: ' . count($this->apiKeys) . ' API key(s) loaded.');
        $this->selectedModelKey = array_key_exists($modelKey, self::$availableModels)
            ? $modelKey
            : 'flash25';
    }

    /**
     * Model yang sedang aktif
     */
    public function getModelKey(): string
    {
        return $this->selectedModelKey;
    }

    public function getModelLabel(): string
    {
        return self::$availableModels[$this->selectedModelKey]['label'] ?? 'Gemini';
    }

    /**
     * Generate embedding vector untuk teks
     * @return float[]|null
     */
    public function embed(string $text): ?array
    {
        $text = mb_substr(strip_tags($text), 0, 2000);
        $url = "{$this->baseUrl}/models/{$this->embedModel}:embedContent?key={$this->apiKey}";

        $response = $this->post($url, [
            'model' => "models/{$this->embedModel}",
            'content' => ['parts' => [['text' => $text]]],
        ]);

        return $response['embedding']['values'] ?? null;
    }

    /**
     * Chat completion dengan konteks RAG + fallback otomatis ke model lain jika gagal.
     *
     * @param string $userMessage
     * @param string $context     Konteks dari Knowledge Base
     * @param bool   $kbFound     Apakah ada artikel relevan
     * @return string
     */
    public function chat(string $userMessage, string $context = '', bool $kbFound = false): string
    {
        if ($kbFound) {
            $systemPrompt = "Kamu adalah Helpdesk AI Pusim, asisten helpdesk Universitas Merdeka Malang.\n\n"
                . "INSTRUKSI:\n"
                . "- Jawab pertanyaan user HANYA berdasarkan Knowledge Base berikut.\n"
                . "- Gunakan bahasa yang ramah, jelas, dan ringkas.\n"
                . "- Jangan tambahkan pertanyaan di akhir jawaban.\n\n"
                . "=== KNOWLEDGE BASE PUSIM ===\n{$context}\n=== AKHIR KNOWLEDGE BASE ===";
        } else {
            $systemPrompt = "Kamu adalah Helpdesk AI Pusim, asisten helpdesk Universitas Merdeka Malang.\n\n"
                . "INSTRUKSI:\n"
                . "- Pertanyaan ini tidak ditemukan di Knowledge Base internal.\n"
                . "- Jawab menggunakan pengetahuan umum kamu dengan bahasa yang ramah dan jelas.\n"
                . "- Di awal jawaban, tambahkan catatan singkat: \"(Jawaban dari pengetahuan umum AI)\"\n"
                . "- Jangan tambahkan pertanyaan di akhir jawaban.";
        }

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nPertanyaan: " . $userMessage]]],
            ],
            'generationConfig' => ['maxOutputTokens' => 2048, 'temperature' => 0.7],
        ];

        // ── Coba model yang dipilih user dulu, lalu fallback ──
        $chain = $this->buildFallbackChain();

        foreach ($chain as $modelKey) {
            $modelId = self::$availableModels[$modelKey]['model'];
            $url = "{$this->baseUrl}/models/{$modelId}:generateContent?key={$this->apiKey}";

            try {
                $response = $this->post($url, $payload);
                $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($text !== null) {
                    // Kalau ini bukan model pilihan user (artinya sudah fallback), beri tanda
                    if ($modelKey !== $this->selectedModelKey) {
                        $label = self::$availableModels[$modelKey]['label'];
                        $text = "_(Dijawab oleh {$label} — model utama sedang tidak tersedia)_\n\n" . $text;
                    }
                    return $text;
                }
            } catch (\RuntimeException $e) {
                // Rate limit atau error — coba model berikutnya
                log_message('warning', "GeminiHelper: model {$modelKey} gagal ({$e->getMessage()}), mencoba fallback...");
                continue;
            }
        }

        return 'Maaf, semua model AI sedang tidak tersedia saat ini. Silakan buat tiket untuk mendapat bantuan.';
    }

    /**
     * Bangun urutan fallback: model pilihan user di depan, sisanya menyusul.
     */
    private function buildFallbackChain(): array
    {
        $chain = [$this->selectedModelKey];
        foreach ($this->fallbackChain as $key) {
            if ($key !== $this->selectedModelKey) {
                $chain[] = $key;
            }
        }
        return $chain;
    }

    /**
     * Cosine similarity antara dua vector
     */
    public static function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }
        $denom = sqrt($normA) * sqrt($normB);
        return $denom > 0 ? $dot / $denom : 0.0;
    }

    /**
     * Cari artikel paling relevan berdasarkan cosine similarity.
     * Fallback ke keyword search jika embed gagal (rate limit).
     *
     * @param array $articles hasil KbArticleModel::getForRag()
     * @return array top-N artikel
     */
    public function findRelevant(string $query, array $articles, int $topN = 3): array
    {
        if (empty($articles))
            return [];

        // ── Coba embedding dulu (model ringan: embedding-001) ──
        try {
            $queryVec = $this->embed($query);
        } catch (\RuntimeException $e) {
            $queryVec = null; // rate limit — fallback ke keyword
        }

        if ($queryVec) {
            $scored = [];
            foreach ($articles as $article) {
                if (empty($article['embedding']))
                    continue;
                $vec = is_string($article['embedding'])
                    ? json_decode($article['embedding'], true)
                    : $article['embedding'];
                if (!is_array($vec))
                    continue;
                $scored[] = ['article' => $article, 'score' => self::cosineSimilarity($queryVec, $vec)];
            }
            usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
            $filtered = array_filter($scored, fn($s) => $s['score'] >= 0.7);
            return array_map(fn($s) => $s['article'], array_slice(array_values($filtered), 0, $topN));
        }

        // ── Fallback: keyword search sederhana ──
        $words = array_filter(explode(' ', mb_strtolower($query)));
        $scored = [];
        foreach ($articles as $article) {
            $haystack = mb_strtolower(
                $article['title'] . ' ' . $article['excerpt'] . ' ' . mb_substr(strip_tags($article['content']), 0, 500)
            );
            $hits = 0;
            foreach ($words as $w) {
                if (mb_strlen($w) >= 3)
                    $hits += substr_count($haystack, $w);
            }
            if ($hits > 0)
                $scored[] = ['article' => $article, 'score' => $hits];
        }
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_map(fn($s) => $s['article'], array_slice($scored, 0, $topN));
    }

    private function post(string $url, array $data): array
    {
        $keys = !empty($this->apiKeys) ? $this->apiKeys : [$this->apiKey];

        foreach ($keys as $i => $key) {
            // Ganti key di URL
            $targetUrl = preg_replace('/key=[^&]+/', 'key=' . $key, $url);

            $ch = curl_init($targetUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 30,
            ]);
            $result   = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $decoded = json_decode($result ?: '{}', true) ?? [];

            if ($httpCode === 429) {
                log_message('warning', "GeminiHelper: API key ke-" . ($i + 1) . " rate limit (429), coba key berikutnya...");
                continue; // coba key berikutnya
            }
            if ($httpCode >= 500) {
                throw new \RuntimeException("Gemini API server error ({$httpCode}).");
            }

            // Sukses — set apiKey aktif ke key ini untuk request berikutnya
            $this->apiKey = $key;
            return $decoded;
        }

        // Semua key kena rate limit
        throw new \RuntimeException('Gemini API quota exceeded (429). Semua API key sedang rate limit.');
    }
}

