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

        // Kirim via cURL PHP (non-blocking dengan timeout rendah)
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FRESH_CONNECT => true,
        ]);
        curl_exec($ch);
        curl_close($ch);

        return true;
    }
}
