<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\TicketModel;
use App\Models\CategoryModel;
use App\Models\DepartmentModel;
use App\Models\TicketHistoryModel;
use App\Models\TicketMessageModel;
use App\Models\UserModel;
use App\Models\NotificationModel;
use App\Models\AuditLogModel;
use App\Models\TicketAssigneeModel;
use App\Libraries\MinioStorage;


class Tickets extends BaseController
{
    public function _remap($method, ...$params)
    {
        // Sanitize method to prevent path traversal / method injection
        $method = preg_replace('/[^a-zA-Z0-9_\-]/', '', $method);

        if ($method === 'view' || $method === 'detail') {
            return $this->detail(...$params);
        }

        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function index()
    {
        $ticketModel = new TicketModel();
        $catModel = new CategoryModel();
        $deptModel = new DepartmentModel();
        $userModel = new UserModel();

        $session = session();
        $isStaff = is_staff();
        $userId = $session->get('id');

        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('f-status'),
            'priority' => $this->request->getGet('f-priority'),
            'cat_id' => $this->request->getGet('f-cat'),
            'dept_id' => $this->request->getGet('f-dept'),
            'assigned_to' => $this->request->getGet('f-assigned'),
            'date_from' => $this->request->getGet('f-from'),
            'date_to' => $this->request->getGet('f-to'),
            'unassigned' => $this->request->getGet('f-unassigned'),
            'overdue' => $this->request->getGet('f-overdue'),
            'sort' => $this->request->getGet('sort') ?: 'created_at',
            'dir' => $this->request->getGet('dir') ?: 'DESC',
        ];

        $query = $ticketModel->getFilteredTickets($filters, $isStaff, $userId);

        // Load list of technicians (support staff) for filter dropdown
        $technicians = $userModel->select('users.*')
            ->join('roles', 'users.role_id = roles.id')
            ->where('roles.is_technician', 1)
            ->where('users.is_active', 1)
            ->orderBy('users.name', 'ASC')
            ->findAll();

        $data = [
            'pageTitle' => $isStaff ? 'Semua Tiket' : 'Tiket Saya',
            'activePage' => 'tickets',
            'tickets' => $query->paginate(10),
            'pager' => $ticketModel->pager,
            'categories' => $catModel->findAll(),
            'departments' => $deptModel->findAll(),
            'technicians' => $technicians,
            'filters' => $filters,
            'isStaff' => $isStaff,
            'totalRows' => $query->countAllResults(false)
        ];

        return view('tickets/index', $data);
    }

    public function export()
    {
        $ticketModel = new TicketModel();
        $session = session();
        $isStaff = is_staff();
        $userId = $session->get('id');

        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('f-status'),
            'priority' => $this->request->getGet('f-priority'),
            'cat_id' => $this->request->getGet('f-cat'),
            'dept_id' => $this->request->getGet('f-dept'),
            'assigned_to' => $this->request->getGet('f-assigned'),
            'date_from' => $this->request->getGet('f-from'),
            'date_to' => $this->request->getGet('f-to'),
            'unassigned' => $this->request->getGet('f-unassigned'),
        ];

        $tickets = $ticketModel->getFilteredTickets($filters, $isStaff, $userId)->findAll();

        $filename = "Helpdesk_Tiket_" . date('Ymd_His') . ".xls";

        $headers = ['ID', 'Judul', 'Nama Pemohon', 'Kategori'];
        if ($isStaff) {
            $headers[] = 'Pelapor';
        }
        $headers = array_merge($headers, ['Prioritas', 'Status', 'SLA', 'Lokasi', 'Link Dokumentasi']);
        if ($isStaff) {
            $headers[] = 'Ditangani';
        }
        $headers[] = 'Tanggal';

        $data = [
            'tickets' => $tickets,
            'isStaff' => $isStaff,
            'headers' => $headers
        ];

