<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TicketModel;
use App\Models\TicketMessageModel;
use App\Models\UserModel;

/**
 * FetchEmailReplies
 *
 * Spark CLI command untuk memantau kotak masuk IMAP dan mensinkronisasi
 * balasan email dari user ke tiket yang sesuai di aplikasi Helpdesk.
 *
 * Implementasi menggunakan cURL (bukan php imap_* extension) karena
 * cURL mendukung IMAP/IMAPS di semua environment termasuk Alpine Linux.
 *
 * Cara jalankan via CLI:
 *   php spark cron:fetch-email-replies
 *
 * Cara jalankan via crontab server (setiap 2 menit):
 *   * /2 * * * * php /var/www/html/spark cron:fetch-email-replies >> /dev/null 2>&1
 */
class FetchEmailReplies extends BaseCommand
{
    protected $group       = 'Helpdesk';
    protected $name        = 'cron:fetch-email-replies';
    protected $description = 'Memantau inbox IMAP dan mensinkronisasi balasan email user ke tiket yang sesuai.';
    protected $usage       = 'cron:fetch-email-replies';

    /** @var string URL dasar IMAP */
    private string $imapBaseUrl;
    /** @var string Username IMAP */
    private string $imapUser;
    /** @var string Password IMAP */
    private string $imapPass;

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $db->query("SET time_zone = '+07:00'");

        CLI::write('[Email Reply Sync] Memulai polling IMAP...', 'yellow');

        // ── 1. Ambil konfigurasi IMAP dari .env ──
        $imapHost    = trim(env('IMAP_HOST', 'imap.gmail.com'));
        $imapPort    = (int) trim(env('IMAP_PORT', '993'));
        $this->imapUser = trim(env('IMAP_USER', env('email.SMTPUser', '')));
        $this->imapPass = trim(env('IMAP_PASS', env('email.SMTPPass', '')));
        $imapMailbox    = trim(env('IMAP_MAILBOX', 'INBOX'));

        if (empty($this->imapUser) || empty($this->imapPass)) {
            CLI::error('[Email Reply Sync] Konfigurasi IMAP_USER / IMAP_PASS tidak ditemukan di .env');
            return;
        }

        // URL format: imaps://host:port/MAILBOX
        $this->imapBaseUrl = 'imaps://' . $imapHost . ':' . $imapPort . '/' . $imapMailbox;

        CLI::write('[Email Reply Sync] Menghubungkan ke: ' . $imapHost . ':' . $imapPort . ' sebagai ' . $this->imapUser, 'cyan');

        // ── 2. Test koneksi dengan EXAMINE ──
        $examineResult = $this->imapCommand('EXAMINE ' . $imapMailbox);
        if ($examineResult === false) {
            CLI::error('[Email Reply Sync] Gagal terhubung ke IMAP server.');
            CLI::write('', 'white');
            CLI::write('📌 Kemungkinan penyebab:', 'yellow');
            CLI::write('   1. IMAP belum diaktifkan di Gmail account ' . $this->imapUser, 'white');
            CLI::write('      → Buka: Gmail → Settings → See all settings → Forwarding and POP/IMAP → Enable IMAP', 'cyan');
            CLI::write('   2. App Password tidak valid (cek kembali di Google Account Security)', 'white');
            log_message('error', '[EmailReplies] IMAP connection failed | User: ' . $this->imapUser);
            return;
        }

        CLI::write('[Email Reply Sync] Koneksi IMAP berhasil.', 'green');

        // ── 3. Cari email UNSEEN ──
        $searchResult = $this->imapCommand('UID SEARCH UNSEEN');
        if ($searchResult === false) {
            CLI::write('[Email Reply Sync] Gagal melakukan pencarian email.', 'red');
            return;
        }

        // Parse UID dari response "* SEARCH 1 2 3 ..."
        preg_match_all('/\b(\d+)\b/', $searchResult, $matches);
        $uids = $matches[1] ?? [];

        if (empty($uids)) {
            CLI::write('[Email Reply Sync] Tidak ada email baru yang belum dibaca.', 'cyan');
            return;
        }

        CLI::write('[Email Reply Sync] Ditemukan ' . count($uids) . ' email belum dibaca.', 'cyan');

        $ticketModel  = new TicketModel();
        $messageModel = new TicketMessageModel();
        $userModel    = new UserModel();

        $processed = 0;
        $skipped   = 0;

