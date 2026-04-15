<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id', 'title', 'description', 'cat_id', 'priority', 'reporter_id', 'assigned_to', 'dept_id', 'location', 'status', 'sla_deadline', 'sla_notified', 'sla_paused_at', 'drive_link', 'created_at', 'updated_at', 'closed_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    public function getFilteredTickets($filters = [], $isStaff = false, $userId = null)
    {
        $builder = $this->select('tickets.*, categories.name as cat_name, reporter.name as reporter_name, assigned.name as assigned_name')
            ->join('categories', 'tickets.cat_id = categories.id', 'left')
            ->join('users as reporter', 'tickets.reporter_id = reporter.id', 'left')
            ->join('users as assigned', 'tickets.assigned_to = assigned.id', 'left');

        if (!$isStaff && $userId !== null) {
            $builder->where('tickets.reporter_id', $userId);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('tickets.title', $filters['search'])
                ->orLike('tickets.id', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('tickets.status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $builder->where('tickets.priority', $filters['priority']);
        }

        if (!empty($filters['cat_id'])) {
            $builder->where('tickets.cat_id', $filters['cat_id']);
        }

        if ($isStaff && !empty($filters['dept_id'])) {
            $builder->where('tickets.dept_id', $filters['dept_id']);
        }

        if ($isStaff && !empty($filters['assigned_to'])) {
            $builder->where('tickets.assigned_to', $filters['assigned_to']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('tickets.created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('tickets.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        if (!empty($filters['unassigned']) && $isStaff) {
            $builder->where('tickets.assigned_to', null)
                ->whereNotIn('tickets.status', ['RESOLVED', 'CLOSED']);
        }

        return $builder->orderBy('tickets.created_at', 'DESC');
    }

    public function getTicketDetail($id)
    {
        return $this->select('tickets.*, reporter.name as reporter_name, reporter.email as reporter_email, 
                             departments.name as dept_name, categories.name as cat_name, assigned.name as assigned_name')
            ->join('users as reporter', 'tickets.reporter_id = reporter.id', 'left')
            ->join('departments', 'tickets.dept_id = departments.id', 'left')
            ->join('categories', 'tickets.cat_id = categories.id', 'left')
            ->join('users as assigned', 'tickets.assigned_to = assigned.id', 'left')
            ->where('tickets.id', $id)
            ->first();
    }

    public function generateTicketId()
    {
        $lastTicket = $this->orderBy('id', 'DESC')->first();
        if ($lastTicket) {
            $num = (int)substr($lastTicket['id'], 2) + 1;
            return 'HD' . str_pad($num, 4, '0', STR_PAD_LEFT);
        }
        return 'HD0001';
    }

    public function calculateSlaDeadline($priority, $createdAt = null)
    {
        $start = $createdAt ? strtotime($createdAt) : time();

        $durations = [
            'URGENT' => 2 * 3600, // 2 Jam
            'HIGH' => 5 * 3600, // 5 Jam
            'MEDIUM' => 12 * 3600, // 12 Jam
            'LOW' => 24 * 3600, // 24 Jam
        ];

        $duration = $durations[$priority] ?? $durations['MEDIUM'];
        return date('Y-m-d H:i:s', $start + $duration);
    }
}
