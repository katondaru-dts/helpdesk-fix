<?php

/**
 * Queue Helper
 * Helper untuk memicu (trigger) antrean pekerjaan di latar belakang.
 */

if (!function_exists('trigger_queue_worker')) {
    /**
     * Memicu eksekusi queue worker secara asinkron tanpa menunggu respon.
     * Menggunakan cURL dengan timeout sangat rendah.
     *
     * @return void
     */
    function trigger_queue_worker()
    {
        // Memanggil route process-queue via CLI PHP di latar belakang (background process Linux)
        // Ini lebih aman di lingkungan Docker karena menghindari masalah resolusi DNS / Port Mapping internal
        $command = "php " . FCPATH . "index.php cron/process-queue > /dev/null 2>&1 &";
        exec($command);
    }
}