        foreach ($uids as $uid) {
            try {
                // ── 4a. Ambil header email ──
                $headerRaw = $this->imapCommand('UID FETCH ' . $uid . ' (BODY[HEADER.FIELDS (SUBJECT FROM DATE)])');
                if ($headerRaw === false) {
                    $skipped++;
                    continue;
                }

                $subject   = $this->parseHeader($headerRaw, 'Subject');
                $fromEmail = strtolower(trim($this->parseFromEmail($headerRaw)));

                CLI::write('  → UID ' . $uid . ' | Dari: ' . $fromEmail . ' | Subjek: ' . mb_substr($subject, 0, 50), 'white');

                // ── 4b. Abaikan email dari sistem sendiri (loop prevention) ──
                $smtpUser = strtolower(trim(env('IMAP_USER', env('email.SMTPUser', ''))));
                if ($fromEmail === $smtpUser || str_contains($fromEmail, 'no-reply') || str_contains($fromEmail, 'noreply')) {
                    CLI::write('    ⏭ Diabaikan: email dari sistem sendiri atau no-reply.', 'dark_gray');
                    $this->markSeen($uid);
                    $skipped++;
                    continue;
                }

                // ── 4c. Cocokkan email pengirim dengan user di DB ──
                $sender = $userModel->where('LOWER(email)', $fromEmail)->first();
                if (!$sender) {
                    CLI::write('    ⏭ Diabaikan: email pengirim tidak terdaftar (' . $fromEmail . ').', 'dark_gray');
                    $this->markSeen($uid);
                    $skipped++;
                    continue;
                }

                // ── 4d. Identifikasi tiket dari Subject/Body ──
                $bodyRaw = $this->imapCommand('UID FETCH ' . $uid . ' (BODY[TEXT])') ?: '';
                $ticket  = $this->findTicketFromEmail($subject, $bodyRaw, $ticketModel);

                if (!$ticket) {
                    CLI::write('    ⏭ Diabaikan: tidak ditemukan referensi tiket yang valid.', 'dark_gray');
                    $this->markSeen($uid);
                    $skipped++;
                    continue;
                }

                // ── 4e. Pastikan pengirim adalah reporter tiket (untuk role user/3) ──
                if ($sender['role_id'] == 3 && $sender['id'] != $ticket['reporter_id']) {
                    CLI::write('    ⏭ Diabaikan: pengirim bukan reporter tiket #' . $ticket['id'] . '.', 'dark_gray');
                    $this->markSeen($uid);
                    $skipped++;
                    continue;
                }

                // ── 4f. Decode dan bersihkan body email ──
                $bodyText = $this->extractCleanBody($bodyRaw);

                if (empty(trim($bodyText))) {
                    CLI::write('    ⏭ Diabaikan: body email kosong setelah dibersihkan.', 'dark_gray');
                    $this->markSeen($uid);
                    $skipped++;
                    continue;
                }

                // ── 4g. Simpan pesan ke ticket_messages ──
                $messageModel->insert([
                    'ticket_id'   => $ticket['id'],
                    'sender_id'   => $sender['id'],
                    'message'     => $bodyText,
                    'is_internal' => 0,
                    'sent_at'     => date('Y-m-d H:i:s'),
                    'source'      => 'email',
                ]);

                CLI::write('    ✅ Disimpan ke tiket #' . $ticket['id'] . ' dari ' . $sender['name'], 'green');
                log_message('info', '[EmailReplies] Pesan dari ' . $fromEmail . ' disimpan ke tiket #' . $ticket['id']);

                // ── 4h. Kirim notifikasi in-app ke staf ──
                helper('notification');
                $staffToNotify = $userModel->select('users.*')
                    ->join('roles', 'users.role_id = roles.id')
                    ->where('roles.is_staff', 1)
                    ->where('users.is_active', 1)
                    ->findAll();

                foreach ($staffToNotify as $staff) {
                    if ($staff['id'] != $sender['id']) {
                        add_notification(
                            $staff['id'],
                            'NEW_MESSAGE',
                            '📧 Balasan Email dari ' . $sender['name'],
                            'Balasan via email pada tiket: "' . $ticket['title'] . '" (ID: ' . $ticket['id'] . ')',
                            $ticket['id']
                        );
                    }
                }

                // ── 4i. Tandai email sebagai SEEN ──
                $this->markSeen($uid);

                $processed++;

            } catch (\Throwable $e) {
                CLI::error('    ❌ Error memproses UID ' . $uid . ': ' . $e->getMessage());
                log_message('error', '[EmailReplies] Error processing UID #' . $uid . ': ' . $e->getMessage());
                $this->markSeen($uid);
            }
        }

