<?php

/**
 * Email Helper
 * Mengirim notifikasi email ke pengguna menggunakan CodeIgniter Email Library.
 */

if (!function_exists('send_email_notification')) {
    /**
     * Kirim email notifikasi ke user tertentu.
     *
     * @param string $toEmail   Alamat email tujuan
     * @param string $toName    Nama penerima
     * @param string $subject   Subjek email
     * @param string $body      Isi email (HTML)
     * @return bool
     */
    function send_email_notification(string $toEmail, string $toName, string $subject, string $body): bool
    {
        if (empty($toEmail)) {
            log_message('warning', '[Email] Alamat email tujuan kosong, email tidak dikirim.');
            return false;
        }

        try {
            $emailService = \Config\Services::email();

            $emailService->setTo($toEmail);
            $emailService->setSubject($subject);
            $emailService->setMessage($body);

            $result = $emailService->send(false);

            if (!$result) {
                log_message('error', '[Email] Gagal mengirim email ke ' . $toEmail . ' | Debug: ' . $emailService->printDebugger(['headers']));
            } else {
                log_message('info', '[Email] Email berhasil dikirim ke ' . $toEmail . ' | Subjek: ' . $subject);
            }

            return $result;
        } catch (\Throwable $e) {
            log_message('error', '[Email] Exception saat mengirim email: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('email_template_reply')) {
    /**
     * Buat template HTML email untuk notifikasi balasan komentar tiket.
     *
     * @param array  $ticket      Data tiket
     * @param string $senderName  Nama pengirim balasan
     * @param string $message     Isi pesan balasan
     * @return string             HTML email
     */
    function email_template_reply(array $ticket, string $senderName, string $message): string
    {
        $baseUrl = rtrim(env('app.baseURL') ?: base_url(), '/');
        $ticketUrl = $baseUrl . '/tickets/detail/' . $ticket['id'];
        $previewMsg = mb_substr(strip_tags($message), 0, 200) . (mb_strlen(strip_tags($message)) > 200 ? '...' : '');

        return '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Balasan Tiket Baru</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:\'Segoe UI\',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,#1a56db 0%,#1e40af 100%);padding:32px 40px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:-0.3px;">💬 Balasan Tiket Baru</h1>
              <p style="margin:8px 0 0;color:#bfdbfe;font-size:14px;">Helpdesk Universitas Merdeka Malang</p>
            </td>
          </tr>
          <!-- Body -->
          <tr>
            <td style="padding:36px 40px;">
              <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.6;">
                Tiket Anda mendapatkan balasan baru dari <strong style="color:#1a56db;">' . htmlspecialchars($senderName) . '</strong>.
              </p>

              <!-- Ticket Info Box -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:24px;">
                <tr>
                  <td style="padding:20px 24px;">
                    <p style="margin:0 0 8px;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Detail Tiket</p>
                    <p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>ID Tiket:</strong> <span style="font-family:monospace;background:#e0e7ff;color:#3730a3;padding:2px 8px;border-radius:4px;">' . htmlspecialchars($ticket['id']) . '</span></p>
                    <p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>Judul:</strong> ' . htmlspecialchars($ticket['title']) . '</p>
                    ' . (!empty($ticket['location']) ? '<p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>Lokasi:</strong> ' . htmlspecialchars($ticket['location']) . '</p>' : '') . '
                    <p style="margin:0;font-size:14px;color:#111827;"><strong>Teknisi:</strong> ' . htmlspecialchars($ticket['assigned_name'] ?? 'Belum ditugaskan') . '</p>
                  </td>
                </tr>
              </table>

              <!-- Message Preview -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border-radius:8px;border-left:4px solid #f59e0b;margin-bottom:28px;">
                <tr>
                  <td style="padding:16px 20px;">
                    <p style="margin:0 0 6px;font-size:12px;font-weight:600;color:#92400e;text-transform:uppercase;letter-spacing:0.5px;">Isi Pesan</p>
                    <p style="margin:0;font-size:14px;color:#374151;line-height:1.7;">' . nl2br(htmlspecialchars($previewMsg)) . '</p>
                  </td>
                </tr>
              </table>

              <!-- CTA Button -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <a href="' . $ticketUrl . '" style="display:inline-block;background:linear-gradient(135deg,#1a56db,#1e40af);color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:600;letter-spacing:0.3px;">
                      Lihat Tiket &amp; Balas
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td style="padding:20px 40px;background:#f8fafc;border-top:1px solid #e2e8f0;text-align:center;">
              <p style="margin:0;font-size:12px;color:#9ca3af;">Email ini dikirim otomatis oleh sistem Helpdesk UNMER. Jangan membalas email ini.</p>
              <p style="margin:6px 0 0;font-size:12px;color:#d1d5db;">Universitas Merdeka Malang &copy; ' . date('Y') . '</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
    }
}

if (!function_exists('email_template_status_change')) {
    /**
     * Buat template HTML email untuk notifikasi perubahan status tiket (selain RESOLVED).
     *
     * @param array  $ticket      Data tiket
     * @param string $newStatus   Status baru (OPEN, IN_PROGRESS, PENDING, CLOSED)
     * @param string $changedBy   Nama staf yang mengubah status
     * @param string $notes       Catatan (opsional)
     * @return string             HTML email
     */
    function email_template_status_change(array $ticket, string $newStatus, string $changedBy, string $notes = ''): string
    {
        $baseUrl = rtrim(env('app.baseURL') ?: base_url(), '/');
        $ticketUrl = $baseUrl . '/tickets/detail/' . $ticket['id'];

        $statusConfig = [
            'OPEN'        => ['label' => 'Terbuka',          'emoji' => '🔴', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca'],
            'IN_PROGRESS' => ['label' => 'Sedang Diproses',  'emoji' => '🟡', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'],
            'PENDING'     => ['label' => 'Ditunda',          'emoji' => '⏸️', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'],
            'CLOSED'      => ['label' => 'Ditutup',          'emoji' => '✅', 'color' => '#374151', 'bg' => '#f9fafb', 'border' => '#e5e7eb'],
        ];

        $cfg = $statusConfig[$newStatus] ?? ['label' => $newStatus, 'emoji' => '🔵', 'color' => '#1a56db', 'bg' => '#eff6ff', 'border' => '#bfdbfe'];

        return '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Tiket Diperbarui</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:\'Segoe UI\',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,' . $cfg['color'] . ' 0%,' . $cfg['color'] . 'cc 100%);padding:32px 40px;text-align:center;">
              <div style="font-size:48px;margin-bottom:12px;">' . $cfg['emoji'] . '</div>
              <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:-0.3px;">Status Tiket Diperbarui</h1>
              <p style="margin:8px 0 0;color:rgba(255,255,255,0.8);font-size:14px;">Helpdesk Universitas Merdeka Malang</p>
            </td>
          </tr>
          <!-- Body -->
          <tr>
            <td style="padding:36px 40px;">
              <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.6;">
                Status tiket Anda telah diperbarui menjadi <strong style="color:' . $cfg['color'] . ';">' . $cfg['label'] . '</strong> oleh <strong>' . htmlspecialchars($changedBy) . '</strong>.
              </p>

              <!-- Ticket Info Box -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:' . $cfg['bg'] . ';border-radius:8px;border:1px solid ' . $cfg['border'] . ';margin-bottom:24px;">
                <tr>
                  <td style="padding:20px 24px;">
                    <p style="margin:0 0 8px;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Detail Tiket</p>
                    <p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>ID Tiket:</strong> <span style="font-family:monospace;background:' . $cfg['border'] . ';color:' . $cfg['color'] . ';padding:2px 8px;border-radius:4px;">' . htmlspecialchars($ticket['id']) . '</span></p>
                    <p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>Judul:</strong> ' . htmlspecialchars($ticket['title']) . '</p>
                    ' . (!empty($ticket['location']) ? '<p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>Lokasi:</strong> ' . htmlspecialchars($ticket['location']) . '</p>' : '') . '
                    <p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>Teknisi:</strong> ' . htmlspecialchars($ticket['assigned_name'] ?? 'Belum ditugaskan') . '</p>
                    <p style="margin:0;font-size:14px;color:#111827;"><strong>Status Baru:</strong> <span style="color:' . $cfg['color'] . ';font-weight:600;">' . $cfg['emoji'] . ' ' . $cfg['label'] . '</span></p>
                  </td>
                </tr>
              </table>

              ' . (!empty($notes) ? '
              <!-- Notes Box -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border-radius:8px;border-left:4px solid #f59e0b;margin-bottom:24px;">
                <tr>
                  <td style="padding:16px 20px;">
                    <p style="margin:0 0 6px;font-size:12px;font-weight:600;color:#92400e;text-transform:uppercase;letter-spacing:0.5px;">Catatan</p>
                    <p style="margin:0;font-size:14px;color:#374151;line-height:1.7;">' . nl2br(htmlspecialchars($notes)) . '</p>
                  </td>
                </tr>
              </table>' : '') . '

              <!-- CTA Button -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <a href="' . $ticketUrl . '" style="display:inline-block;background:linear-gradient(135deg,' . $cfg['color'] . ',' . $cfg['color'] . 'cc);color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:600;letter-spacing:0.3px;">
                      Lihat Detail Tiket
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td style="padding:20px 40px;background:#f8fafc;border-top:1px solid #e2e8f0;text-align:center;">
              <p style="margin:0;font-size:12px;color:#9ca3af;">Email ini dikirim otomatis oleh sistem Helpdesk UNMER. Jangan membalas email ini.</p>
              <p style="margin:6px 0 0;font-size:12px;color:#d1d5db;">Universitas Merdeka Malang &copy; ' . date('Y') . '</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
    }
}

if (!function_exists('email_template_resolved')) {
    /**
     * Buat template HTML email untuk notifikasi tiket resolved.
     *
     * @param array  $ticket      Data tiket
     * @param string $changedBy   Nama staf yang mengubah status
     * @param string $notes       Catatan (opsional)
     * @return string             HTML email
     */
    function email_template_resolved(array $ticket, string $changedBy, string $notes = ''): string
    {
        $baseUrl = rtrim(env('app.baseURL') ?: base_url(), '/');
        $ticketUrl = $baseUrl . '/tickets/detail/' . $ticket['id'];

        return '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tiket Terselesaikan</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:\'Segoe UI\',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,#059669 0%,#047857 100%);padding:32px 40px;text-align:center;">
              <div style="font-size:48px;margin-bottom:12px;">✅</div>
              <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:-0.3px;">Tiket Anda Telah Terselesaikan</h1>
              <p style="margin:8px 0 0;color:#a7f3d0;font-size:14px;">Helpdesk Universitas Merdeka Malang</p>
            </td>
          </tr>
          <!-- Body -->
          <tr>
            <td style="padding:36px 40px;">
              <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.6;">
                Kabar baik! Tiket Anda telah ditandai sebagai <strong style="color:#059669;">Terselesaikan</strong> oleh <strong>' . htmlspecialchars($changedBy) . '</strong>.
              </p>

              <!-- Ticket Info Box -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;margin-bottom:24px;">
                <tr>
                  <td style="padding:20px 24px;">
                    <p style="margin:0 0 8px;font-size:12px;font-weight:600;color:#166534;text-transform:uppercase;letter-spacing:0.5px;">Detail Tiket</p>
                    <p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>ID Tiket:</strong> <span style="font-family:monospace;background:#dcfce7;color:#166534;padding:2px 8px;border-radius:4px;">' . htmlspecialchars($ticket['id']) . '</span></p>
                    <p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>Judul:</strong> ' . htmlspecialchars($ticket['title']) . '</p>
                    ' . (!empty($ticket['location']) ? '<p style="margin:0 0 6px;font-size:14px;color:#111827;"><strong>Lokasi:</strong> ' . htmlspecialchars($ticket['location']) . '</p>' : '') . '
                    <p style="margin:0;font-size:14px;color:#111827;"><strong>Diselesaikan oleh:</strong> ' . htmlspecialchars($ticket['assigned_name'] ?? $changedBy) . '</p>
                  </td>
                </tr>
              </table>

              ' . (!empty($notes) ? '
              <!-- Notes Box -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border-radius:8px;border-left:4px solid #f59e0b;margin-bottom:24px;">
                <tr>
                  <td style="padding:16px 20px;">
                    <p style="margin:0 0 6px;font-size:12px;font-weight:600;color:#92400e;text-transform:uppercase;letter-spacing:0.5px;">Catatan dari Teknisi</p>
                    <p style="margin:0;font-size:14px;color:#374151;line-height:1.7;">' . nl2br(htmlspecialchars($notes)) . '</p>
                  </td>
                </tr>
              </table>' : '') . '

              <!-- Satisfaction note -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#eff6ff;border-radius:8px;border:1px solid #bfdbfe;margin-bottom:28px;">
                <tr>
                  <td style="padding:16px 20px;">
                    <p style="margin:0;font-size:14px;color:#1e40af;line-height:1.6;">
                      ℹ️ Jika masalah Anda belum terselesaikan sepenuhnya, Anda dapat membuka kembali tiket ini dengan membalas melalui sistem helpdesk.
                    </p>
                  </td>
                </tr>
              </table>

              <!-- CTA Button -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <a href="' . $ticketUrl . '" style="display:inline-block;background:linear-gradient(135deg,#059669,#047857);color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:600;letter-spacing:0.3px;">
                      Lihat Detail Tiket
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td style="padding:20px 40px;background:#f8fafc;border-top:1px solid #e2e8f0;text-align:center;">
              <p style="margin:0;font-size:12px;color:#9ca3af;">Email ini dikirim otomatis oleh sistem Helpdesk UNMER. Jangan membalas email ini.</p>
              <p style="margin:6px 0 0;font-size:12px;color:#d1d5db;">Universitas Merdeka Malang &copy; ' . date('Y') . '</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
    }
}
