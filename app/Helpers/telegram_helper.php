<?php

/**
 * Telegram Helper
 * Mengirim pesan notifikasi ke Telegram via Bot API.
 */

if (!function_exists('send_telegram')) {
    /**
     * Kirim pesan teks ke Telegram.
     *
     * @param string $message  Teks pesan (mendukung HTML parse mode)
     * @param string|null $chatId  Override chat ID (default: dari .env)
     */
    function send_telegram(string $message, ?string $chatId = null): bool
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $target = $chatId ?? env('TELEGRAM_CHAT_ID');

        if (empty($token) || empty($target)) {
            log_message('warning', '[Telegram] Token atau Chat ID belum dikonfigurasi di .env');
            return false;
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $payload = json_encode([
            'chat_id' => $target,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);

        // Eksekusi CURL di background (Asinkron / Non-blocking)
        // Mencegah aplikasi melambat (lag) saat menunggu respon dari server Telegram
        $cmd = "curl -s -X POST " . escapeshellarg($url) . " -d " . escapeshellarg($payload) . " -H 'Content-Type: application/json' > /dev/null 2>&1 &";
        exec($cmd);

        return true;
    }
}
