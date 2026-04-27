<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TicketModel;
use App\Models\TicketMessageModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        $userRole = $session->get('role_id');
        $userId = $session->get('user_id') ?? $session->get('id');

        $ticketModel = new TicketModel();
        $userModel = new UserModel();
        $messageModel = new TicketMessageModel();

        $data = [
            'pageTitle' => 'Dashboard — Helpdesk',
            'activePage' => 'dashboard'
        ];

        if (has_permission('Update Status Tiket')) {
            // Admin or Support
            $data['stats'] = [
                'total' => $ticketModel->countAllResults(),
                'open' => $ticketModel->where('status', 'OPEN')->countAllResults(),
                'inProgress' => $ticketModel->where('status', 'IN_PROGRESS')->countAllResults(),
                'pending' => $ticketModel->where('status', 'PENDING')->countAllResults(),
                'resolved' => $ticketModel->whereIn('status', ['RESOLVED', 'CLOSED'])->countAllResults(),
                'users' => $userModel->where('is_active', 1)->countAllResults(),
                'unassigned' => $ticketModel->where('assigned_to', null)->whereNotIn('status', ['RESOLVED', 'CLOSED'])->countAllResults(),
                'urgent' => $ticketModel->whereIn('priority', ['HIGH', 'URGENT'])->whereNotIn('status', ['RESOLVED', 'CLOSED'])->countAllResults(),
            ];

            // Highlight Notifikasi: Pesan terbaru dari Reporter (User)
            $data['recentMessages'] = $messageModel->select('ticket_messages.*, users.name as sender_name, tickets.title as ticket_title')
                ->join('tickets', 'ticket_messages.ticket_id = tickets.id')
                ->join('users', 'ticket_messages.sender_id = users.id')
                ->where('users.role_id', 3) // Hanya pesan dari role User
                ->where('ticket_messages.is_internal', 0)
                ->orderBy('ticket_messages.sent_at', 'DESC')
                ->limit(5)
                ->findAll();

            // Fetch Urgent Tickets
            $data['urgentTickets'] = $ticketModel->select('tickets.*, reporter.name as reporter_name')
                ->join('users as reporter', 'tickets.reporter_id = reporter.id', 'left')
                ->whereIn('priority', ['HIGH', 'URGENT'])
                ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
                ->orderBy('created_at', 'DESC')
                ->limit(6)
                ->findAll();

            // Fetch Category Stats
            $db = \Config\Database::connect();
            $data['categoryStats'] = $db->query("
                SELECT c.id as cat_id, c.name as cat_name,
                    COUNT(t.id) as total,
                    SUM(CASE WHEN t.status = 'OPEN' THEN 1 ELSE 0 END) as open_count,
                    SUM(CASE WHEN t.status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as inprogress_count,
                    SUM(CASE WHEN t.status = 'PENDING' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN t.status IN ('RESOLVED','CLOSED') THEN 1 ELSE 0 END) as resolved_count
                FROM categories c
                LEFT JOIN tickets t ON t.cat_id = c.id
                WHERE c.is_active = 1
                GROUP BY c.id, c.name
                ORDER BY total DESC
            ")->getResultArray();

            $data['usersConfigured'] = $userModel->where('dept_id IS NOT NULL')->countAllResults();
            $data['usersUnconfigured'] = $userModel->where('dept_id', null)->countAllResults();

            // Fetch Belum Diassign
            $data['pendingTickets'] = $ticketModel->select('tickets.*, categories.name as cat_name')
                ->join('categories', 'tickets.cat_id = categories.id', 'left')
                ->where('assigned_to', null)
                ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();

            // --- CHART DATA GENERATION ---
            
            $filter = $this->request->getGet('filter') ?? 'minggu_ini';
            $daysCount = 6;
            $startDay = 0;
            
            if ($filter === 'hari_ini') {
                $daysCount = 0;
                $startDay = 0;
            } else if ($filter === 'kemarin') {
                $daysCount = 1;
                $startDay = 1; // We'll show only yesterday if we want, but for trend, maybe yesterday and the day before? 
                // Let's make it show only the selected day if it's hari_ini or kemarin to be precise.
            } else if ($filter === 'minggu_ini') {
                $daysCount = 6;
                $startDay = 0;
            } else if ($filter === 'bulan_ini') {
                $daysCount = 29;
                $startDay = 0;
            }

            // Kinerja Tim Teknisi (Tiket Selesai per Teknisi)
            $techs = $userModel->where('role_id', 2)->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

            $lineData = ['labels' => [], 'datasets' => []];
            $colors = ['#F43F5E', '#8B5CF6', '#F59E0B', '#3B82F6', '#10B981', '#06B6D4', '#EC4899', '#14B8A6', '#6366F1', '#F97316', '#84CC16', '#A855F7', '#EF4444', '#0EA5E9', '#22C55E'];

            foreach ($techs as $idx => $tech) {
                $fullName = ucwords(strtolower($tech['name']));
                $lineData['datasets'][] = [
                    'label' => $fullName,
                    'data'  => [],
                    'color' => $colors[$idx % count($colors)]
                ];
            }

            $loopStart = $daysCount;
            $loopEnd = 0;
            
            // If today or yesterday, show at least 3 days for a better trend line
            if ($filter === 'hari_ini') {
                $loopStart = 2; // Show H-2, H-1, Today
                $loopEnd = 0;
            } else if ($filter === 'kemarin') {
                $loopStart = 3; // Show H-3, H-2, Yesterday
                $loopEnd = 1;
            }

            for ($i = $loopStart; $i >= $loopEnd; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                
                $label = date('d M', strtotime($date));
                if ($filter === 'hari_ini' && $i == 0) $label = "Hari Ini";
                else if ($filter === 'kemarin' && $i == 1) $label = "Kemarin";
                
                $lineData['labels'][] = $label;
                
                if (!empty($techs)) {
                    foreach ($techs as $idx => $tech) {
                        $count = $db->query("
                            SELECT COUNT(id) as c 
                            FROM tickets 
                            WHERE assigned_to = {$tech['id']} 
                              AND status IN ('RESOLVED','CLOSED') 
                              AND DATE(updated_at) = '$date'
                        ")->getRow()->c;
                        $lineData['datasets'][$idx]['data'][] = (int)$count;
                    }
                }
            }
            $data['chartLine'] = json_encode($lineData);

            // Bar Chart: Rata-rata Waktu Respons (Jam) by Category (top 7)
            $barData = ['labels' => [], 'data' => []];
            
            $whereClause = "";
            if ($filter === 'hari_ini') {
                $whereClause = " AND DATE(t.updated_at) = CURDATE()";
            } else if ($filter === 'kemarin') {
                $whereClause = " AND DATE(t.updated_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            } else if ($filter === 'minggu_ini') {
                $whereClause = " AND t.updated_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)";
            } else if ($filter === 'bulan_ini') {
                $whereClause = " AND t.updated_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)";
            }

            $catAvg = $db->query("
                SELECT c.name, AVG(TIMESTAMPDIFF(HOUR, t.created_at, t.updated_at)) as avg_hours
                FROM tickets t
                JOIN categories c ON t.cat_id = c.id
                WHERE t.status IN ('RESOLVED','CLOSED') $whereClause
                GROUP BY c.id, c.name
                ORDER BY avg_hours DESC
                LIMIT 7
            ")->getResultArray();

            if (empty($catAvg)) {
                $barData['labels'] = ['Belum ada data'];
                $barData['data'] = [0];
            } else {
                foreach($catAvg as $c){
                    $barData['labels'][] = $c['name'];
                    // fallback to 0 if null
                    $val = $c['avg_hours'] !== null ? round((float)$c['avg_hours'], 1) : 0;
                    $barData['data'][] = $val;
                }
            }
            $data['chartBar'] = json_encode($barData);

            return view('dashboard/admin', $data);
        }
        else {
            // Regular User
            $data['stats'] = [
                'total' => $ticketModel->where('reporter_id', $userId)->countAllResults(),
                'open' => $ticketModel->where('reporter_id', $userId)->where('status', 'OPEN')->countAllResults(),
                'inProgress' => $ticketModel->where('reporter_id', $userId)->where('status', 'IN_PROGRESS')->countAllResults(),
                'pending' => $ticketModel->where('reporter_id', $userId)->where('status', 'PENDING')->countAllResults(),
                'resolved' => $ticketModel->where('reporter_id', $userId)->whereIn('status', ['RESOLVED', 'CLOSED'])->countAllResults(),
            ];

            $data['recentTickets'] = $ticketModel->where('reporter_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();

            // Highlight Notifikasi: Pesan terbaru dari IT Support
            $data['recentMessages'] = $messageModel->select('ticket_messages.*, users.name as sender_name, tickets.title as ticket_title')
                ->join('tickets', 'ticket_messages.ticket_id = tickets.id')
                ->join('users', 'ticket_messages.sender_id = users.id')
                ->where('tickets.reporter_id', $userId)
                ->where('ticket_messages.sender_id !=', $userId)
                ->where('ticket_messages.is_internal', 0)
                ->orderBy('ticket_messages.sent_at', 'DESC')
                ->limit(5)
                ->findAll();

            return view('dashboard/user', $data);
        }
    }
}