        return response()
            ->setHeader('Content-Type', 'application/vnd.ms-excel')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody(view('tickets/export_excel', $data));
    }

    public function view($id)
    {
        return $this->detail($id);
    }

    public function detail($id)
    {
        $ticketModel = new TicketModel();
        $historyModel = new TicketHistoryModel();
        $messageModel = new TicketMessageModel();
        $userModel = new UserModel();

        $ticket = $ticketModel->getTicketDetail($id);
        if (!$ticket) {
            return redirect()->to('/tickets')->with('error', 'Tiket tidak ditemukan.');
        }

        // Resolve photo URLs: MinIO key → presigned URL, local path → base_url()
        $minio = new MinioStorage();
        $resolvedPhotoUrls = [];
        foreach (['photo', 'photo2'] as $field) {
            if (!empty($ticket[$field])) {
                if (is_minio_key($ticket[$field])) {
                    $url = $minio->getPresignedUrl($ticket[$field]) ?? '';
                    $ticket[$field] = $url;
                    if ($url)
                        $resolvedPhotoUrls[] = $url;
                } else {
                    $url = base_url($ticket[$field]);
                    $ticket[$field] = $url;
                    $resolvedPhotoUrls[] = $url;
                }
            }
        }
        // Fallback: if drive_link is empty but photos exist, show photo URLs as documentation link
        $ticket['display_link'] = !empty($ticket['drive_link']) ? $ticket['drive_link'] : implode("\n", $resolvedPhotoUrls);

        $session = session();
        $userId = $session->get('id');
        $roleId = $session->get('role_id');
        $isStaff = is_staff();

        // Ambil gabungan izin Role + Izin Khusus User
        $rolePerms = $session->get('permissions') ?: [];
        $specialPerms = $session->get('user_permissions') ?: [];
        $userPerms = array_unique(array_merge($rolePerms, $specialPerms));

        $canAssign = in_array('Full Access', $userPerms) || in_array('Tugaskan Support', $userPerms) || is_admin();
        $canUpdateStatus = in_array('Full Access', $userPerms) || in_array('Update Status Tiket', $userPerms) || is_admin();

        // Meloloskan jika user punya izin khusus tertentu (selain cuma lihat tiket sendiri) atau memang staff
        $hasManagementPerm = in_array('Full Access', $userPerms) || in_array('Update Status Tiket', $userPerms) || in_array('Tugaskan Support', $userPerms);

        if (!$isStaff && !$hasManagementPerm && $ticket['reporter_id'] != $userId) {
            return redirect()->to('/tickets')->with('error', 'Akses ditolak.');
        }

        // Load multi-assignees from pivot table
        $assigneeModel   = new TicketAssigneeModel();
        $ticketAssignees = $assigneeModel->getAssigneesByTicket($id); // [{user_id, name, ...}]
        $assigneeIds     = array_column($ticketAssignees, 'user_id');
        $hasAssignees    = count($assigneeIds) > 0 || !empty($ticket['assigned_to']);

        // Teknisi hanya bisa lihat tiket yang ditugaskan kepadanya atau belum diassign
        // KECUALI jika user punya izin manajemen (Full Access, Update Status, dsb)
        if (!$hasManagementPerm && is_technician() && $hasAssignees) {
            $isAssigned = in_array($userId, $assigneeIds) || $ticket['assigned_to'] == $userId;
            if (!$isAssigned) {
                return redirect()->to('/tickets')->with('error', 'Akses ditolak. Tiket ini tidak ditugaskan kepada Anda.');
            }
        }

        $history = $historyModel->select('ticket_history.*, users.name as changed_by_name')
            ->join('users', 'ticket_history.changed_by = users.id', 'left')
            ->where('ticket_id', $id)
            ->orderBy('changed_at', 'ASC')
            ->findAll();

        $messagesQuery = $messageModel->select('ticket_messages.*, users.name as sender_name')
            ->join('users', 'ticket_messages.sender_id = users.id', 'left')
            ->where('ticket_id', $id);

        if (!$isStaff) {
            $messagesQuery->where('is_internal', 0);
        }
        $messages = $messagesQuery->orderBy('sent_at', 'ASC')->findAll();


        $timeline = [];
        foreach ($history as $h) {
            $timeline[] = ['type' => 'status', 'at' => $h['changed_at'], 'status' => $h['status'], 'notes' => $h['notes'], 'by' => $h['changed_by_name']];
        }
        foreach ($messages as $m) {
            $photoUrl = null;
            if (!empty($m['photo'])) {
                if (is_minio_key($m['photo'])) {
                    $photoUrl = $minio->getPresignedUrl($m['photo'], 'foto balasan tiket');
                } else {
                    $photoUrl = base_url($m['photo']);
                }
            }
            $timeline[] = [
                'type' => 'msg',
                'at' => $m['sent_at'],
                'msg' => $m['message'],
                'by' => $m['sender_name'],
                'internal' => $m['is_internal'],
                'photo' => $photoUrl
            ];
        }
        usort($timeline, function ($a, $b) {
            return strtotime($a['at']) - strtotime($b['at']);
        });

        $supports = $userModel->select('users.*')
            ->join('roles', 'users.role_id = roles.id')
            ->where('roles.is_technician', 1)
            ->where('users.is_active', 1)
            ->findAll();

        $data = [
            'pageTitle'       => "Detail Tiket: " . $ticket['id'],
            'activePage'      => 'tickets',
            'ticket'          => $ticket,
            'timeline'        => $timeline,
            'supports'        => $supports,
            'isStaff'         => $isStaff,
            'canAssign'       => $canAssign,
            'canUpdateStatus' => $canUpdateStatus,
            'userId'          => $userId,
            'ticketAssignees' => $ticketAssignees,  // array lengkap [{name, ...}]
            'assigneeIds'     => $assigneeIds,       // array user_id yang sudah dicentang
        ];

        return view('tickets/detail', $data);
    }

    public function reply($id)
    {
        $messageModel = new TicketMessageModel();
        $ticketModel = new TicketModel();
        $message = $this->request->getPost('message');
        $isInternal = $this->request->getPost('is_internal') ? 1 : 0;
        $session = session();

        if ($message || $this->request->getFile('photo')) {
            $data = [
                'ticket_id' => $id,
                'sender_id' => $session->get('id'),
                'message' => $message ?: '',
                'is_internal' => $isInternal
            ];

            // Handle Photo Upload (Technician Check Proof)
            $file = $this->request->getFile('photo');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                $ext = strtolower($file->getExtension());
                if (in_array($file->getMimeType(), $allowedTypes) && in_array($ext, ['jpg', 'jpeg', 'png']) && $file->getSize() <= 20 * 1024 * 1024) {
                    $newName = 'msg_' . $id . '_' . $file->getRandomName();
                    $folder = 'foto balasan tiket';

                    if ($file->move(FCPATH . $folder, $newName)) {
                        $fullPath = FCPATH . $folder . '/' . $newName;

                        // Compress image before upload
                        helper('image');
                        compress_image($fullPath, $fullPath, 75, 1200);

                        $data['photo'] = $folder . '/' . $newName;

                        // Optional: Upload to MinIO if possible
                        try {
                            $minio = new MinioStorage();
                            $minio->upload(FCPATH . $folder . '/' . $newName, $newName, $folder);
                            $data['photo'] = $newName; // Store basename for MinIO compatibility
                        } catch (\Exception $e) {
                            log_message('error', '[Tickets] Reply photo MinIO upload failed: ' . $e->getMessage());
                        }
                    }
                }
            }

            $messageModel->insert($data);

            $ticket = $ticketModel->find($id);
            if ($ticket) {
                helper('notification');
                $senderId = $session->get('id');
                $senderName = $session->get('name');

                // Kumpulkan ID user yang perlu dinotifikasi
                $userIdsToNotify = [];

                if (!$isInternal) {
                    // Beritahu Pelapor (jika pengirim bukan pelapor)
                    if ($senderId != $ticket['reporter_id']) {
                        $userIdsToNotify[] = $ticket['reporter_id'];
                    }
                }

                // Beritahu SEMUA Teknisi yang ditugaskan (jika ada dan bukan si pengirim)
                $assigneeModelReply = new \App\Models\TicketAssigneeModel();
                $assigneeIdsReply = $assigneeModelReply->getAssigneeIds($id);
                if (!empty($ticket['assigned_to']) && !in_array($ticket['assigned_to'], $assigneeIdsReply)) {
                    $assigneeIdsReply[] = $ticket['assigned_to'];
                }
                foreach ($assigneeIdsReply as $uid) {
                    if ($senderId != $uid) {
                        $userIdsToNotify[] = $uid;
                    }
                }

                $userModel = new \App\Models\UserModel();

                // Selalu beritahu semua Administrator agar mereka bisa memantau semua aktivitas tiket
                $admins = $userModel->select('users.*')
                    ->join('roles', 'users.role_id = roles.id')
                    ->where('roles.is_staff', 1)
                    ->where('users.is_active', 1)
                    ->findAll();
                foreach ($admins as $admin) {
                    if ($admin['id'] != $senderId) {
                        $userIdsToNotify[] = $admin['id'];
                    }
                }

                // Jika tiket belum diassign, beritahu SEMUA staf (Admin, Support, Operator)
                if (empty($ticket['assigned_to'])) {
                    $staffToNotify = $userModel->select('users.*')
                        ->join('roles', 'users.role_id = roles.id')
                        ->where('roles.is_staff', 1)
                        ->where('users.is_active', 1)
                        ->findAll();
                    foreach ($staffToNotify as $staff) {
                        if ($staff['id'] != $senderId) {
                            $userIdsToNotify[] = $staff['id'];
                        }
                    }
                }

                // Hapus duplikasi ID user (agar tidak dikirim notif ganda ke orang yang sama)
                $userIdsToNotify = array_unique($userIdsToNotify);

                // Kirim notifikasi ke semua ID yang terkumpul
                $locationStr = !empty($ticket['location']) ? ' | Lokasi: ' . $ticket['location'] : '';
                foreach ($userIdsToNotify as $uid) {
                    add_notification(
                        $uid,
                        'NEW_MESSAGE',
                        $isInternal ? 'Pesan Internal Baru' : 'Balasan Pesan Baru',
                        'Pengirim: ' . $senderName . $locationStr . ' | Pada tiket: "' . $ticket['title'] . '"',
                        $id
                    );
                }
            }

            // Kirim notifikasi Telegram untuk balasan (hanya pesan publik)
            if (!$isInternal) {
                helper('telegram');
                $ticket = $ticketModel->getTicketDetail($id);
                if ($ticket) {
                    $telegramMsg = "💬 <b>BALASAN TIKET BARU</b>\n";
                    $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
                    $telegramMsg .= "📋 <b>ID Tiket:</b> {$id}\n";
                    $telegramMsg .= "📌 <b>Judul:</b> " . $ticket['title'] . "\n";
                    if (!empty($ticket['location'])) {
                        $telegramMsg .= "📍 <b>Lokasi:</b> " . $ticket['location'] . "\n";
                    }
                    $telegramMsg .= "👨‍🔧 <b>Teknisi:</b> " . ($ticket['assigned_name'] ?? 'Belum ditugaskan') . "\n";
                    $telegramMsg .= "✍️ <b>Pengirim:</b> " . $session->get('name') . "\n";
                    $telegramMsg .= "💬 <b>Pesan:</b> " . mb_substr($message, 0, 200) . (mb_strlen($message) > 200 ? '...' : '') . "\n";

                    // Tambahkan link foto jika ada
                    if (!empty($data['photo'])) {
                        $telegramPhotoUrl = null;
                        if (is_minio_key($data['photo'])) {
                            $minioForTele = new MinioStorage();
                            $telegramPhotoUrl = $minioForTele->getPresignedUrl($data['photo'], 'foto balasan tiket');
                        } else {
                            $telegramPhotoUrl = base_url($data['photo']);
                        }
                        if ($telegramPhotoUrl) {
                            $telegramMsg .= "\n📷 <b>Foto Bukti:</b> <a href=\"{$telegramPhotoUrl}\">Klik Lihat Foto</a>\n";
                        }
                    }

                    $telegramMsg .= "\n⏰ <b>Waktu:</b> " . date('d/m/Y H:i') . " WIB";
                    send_telegram($telegramMsg);

                    // Kirim email ke reporter jika: pengirim bukan reporter DAN reporter adalah role user (role_id=3)
                    $userModel2 = new \App\Models\UserModel();
                    $reporter = $userModel2->find($ticket['reporter_id']);
                    if (
                        $reporter &&
                        !empty($reporter['email']) &&
                        $reporter['role_id'] == 3 &&
                        $session->get('id') != $ticket['reporter_id']
                    ) {
                        helper('email');
                        // Resolve photo URL for email
                        $emailPhotoUrl = null;
                        if (!empty($data['photo'])) {
                            if (is_minio_key($data['photo'])) {
                                $minioForEmail = new MinioStorage();
                                $emailPhotoUrl = $minioForEmail->getPresignedUrl($data['photo'], 'foto balasan tiket') ?: null;
                            } else {
                                $emailPhotoUrl = base_url($data['photo']);
                            }
                        }
                        $emailBody = email_template_reply($ticket, $session->get('name'), $message, $emailPhotoUrl);
                        send_email_notification(
                            $reporter['email'],
                            $reporter['name'],
                            '[Helpdesk] Balasan Baru pada Tiket #' . $ticket['id'] . ': ' . $ticket['title'],
                            $emailBody
                        );
                    }
                }
            }

            return redirect()->back()->with('success', 'Balasan terkirim.');
        }
        return redirect()->back();
    }

    public function updateStatus($id)
    {
        $ticketModel = new TicketModel();
        $historyModel = new TicketHistoryModel();
        $newStatus = $this->request->getPost('new_status');
        $newPriority = $this->request->getPost('new_priority');
        $notes = $this->request->getPost('notes');
        $session = session();

        if ($newStatus || $newPriority) {
            $db = \Config\Database::connect();
            $db->transStart();

            $ticket = $ticketModel->find($id);
            $updateData = [];

            // Proteksi & Assignees Logic
            $ticketAssigneeModel = new \App\Models\TicketAssigneeModel();
            $assigneeIds = $ticketAssigneeModel->getAssigneeIds($id);
            if (!empty($ticket['assigned_to']) && !in_array($ticket['assigned_to'], $assigneeIds)) {
                $assigneeIds[] = $ticket['assigned_to'];
            }
            $hasAssignees = count($assigneeIds) > 0;

            // PROTEKSI: Jika tiket sudah ada teknisinya, dan user saat ini BUKAN salah satu teknisinya, dan BUKAN admin/operator (role_id != 1 & role_id != 4)
            if ($hasAssignees && !in_array($session->get('role_id'), [1, 4])) {
                if (!in_array($session->get('id'), $assigneeIds)) {
                    return redirect()->back()->with('error', 'Maaf, tiket ini sudah ditangani oleh teknisi lain.');
                }
            }

            if ($newStatus) {
                $updateData['status'] = $newStatus;

                // Auto-Assign Logic: If status changed to IN_PROGRESS and ticket is unassigned
                if ($newStatus === 'IN_PROGRESS' && !$hasAssignees) {
                    $updateData['assigned_to'] = $session->get('id');
                    $ticketAssigneeModel->insert([
                        'ticket_id' => $id,
                        'user_id' => $session->get('id'),
                        'assigned_by' => $session->get('id'),
                        'assigned_at' => date('Y-m-d H:i:s')
                    ]);
                    $notes = ($notes ? $notes . " | " : "") . "Sistem: Tiket otomatis ditugaskan kepada " . $session->get('name');
                }

                // Pause SLA Logic
                if ($newStatus === 'PENDING' && $ticket['status'] !== 'PENDING') {
                    // Berhenti: Simpan waktu mulai pause
                    $updateData['sla_paused_at'] = date('Y-m-d H:i:s');
                } elseif ($ticket['status'] === 'PENDING' && $newStatus !== 'PENDING') {
                    // Jalan lagi: Geser deadline berdasarkan durasi pause
                    if (isset($ticket['sla_paused_at']) && $ticket['sla_paused_at'] && isset($ticket['sla_deadline']) && $ticket['sla_deadline']) {
                        $pauseTime = time() - strtotime($ticket['sla_paused_at']);
                        $newDeadline = date('Y-m-d H:i:s', strtotime($ticket['sla_deadline']) + $pauseTime);
                        $updateData['sla_deadline'] = $newDeadline;
                        $updateData['sla_paused_at'] = null;
                    }
                }
            }

            if ($newPriority && $newPriority !== $ticket['priority']) {
                $updateData['priority'] = $newPriority;
                // Recalculate SLA deadline from creation time if priority changes
                $updateData['sla_deadline'] = $ticketModel->calculateSlaDeadline($newPriority, $ticket['created_at']);
                $notes = ($notes ? $notes . " | " : "") . "Sistem: Prioritas diubah ke " . $newPriority;
            }

            if (!empty($updateData)) {
                $ticketModel->update($id, $updateData);

                $historyModel->insert([
                    'ticket_id' => $id,
                    'status' => $newStatus ?: $ticket['status'],
                    'notes' => $notes,
                    'changed_by' => $session->get('id')
                ]);
            }

            // Notify ticket creator about status change
            if ($newStatus) {
                $statusLabel = [
                    'OPEN' => 'Terbuka',
                    'IN_PROGRESS' => 'Sedang Diproses',
                    'PENDING' => 'Ditunda',
                    'RESOLVED' => 'Terselesaikan',
                    'CLOSED' => 'Ditutup',
                ][$newStatus] ?? $newStatus;

                if ($ticket && $ticket['reporter_id'] && $ticket['reporter_id'] != $session->get('id')) {
                    helper('notification');
                    $locationStr = !empty($ticket['location']) ? ' | Lokasi: ' . $ticket['location'] : '';
                    add_notification(
                        $ticket['reporter_id'],
                        'STATUS_CHANGE',
                        'Status Tiket Diperbarui',
                        'Status berubah menjadi: ' . $statusLabel . $locationStr . ' | Pada tiket: "' . $ticket['title'] . '"',
                        $id
                    );

                    // Kirim email ke reporter jika reporter adalah role user (role_id=3), kecuali status CLOSED
                    $userModelEmail = new \App\Models\UserModel();
                    $reporter = $newStatus !== 'CLOSED' ? $userModelEmail->find($ticket['reporter_id']) : null;
                    if ($reporter && !empty($reporter['email']) && $reporter['role_id'] == 3) {
                        helper('email');
                        $ticketDetail = $ticketModel->getTicketDetail($id);
                        if ($newStatus === 'RESOLVED') {
                            $emailBody = email_template_resolved(
                                $ticketDetail ?: $ticket,
                                $session->get('name'),
                                $notes ?? ''
                            );
                            $emailSubject = '[Helpdesk] Tiket #' . $ticket['id'] . ' Telah Terselesaikan';
                        } else {
                            $emailBody = email_template_status_change(
                                $ticketDetail ?: $ticket,
                                $newStatus,
                                $session->get('name'),
                                $notes ?? ''
                            );
                            $emailSubject = '[Helpdesk] Status Tiket #' . $ticket['id'] . ' Diperbarui: ' . $statusLabel;
                        }
                        send_email_notification(
                            $reporter['email'],
                            $reporter['name'],
                            $emailSubject,
                            $emailBody
                        );
                    }
                }

                // Kirim notifikasi Telegram perubahan status
                helper('telegram');
                $statusEmoji = [
                    'OPEN' => '🔴',
                    'IN_PROGRESS' => '🟡',
                    'PENDING' => '⏸️',
                    'RESOLVED' => '🟢',
                    'CLOSED' => '✅',
                ][$newStatus] ?? '🔵';
                $telegramMsg = "{$statusEmoji} <b>STATUS TIKET DIPERBARUI</b>\n";
                $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
                $telegramMsg .= "📋 <b>ID Tiket:</b> {$id}\n";
                $telegramMsg .= "📌 <b>Judul:</b> " . $ticket['title'] . "\n";
                if (!empty($ticket['location'])) {
                    $telegramMsg .= "📍 <b>Lokasi:</b> " . $ticket['location'] . "\n";
                }
                $updatedTicket = $ticketModel->getTicketDetail($id);
                $telegramMsg .= "👨‍🔧 <b>Teknisi:</b> " . ($updatedTicket['assigned_name'] ?? 'Belum ditugaskan') . "\n";
                $telegramMsg .= "{$statusEmoji} <b>Status Baru:</b> {$statusLabel}\n";
                $telegramMsg .= "👤 <b>Diubah oleh:</b> " . $session->get('name') . "\n";
                if ($notes) {
                    $telegramMsg .= "📝 <b>Catatan:</b> {$notes}\n";
                }
                $telegramMsg .= "⏰ <b>Waktu:</b> " . date('d/m/Y H:i') . " WIB";
                send_telegram($telegramMsg);
            }

            $db->transComplete();

            if ($db->transStatus() !== false) {
                $auditLog = new AuditLogModel();
                $auditLog->logAction('UPDATE_STATUS', 'tickets', $id, ['status' => $newStatus, 'priority' => $newPriority]);
            }

            return redirect()->back()->with('success', 'Tiket diperbarui.');
        }
        return redirect()->back();
    }

    public function bulkUpdateStatus()
    {
        $session = session();
        $rolePerms = $session->get('permissions') ?: [];
        $specialPerms = $session->get('user_permissions') ?: [];
        $userPerms = array_unique(array_merge($rolePerms, $specialPerms));

        $canUpdate = in_array('Full Access', $userPerms) || in_array('Update Status Tiket', $userPerms) || is_staff();

        if (!$canUpdate) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah status tiket.');
        }

        $ids = $this->request->getPost('ticket_ids');
        $newStatus = $this->request->getPost('bulk_status');

        $validStatuses = ['OPEN', 'IN_PROGRESS', 'PENDING', 'RESOLVED', 'CLOSED'];
        if (empty($ids) || !is_array($ids) || !in_array($newStatus, $validStatuses)) {
            return redirect()->back()->with('error', 'Pilih tiket dan status yang valid.');
        }

        if ($newStatus === 'CLOSED' && is_technician() && !is_admin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah status ke CLOSED.');
        }

        $ticketModel = new TicketModel();
        $historyModel = new TicketHistoryModel();
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($ids as $id) {
            $id = trim($id);
            $ticket = $ticketModel->find($id);
            if (!$ticket)
                continue;

            $updateData = ['status' => $newStatus];
            if ($newStatus === 'IN_PROGRESS' && empty($ticket['assigned_to'])) {
                $updateData['assigned_to'] = $session->get('id');
            }
            if ($newStatus === 'PENDING' && $ticket['status'] !== 'PENDING') {
                $updateData['sla_paused_at'] = date('Y-m-d H:i:s');
            } elseif ($ticket['status'] === 'PENDING' && $newStatus !== 'PENDING') {
                if (!empty($ticket['sla_paused_at']) && !empty($ticket['sla_deadline'])) {
                    $pauseTime = time() - strtotime($ticket['sla_paused_at']);
                    $updateData['sla_deadline'] = date('Y-m-d H:i:s', strtotime($ticket['sla_deadline']) + $pauseTime);
                    $updateData['sla_paused_at'] = null;
                }
            }

            $ticketModel->update($id, $updateData);
            $historyModel->insert([
                'ticket_id' => $id,
                'status' => $newStatus,
                'notes' => 'Bulk update status oleh ' . $session->get('name'),
                'changed_by' => $session->get('id'),
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memperbarui status tiket.');
        }

        $auditLog = new AuditLogModel();
        $auditLog->logAction('BULK_UPDATE_STATUS', 'tickets', implode(',', $ids), ['status' => $newStatus]);

        return redirect()->back()->with('success', count($ids) . ' tiket berhasil diubah ke status ' . $newStatus . '.');
    }

    public function assign($id)
    {
        $session = session();
        $rolePerms    = $session->get('permissions') ?: [];
        $specialPerms = $session->get('user_permissions') ?: [];
        $userPerms    = array_unique(array_merge($rolePerms, $specialPerms));

        $canAssign = in_array('Full Access', $userPerms) || in_array('Tugaskan Support', $userPerms) || is_admin();

        if (!$canAssign) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan penugasan.');
        }

        $ticketModel   = new TicketModel();
        $historyModel  = new TicketHistoryModel();
        $userModel     = new UserModel();
        $assigneeModel = new TicketAssigneeModel();

        // Terima array dari checkbox (bisa kosong = lepas semua)
        $assigneeIds = $this->request->getPost('assignees') ?: [];
        $assigneeIds = array_map('intval', (array) $assigneeIds);
        $assigneeIds = array_filter($assigneeIds); // hilangkan 0 / null
        $assigneeIds = array_values(array_unique($assigneeIds));

        $ticket = $ticketModel->find($id);
        if (!$ticket) {
            return redirect()->back()->with('error', 'Tiket tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // --- Sync tabel pivot ticket_assignees ---
        $newlyAdded = $assigneeModel->syncAssignees($id, $assigneeIds, (int) $session->get('id'));

        // --- Update assigned_to (teknisi utama/PIC = pertama dalam list) ---
        $primaryAssignee = !empty($assigneeIds) ? $assigneeIds[0] : null;
        $ticketModel->update($id, ['assigned_to' => $primaryAssignee]);

        // --- Catat di riwayat tiket ---
        if (!empty($assigneeIds)) {
            $names = [];
            foreach ($assigneeIds as $uid) {
                $u = $userModel->find($uid);
                if ($u) $names[] = $u['name'];
            }
            $notes = 'Tiket ditugaskan kepada: ' . implode(', ', $names);
        } else {
            $notes = 'Tiket dilepas dari semua penugasan.';
        }

        $historyModel->insert([
            'ticket_id'  => $id,
            'status'     => $ticket['status'],
            'notes'      => $notes,
            'changed_by' => $session->get('id'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memperbarui penugasan.');
        }

        // --- Kirim notifikasi in-app ke teknisi yang BARU ditambahkan ---
        if (!empty($newlyAdded)) {
            helper('notification');
            foreach ($newlyAdded as $uid) {
                if ($uid != $session->get('id')) {
                    add_notification(
                        $uid,
                        'ASSIGNED',
                        'Penugasan Tiket Baru',
                        'Anda telah ditugaskan untuk menangani tiket: ' . $ticket['title'],
                        $id
                    );
                }
            }
        }

        // --- Kirim notifikasi Telegram ---
        helper('telegram');
        $assignedNames = [];
        foreach ($assigneeIds as $uid) {
            $u = $userModel->find($uid);
            if ($u) $assignedNames[] = $u['name'];
        }
        $telegramMsg  = "👨‍🔧 <b>PENUGASAN TIKET</b>\n";
        $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $telegramMsg .= "📋 <b>ID Tiket:</b> {$id}\n";
        $telegramMsg .= "📌 <b>Judul:</b> " . $ticket['title'] . "\n";
        if (!empty($ticket['location'])) {
            $telegramMsg .= "📍 <b>Lokasi:</b> " . $ticket['location'] . "\n";
        }
        $telegramMsg .= "👨‍🔧 <b>Teknisi:</b> " . (empty($assignedNames) ? 'Belum ditugaskan' : implode(', ', $assignedNames)) . "\n";
        $telegramMsg .= "👤 <b>Ditugaskan oleh:</b> " . $session->get('name') . "\n";
        $telegramMsg .= "⏰ <b>Waktu:</b> " . date('d/m/Y H:i') . " WIB";
        send_telegram($telegramMsg);

        $auditLog = new AuditLogModel();
        $auditLog->logAction('ASSIGN_TICKET', 'tickets', $id, ['assignees' => $assigneeIds]);

        return redirect()->back()->with('success', 'Penugasan diperbarui.');
    }


    public function create()
    {
        $catModel = new CategoryModel();
        $kbModel = new \App\Models\KbArticleModel();

        // Baca daftar unit dari CSV untuk dropdown lokasi
        $csvPath = ROOTPATH . 'namaunit.csv';
        $units = [];
        if (file_exists($csvPath)) {
            $handle = fopen($csvPath, 'r');
            $header = fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle)) !== false) {
                $name = trim($row[0] ?? '');
                if ($name !== '') {
                    $units[] = $name;
                }
            }
            fclose($handle);
        }
        $units = array_unique($units);
        sort($units);

        $data = [
            'pageTitle' => 'Buat Tiket Baru',
            'activePage' => 'ticket-create',
            'categories' => $catModel->orderBy('name', 'ASC')->findAll(),
            'units' => $units,
            'popularArticles' => $kbModel->getPopularArticles(2),
        ];
        return view('tickets/create', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|max_length[200]',
            'cat_id' => 'required',
            'description' => 'required',
            'location' => 'required',
            'requester_name' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ticketModel = new TicketModel();
        $historyModel = new TicketHistoryModel();
        $userModel = new UserModel();
        $session = session();

        $newId = $ticketModel->generateTicketId();

        $db = \Config\Database::connect();
        $db->transStart();

        $ticketTitle = $this->request->getPost('title');

        $priority = $this->request->getPost('priority') ?: 'MEDIUM';
        $slaDeadline = $ticketModel->calculateSlaDeadline($priority);

        $ticketModel->insert([
            'id' => $newId,
            'title' => $ticketTitle,
            'description' => $this->request->getPost('description'),
            'cat_id' => $this->request->getPost('cat_id'),
            'priority' => $priority,
            'reporter_id' => $session->get('id'),
            'requester_name' => $this->request->getPost('requester_name'),
            'dept_id' => $session->get('dept_id'),
            'location' => $this->request->getPost('location'),
            'drive_link' => $this->request->getPost('drive_link'),
            'status' => 'OPEN',
            'sla_deadline' => $slaDeadline
        ]);

        // ── Upload foto (opsional, max 2) — MinIO ──
        $photoFields = ['photo', 'photo2'];
        $allowedExts = ['jpg', 'jpeg', 'png'];
        $minio = new MinioStorage();

        foreach ($photoFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                $ext = strtolower($file->getExtension());
                if (in_array($file->getMimeType(), $allowedTypes) && in_array($ext, $allowedExts) && $file->getSize() <= 20 * 1024 * 1024) {
                    $fileName = $field . '_' . $newId . '_' . $file->getRandomName();
                    // Temporarily move to local, then upload to MinIO
                    $tempPath = FCPATH . 'uploads/tickets/' . $fileName;
                    if ($file->move(FCPATH . 'uploads/tickets', $fileName)) {
                        // Compress image before upload
                        helper('image');
                        compress_image($tempPath, $tempPath, 75, 1200);

                        try {
                            $minio->upload($tempPath, $fileName);
                            // Store only the basename (MinIO key — no path prefix)
                            $ticketModel->update($newId, [$field => $fileName]);
                            // Clean up temporary local file after successful upload
                            if (file_exists($tempPath)) {
                                unlink($tempPath);
                            }
                        } catch (\Exception $e) {
                            log_message('error', '[Tickets] MinIO upload failed: ' . $e->getMessage());
                            // Fallback: keep local file
                            $ticketModel->update($newId, [$field => 'uploads/tickets/' . $fileName]);
                        }
                    }
                }
            }
        }

        $historyModel->insert([
            'ticket_id' => $newId,
            'status' => 'OPEN',
            'notes' => 'Tiket dibuat oleh user',
            'changed_by' => $session->get('id')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat tiket.');
        }

        // Send notifications AFTER transaction completes
        helper('notification');
        helper('telegram');

        // 1. Notify the ticket creator (confirmation)
        add_notification(
            $session->get('id'),
            'NEW_TICKET',
            'Tiket Berhasil Dibuat',
            'Tiket Anda "' . $ticketTitle . '" (ID: ' . $newId . ') telah berhasil dibuat dan sedang menunggu penanganan.',
            $newId
        );

        // 1b. Kirim email konfirmasi ke reporter jika role user biasa (role_id=3)
        $reporter = $userModel->find($session->get('id'));
        if ($reporter && !empty($reporter['email']) && $reporter['role_id'] == 3) {
            helper('email');
            $ticketForEmail = [
                'id'       => $newId,
                'title'    => $ticketTitle,
                'location' => $this->request->getPost('location'),
                'priority' => $priority,
            ];
            $emailBody = email_template_ticket_created($ticketForEmail, $session->get('name'));
            send_email_notification(
                $reporter['email'],
                $reporter['name'],
                '[Helpdesk] Tiket #' . $newId . ' Berhasil Dibuat: ' . $ticketTitle,
                $emailBody
            );
        }

        // 2. Notify all staff
        $staffToNotify = $userModel->select('users.*')
            ->join('roles', 'users.role_id = roles.id')
            ->where('roles.is_staff', 1)
            ->where('users.is_active', 1)
            ->findAll();

        $locationStr = $this->request->getPost('location') ? ' | Lokasi: ' . $this->request->getPost('location') : '';
        foreach ($staffToNotify as $staff) {
            if ($staff['id'] != $session->get('id')) {
                add_notification(
                    $staff['id'],
                    'NEW_TICKET',
                    'Tiket Baru Masuk',
                    'Pengirim: ' . $session->get('name') . $locationStr . ' | Judul: "' . $ticketTitle . '" (ID: ' . $newId . ')',
                    $newId
                );
            }
        }

        // 3. Kirim notifikasi Telegram
        $location = $this->request->getPost('location');
        $priority = $this->request->getPost('priority') ?: 'MEDIUM';
        $priorityEmoji = ['LOW' => '🟢', 'MEDIUM' => '🟡', 'HIGH' => '🟠', 'URGENT' => '🔴'][$priority] ?? '🟡';
        $telegramMsg = "🎫 <b>TIKET BARU MASUK</b>\n";
        $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $telegramMsg .= "📋 <b>ID:</b> {$newId}\n";
        $telegramMsg .= "📌 <b>Judul:</b> {$ticketTitle}\n";
        $telegramMsg .= "{$priorityEmoji} <b>Prioritas:</b> {$priority}\n";
        $telegramMsg .= "👤 <b>Pelapor:</b> " . $session->get('name') . "\n";
        if ($location) {
            $telegramMsg .= "📍 <b>Lokasi:</b> {$location}\n";
        }
        $telegramMsg .= "👨‍🔧 <b>Teknisi:</b> Belum ditugaskan\n";
        $telegramMsg .= "⏰ <b>Waktu:</b> " . date('d/m/Y H:i') . " WIB\n";

        // Tambahkan link foto dokumentasi jika ada
        $finalTicket = $ticketModel->find($newId);
        if ($finalTicket) {
            $photoLinks = [];
            foreach (['photo', 'photo2'] as $f) {
                if (!empty($finalTicket[$f])) {
                    if (is_minio_key($finalTicket[$f])) {
                        $minioStore = new MinioStorage();
                        $pUrl = $minioStore->getPresignedUrl($finalTicket[$f]);
                        if ($pUrl)
                            $photoLinks[] = $pUrl;
                    } else {
                        $photoLinks[] = base_url($finalTicket[$f]);
                    }
                }
            }
            if (!empty($photoLinks)) {
                $telegramMsg .= "\n🖼️ <b>Dokumentasi Foto:</b>\n";
                foreach ($photoLinks as $idx => $link) {
                    $telegramMsg .= ($idx + 1) . ". <a href=\"{$link}\">Lihat Foto " . ($idx + 1) . "</a>\n";
                }
            }
        }

        $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $telegramMsg .= "🔗 Segera tangani di sistem helpdesk.";
        send_telegram($telegramMsg);

        return redirect()->to('/tickets/detail/' . $newId)->with('success', 'Tiket berhasil dibuat!');
    }

    public function updateLink($id)
    {
        $ticketModel = new TicketModel();
        $session = session();
        $rolePerms = $session->get('permissions') ?: [];
        $specialPerms = $session->get('user_permissions') ?: [];
        $userPerms = array_unique(array_merge($rolePerms, $specialPerms));

        // Staff can edit documentation link
        if (!is_staff() && !in_array('Full Access', $userPerms)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah link dokumentasi.');
        }

        $link = $this->request->getPost('drive_link');
        $ticketModel->update($id, ['drive_link' => $link]);
        return redirect()->back()->with('success', 'Link Dokumentasi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $session = session();
        $rolePerms = $session->get('permissions') ?: [];
        $specialPerms = $session->get('user_permissions') ?: [];
        $userPerms = array_unique(array_merge($rolePerms, $specialPerms));

        if (!is_admin() && !in_array('Full Access', $userPerms)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus tiket.');
        }

        $ticketModel = new TicketModel();
        $historyModel = new TicketHistoryModel();
        $messageModel = new TicketMessageModel();
        $notificationModel = new NotificationModel();

        $ticket = $ticketModel->find($id);
        if (!$ticket) {
            return redirect()->back()->with('error', 'Tiket tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Hapus file foto fisik & MinIO
        $minio = new MinioStorage();
        foreach (['photo', 'photo2'] as $field) {
            if (!empty($ticket[$field])) {
                $value = $ticket[$field];
                if (is_minio_key($value)) {
                    // Stored as MinIO key (just filename)
                    $minio->delete($value);
                } else {
                    // Legacy local file
                    $filePath = FCPATH . $value;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }

        // Delete related data first
        $historyModel->where('ticket_id', $id)->delete();
        $messageModel->where('ticket_id', $id)->delete();
        $notificationModel->where('ref_id', $id)->delete();

        // Delete ticket
        $ticketModel->delete($id);

        $db->transComplete();

        if ($db->transStatus() !== false) {
            $auditLog = new AuditLogModel();
            $auditLog->logAction('DELETE_TICKET', 'tickets', $id, $ticket);
        }

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menghapus tiket.');
        }

        return redirect()->to('/tickets')->with('success', 'Tiket berhasil dihapus.');
    }
}
