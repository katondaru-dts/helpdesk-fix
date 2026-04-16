<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TicketModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        $userRole = $session->get('role_id');
        $userId = $session->get('user_id') ?? $session->get('id');

        $ticketModel = new TicketModel();
        $userModel = new UserModel();

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
                'avgRating' => 0
            ];

            // Fetch Urgent Tickets
            $data['urgentTickets'] = $ticketModel->select('tickets.*, reporter.name as reporter_name')
                ->join('users as reporter', 'tickets.reporter_id = reporter.id', 'left')
                ->whereIn('priority', ['HIGH', 'URGENT'])
                ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
                ->orderBy('created_at', 'DESC')
                ->limit(6)
                ->findAll();

            // Fetch Pending Assignment
            $data['pendingTickets'] = $ticketModel->select('tickets.*, categories.name as cat_name')
                ->join('categories', 'tickets.cat_id = categories.id', 'left')
                ->where('assigned_to', null)
                ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
                ->orderBy('created_at', 'DESC')
                ->limit(6)
                ->findAll();

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

            // Recent My Tickets
            $data['recentTickets'] = $ticketModel->where('reporter_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();

            return view('dashboard/user', $data);
        }
    }
}
