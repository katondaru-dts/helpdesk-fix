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

class Tickets extends BaseController
{
    public function _remap($method, ...$params)
    {
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
        $isStaff = ($session->get('role_id') == 1 || $session->get('role_id') == 2 || $session->get('role_id') == 4);
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

        $query = $ticketModel->getFilteredTickets($filters, $isStaff, $userId);

        // Load list of technicians (support staff) for filter dropdown
        $technicians = $userModel->whereIn('role_id', [2])->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

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
        $isStaff = ($session->get('role_id') == 1 || $session->get('role_id') == 2 || $session->get('role_id') == 4);
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

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<tr>
                <th style="background-color: #f2f2f2;">ID Tiket</th>
                <th style="background-color: #f2f2f2;">Judul</th>
                <th style="background-color: #f2f2f2;">Prioritas</th>
                <th style="background-color: #f2f2f2;">Status</th>
                <th style="background-color: #f2f2f2;">Pengaju</th>
                <th style="background-color: #f2f2f2;">Departemen</th>
                <th style="background-color: #f2f2f2;">Kategori</th>
                <th style="background-color: #f2f2f2;">Deskripsi</th>
                <th style="background-color: #f2f2f2;">Link Dokumentasi</th>
                <th style="background-color: #f2f2f2;">Tanggal Dibuat</th>
                <th style="background-color: #f2f2f2;">Assigned To</th>
              </tr>';

        foreach ($tickets as $row) {
            echo '<tr>';
            echo '<td>' . ($row['id']) . '</td>';
            echo '<td>' . ($row['title']) . '</td>';
            echo '<td>' . ($row['priority']) . '</td>';
            echo '<td>' . ($row['status']) . '</td>';
            echo '<td>' . ($row['reporter_name'] ?? '') . '</td>';
            echo '<td>' . ($row['dept_name'] ?? '') . '</td>';
            echo '<td>' . ($row['cat_name'] ?? '') . '</td>';
            echo '<td>' . ($row['description'] ?? '') . '</td>';
            echo '<td>' . ($row['drive_link'] ?? '') . '</td>';
            echo '<td>' . ($row['created_at']) . '</td>';
            echo '<td>' . ($row['assigned_name'] ?? 'Unassigned') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
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

        $session = session();
        $userId = $session->get('id');
        $roleId = $session->get('role_id');
        $isStaff = ($roleId == 1 || $roleId == 2 || $roleId == 4);

        $userPerms = $session->get('permissions') ?: [];
        $canAssign = in_array('Full Access', $userPerms) || in_array('Tugaskan Support', $userPerms) || $roleId == 1 || $roleId == 4;
        $canUpdateStatus = in_array('Full Access', $userPerms) || in_array('Update Status Tiket', $userPerms) || $roleId == 1 || $roleId == 2 || $roleId == 4;

        if (!$isStaff && $ticket['reporter_id'] != $userId) {
            return redirect()->to('/tickets')->with('error', 'Akses ditolak.');
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
            $timeline[] = ['type' => 'msg', 'at' => $m['sent_at'], 'msg' => $m['message'], 'by' => $m['sender_name'], 'internal' => $m['is_internal']];
        }
        usort($timeline, function ($a, $b) {
            return strtotime($a['at']) - strtotime($b['at']);
        });

        $supports = $userModel->where('role_id', 2)->where('is_active', 1)->findAll();

        $data = [
            'pageTitle' => "Detail Tiket: " . $ticket['id'],
            'activePage' => 'tickets',
            'ticket' => $ticket,
            'timeline' => $timeline,
            'supports' => $supports,
            'isStaff' => $isStaff,
            'canAssign' => $canAssign,
            'canUpdateStatus' => $canUpdateStatus,
            'userId' => $userId,
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

        if ($message) {
            $messageModel->insert([
                'ticket_id' => $id,
                'sender_id' => $session->get('id'),
                'message' => $message,
                'is_internal' => $isInternal
            ]);

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

                // Beritahu Teknisi yang ditugaskan (jika ada dan bukan si pengirim)
                if ($ticket['assigned_to'] && $senderId != $ticket['assigned_to']) {
                    $userIdsToNotify[] = $ticket['assigned_to'];
                }

                $userModel = new \App\Models\UserModel();

                // Selalu beritahu semua Administrator (Role 1) agar mereka bisa memantau semua aktivitas tiket
                $admins = $userModel->where('role_id', 1)->where('is_active', 1)->findAll();
                foreach ($admins as $admin) {
                    if ($admin['id'] != $senderId) {
                        $userIdsToNotify[] = $admin['id'];
                    }
                }

                // Jika tiket belum diassign, beritahu SEMUA staf (Admin, Support, Operator)
                if (empty($ticket['assigned_to'])) {
                    $staffToNotify = $userModel->whereIn('role_id', [1, 2, 4])->where('is_active', 1)->findAll();
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
                    $telegramMsg .= "⏰ <b>Waktu:</b> " . date('d/m/Y H:i') . " WIB";
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
                        $emailBody = email_template_reply($ticket, $session->get('name'), $message);
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

            if ($newStatus) {
                $updateData['status'] = $newStatus;

                // Auto-Assign Logic: If status changed to IN_PROGRESS and ticket is unassigned, 
                // assign it to the technician who is changing the status.
                if ($newStatus === 'IN_PROGRESS' && empty($ticket['assigned_to'])) {
                    $updateData['assigned_to'] = $session->get('id');
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

                    // Kirim email ke reporter jika status RESOLVED dan reporter adalah role user (role_id=3)
                    if ($newStatus === 'RESOLVED') {
                        $userModelEmail = new \App\Models\UserModel();
                        $reporter = $userModelEmail->find($ticket['reporter_id']);
                        if ($reporter && !empty($reporter['email']) && $reporter['role_id'] == 3) {
                            helper('email');
                            $ticketDetail = $ticketModel->getTicketDetail($id);
                            $emailBody = email_template_resolved(
                                $ticketDetail ?: $ticket,
                                $session->get('name'),
                                $notes ?? ''
                            );
                            send_email_notification(
                                $reporter['email'],
                                $reporter['name'],
                                '[Helpdesk] Tiket #' . $ticket['id'] . ' Telah Terselesaikan',
                                $emailBody
                            );
                        }
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
            return redirect()->back()->with('success', 'Tiket diperbarui.');
        }
        return redirect()->back();
    }

    public function assign($id)
    {
        $session = session();
        $userPerms = $session->get('permissions') ?: [];
        $canAssign = in_array('Full Access', $userPerms) || in_array('Tugaskan Support', $userPerms);

        if (!$canAssign) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan penugasan.');
        }

        $ticketModel = new TicketModel();
        $historyModel = new TicketHistoryModel();
        $userModel = new UserModel();

        $assigneeId = $this->request->getPost('assignee') ?: null;
        $ticket = $ticketModel->find($id);

        if (!$ticket) {
            return redirect()->back()->with('error', 'Tiket tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update Ticket
        $ticketModel->update($id, ['assigned_to' => $assigneeId]);

        // Log History
        $notes = "Tiket dilepas dari penugasan.";
        if ($assigneeId) {
            $user = $userModel->find($assigneeId);
            $userName = $user ? $user['name'] : 'User Unknown';
            $notes = "Tiket ditugaskan kepada: " . $userName;
        }

        $historyModel->insert([
            'ticket_id' => $id,
            'status' => $ticket['status'],
            'notes' => $notes,
            'changed_by' => $session->get('id')
        ]);

        // Send notification to the assigned teknisi
        if ($assigneeId && $assigneeId != $session->get('id')) {
            helper('notification');
            add_notification(
                $assigneeId,
                'ASSIGNED',
                'Penugasan Tiket Baru',
                'Anda telah ditugaskan untuk menangani tiket: ' . $ticket['title'],
                $id
            );
        }

        // Kirim notifikasi Telegram penugasan
        helper('telegram');
        $notifTicket = $ticketModel->getTicketDetail($id);
        $telegramMsg = "👨‍🔧 <b>PENUGASAN TIKET</b>\n";
        $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $telegramMsg .= "📋 <b>ID Tiket:</b> {$id}\n";
        $telegramMsg .= "📌 <b>Judul:</b> " . $ticket['title'] . "\n";
        if (!empty($ticket['location'])) {
            $telegramMsg .= "📍 <b>Lokasi:</b> " . $ticket['location'] . "\n";
        }
        $telegramMsg .= "👨‍🔧 <b>Teknisi:</b> " . ($notifTicket['assigned_name'] ?? 'Belum ditugaskan') . "\n";
        $telegramMsg .= "👤 <b>Ditugaskan oleh:</b> " . $session->get('name') . "\n";
        $telegramMsg .= "⏰ <b>Waktu:</b> " . date('d/m/Y H:i') . " WIB";
        send_telegram($telegramMsg);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memperbarui penugasan.');
        }

        return redirect()->back()->with('success', 'Penugasan diperbarui.');
    }


    public function create()
    {
        $catModel = new CategoryModel();
        $data = [
            'pageTitle' => 'Buat Tiket Baru',
            'activePage' => 'ticket-create',
            'categories' => $catModel->orderBy('name', 'ASC')->findAll(),
        ];
        return view('tickets/create', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|max_length[200]',
            'cat_id' => 'required',
            'description' => 'required',
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
            'dept_id' => $session->get('dept_id'),
            'location' => $this->request->getPost('location'),
            'drive_link' => $this->request->getPost('drive_link'),
            'status' => 'OPEN',
            'sla_deadline' => $slaDeadline
        ]);

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

        // 2. Notify all admins (role_id=1), IT Support (role_id=2) and operators (role_id=4)
        $staffToNotify = $userModel
            ->whereIn('role_id', [1, 2, 4])
            ->where('is_active', 1)
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
        $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $telegramMsg .= "🔗 Segera tangani di sistem helpdesk.";
        send_telegram($telegramMsg);

        return redirect()->to('/tickets/detail/' . $newId)->with('success', 'Tiket berhasil dibuat!');
    }

    public function delete($id)
    {
        $session = session();
        if ($session->get('role_id') != 1) {
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

        // Delete related data first
        $historyModel->where('ticket_id', $id)->delete();
        $messageModel->where('ticket_id', $id)->delete();
        $notificationModel->where('ref_id', $id)->delete();

        // Delete ticket
        $ticketModel->delete($id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menghapus tiket.');
        }

        return redirect()->to('/tickets')->with('success', 'Tiket berhasil dihapus.');
    }
}