        CLI::write(
            '[Email Reply Sync] Selesai. Diproses: ' . $processed . ' | Diabaikan: ' . $skipped,
            'green'
        );
        log_message('info', '[EmailReplies] Polling selesai. Diproses: ' . $processed . ' | Diabaikan: ' . $skipped);
    }

    // ─────────────────────────────────────────────────────────────────────
    // IMAP via cURL
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Kirim IMAP command via cURL dan kembalikan response.
     */
    private function imapCommand(string $command): string|false
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->imapBaseUrl,
            CURLOPT_USERNAME       => $this->imapUser,
            CURLOPT_PASSWORD       => $this->imapPass,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => $command,
        ]);

        $result = curl_exec($ch);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', '[EmailReplies] cURL IMAP error: ' . $err . ' | Command: ' . $command);
            return false;
        }

        return $result;
    }

    /**
     * Tandai email sebagai SEEN (sudah dibaca) via cURL IMAP STORE.
     */
    private function markSeen(string $uid): void
    {
        $this->imapCommand('UID STORE ' . $uid . ' +FLAGS (\\Seen)');
    }

    // ─────────────────────────────────────────────────────────────────────
    // PARSING HELPERS
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Cari tiket dari Subject dan Body email.
     * Strategi (urutan prioritas):
     *   1. Token [REF:HDXXXX:TOKEN] di body
     *   2. [REF:HDXXXX] di subject
     *   3. Pola HD#### di subject
     */
    private function findTicketFromEmail(string $subject, string $bodyRaw, TicketModel $ticketModel): ?array
    {
        // Decode body untuk pencarian token
        $bodyDecoded = $this->decodeBodyContent($bodyRaw);

        // Strategi 1: token tersembunyi [REF:HDXXXX:TOKEN32char]
        if (preg_match('/\[REF:([A-Z0-9]+):([a-f0-9]{32})\]/i', $bodyDecoded, $m)) {
            $ticketId  = strtoupper($m[1]);
            $token     = strtolower($m[2]);
            $ticket    = $ticketModel->getTicketByEmailToken($token);
            if ($ticket && $ticket['id'] === $ticketId) {
                return $ticket;
            }
        }

        // Strategi 2: [REF:HDXXXX] di subject
        if (preg_match('/\[REF:(HD\d{4,})\]/i', $subject, $m)) {
            return $ticketModel->find(strtoupper($m[1])) ?: null;
        }

        // Strategi 3: HD#### di subject (format: Re: ... #HD0042 ...)
        if (preg_match('/\b(HD\d{4,})\b/i', $subject, $m)) {
            return $ticketModel->find(strtoupper($m[1])) ?: null;
        }

        return null;
    }

    /**
     * Ambil dan decode body email yang bersih (tanpa quoted reply lama).
     */
    private function extractCleanBody(string $fetchResponse): string
    {
        // Ekstrak konten dari IMAP FETCH response
        // Response format: * N FETCH (BODY[TEXT] {size}\r\nCONTENT\r\n)
        if (preg_match('/\{(\d+)\}\r\n(.*)/s', $fetchResponse, $m)) {
            $content = substr($m[2], 0, (int)$m[1]);
        } else {
            $content = $fetchResponse;
        }

        return $this->decodeBodyContent($content);
    }

    /**
     * Decode body: deteksi multipart, decode Base64/QP, strip HTML, hapus quoted reply.
     */
    private function decodeBodyContent(string $raw): string
    {
        $text = '';

        // Cek apakah multipart
        if (preg_match('/boundary="?([^"\r\n;]+)"?/i', $raw, $bm)) {
            $boundary = trim($bm[1]);
            $parts    = preg_split('/--' . preg_quote($boundary, '/') . '(?:--)?/', $raw);
            foreach ($parts as $part) {
                if (empty(trim($part))) continue;

                // Ambil part text/plain terlebih dahulu
                if (stripos($part, 'Content-Type: text/plain') !== false) {
                    $text = $this->decodePart($part);
                    break;
                }
            }
            // Fallback ke HTML part
            if (empty($text)) {
                foreach ($parts as $part) {
                    if (stripos($part, 'Content-Type: text/html') !== false) {
                        $text = strip_tags($this->decodePart($part));
                        break;
                    }
                }
            }
        } else {
            // Single part — deteksi encoding dari header
            $text = $this->decodePart($raw);
        }

        return $this->removeQuotedReply(trim($text));
    }

    /**
     * Decode satu part email (Base64 / Quoted-Printable / plain).
     */
    private function decodePart(string $part): string
    {
        // Pisahkan header dan body part
        $headerEnd = strpos($part, "\r\n\r\n");
        if ($headerEnd === false) {
            $headerEnd = strpos($part, "\n\n");
        }
        $partHeader = $headerEnd !== false ? substr($part, 0, $headerEnd) : '';
        $partBody   = $headerEnd !== false ? substr($part, $headerEnd + 4) : $part;

        // Deteksi charset
        $charset = 'UTF-8';
        if (preg_match('/charset="?([^"\s;]+)"?/i', $partHeader, $cm)) {
            $charset = strtoupper(trim($cm[1]));
        }

        // Deteksi transfer encoding
        if (preg_match('/Content-Transfer-Encoding:\s*([^\r\n]+)/i', $partHeader, $em)) {
            $encoding = strtolower(trim($em[1]));
            if ($encoding === 'base64') {
                $partBody = base64_decode(str_replace(["\r", "\n"], '', $partBody));
            } elseif ($encoding === 'quoted-printable') {
                $partBody = quoted_printable_decode($partBody);
            }
        }

        // Konversi ke UTF-8 jika perlu
        if ($charset !== 'UTF-8' && $charset !== 'UTF8') {
            $converted = @mb_convert_encoding($partBody, 'UTF-8', $charset);
            if ($converted !== false) {
                $partBody = $converted;
            }
        }

        return $partBody;
    }

    /**
     * Hapus bagian quoted reply (teks lama yang dikutip email client).
     */
    private function removeQuotedReply(string $body): string
    {
        // Hapus token tersembunyi jika masih ada
        $body = preg_replace('/\[REF:[A-Z0-9]+:[a-f0-9]{32}\]/i', '', $body ?? '');

        $lines      = preg_split('/\r\n|\r|\n/', $body ?? '');
        $cleanLines = [];

        foreach ($lines as $line) {
            $trimmed = ltrim($line);

            // Quoted reply: baris diawali >
            if (str_starts_with($trimmed, '>')) break;

            // Gmail/Outlook "On ... wrote:" separator
            if (preg_match('/^On .{10,} wrote:/i', $trimmed)) break;

            // Outlook "-----Original Message-----"
            if (preg_match('/^-{3,}.*(original message|pesan asli).*/i', $trimmed)) break;

            // "From:" di awal baris setelah beberapa baris (header quoted reply)
            if (preg_match('/^(From|Dari|De)\s*:/i', $trimmed) && count($cleanLines) > 2) break;

            // Signature marker
            if ($trimmed === '--') break;

            $cleanLines[] = $line;
        }

        return trim(implode("\n", $cleanLines));
    }

    /**
     * Parse nilai header dari raw IMAP FETCH header response.
     * Contoh: "Subject: Re: Test\r\n" → "Re: Test"
     */
    private function parseHeader(string $raw, string $headerName): string
    {
        if (preg_match('/' . preg_quote($headerName, '/') . ':\s*(.+?)(?:\r\n(?![ \t])|\z)/is', $raw, $m)) {
            $val = preg_replace('/\r\n[ \t]+/', ' ', trim($m[1]));
            return $this->decodeMimeStr($val);
        }
        return '';
    }

    /**
     * Ekstrak alamat email dari header "From:".
     * Menangani format: "Nama <email@domain.com>" dan "email@domain.com"
     */
    private function parseFromEmail(string $raw): string
    {
        $from = $this->parseHeader($raw, 'From');
        if (preg_match('/<([^>]+)>/', $from, $m)) {
            return strtolower(trim($m[1]));
        }
        return strtolower(trim($from));
    }

    /**
     * Decode MIME encoded string (=?UTF-8?B?...?= atau =?UTF-8?Q?...?=).
     */
    private function decodeMimeStr(string $str): string
    {
        // Base64 encoded: =?CHARSET?B?DATA?=
        $str = preg_replace_callback('/=\?([^?]+)\?B\?([^?]*)\?=/i', function ($m) {
            $decoded = base64_decode($m[2]);
            if (strtoupper($m[1]) !== 'UTF-8') {
                $decoded = @mb_convert_encoding($decoded, 'UTF-8', $m[1]);
            }
            return $decoded;
        }, $str);

        // Quoted-printable encoded: =?CHARSET?Q?DATA?=
        $str = preg_replace_callback('/=\?([^?]+)\?Q\?([^?]*)\?=/i', function ($m) {
            $decoded = quoted_printable_decode(str_replace('_', ' ', $m[2]));
            if (strtoupper($m[1]) !== 'UTF-8') {
                $decoded = @mb_convert_encoding($decoded, 'UTF-8', $m[1]);
            }
            return $decoded;
        }, $str);

        return $str ?? '';
    }
}
