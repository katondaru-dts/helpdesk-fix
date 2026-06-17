<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketAssigneeModel extends Model
{
    protected $table         = 'ticket_assignees';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['ticket_id', 'user_id', 'assigned_by', 'assigned_at'];

    /**
     * Ambil semua teknisi yang ditugaskan pada sebuah tiket, lengkap dengan data user.
     */
    public function getAssigneesByTicket(string $ticketId): array
    {
        return $this->select('ticket_assignees.*, users.name, users.email')
            ->join('users', 'ticket_assignees.user_id = users.id', 'left')
            ->where('ticket_assignees.ticket_id', $ticketId)
            ->orderBy('ticket_assignees.assigned_at', 'ASC')
            ->findAll();
    }

    /**
     * Ambil hanya array user_id untuk sebuah tiket.
     */
    public function getAssigneeIds(string $ticketId): array
    {
        $rows = $this->select('user_id')
            ->where('ticket_id', $ticketId)
            ->findAll();

        return array_column($rows, 'user_id');
    }

    /**
     * Sinkronisasi penugasan: hapus lama, insert baru.
     * Mengembalikan array user_id yang BARU ditambahkan (belum ada sebelumnya).
     */
    public function syncAssignees(string $ticketId, array $userIds, int $assignedBy): array
    {
        $existingIds = $this->getAssigneeIds($ticketId);

        // Hapus yang tidak ada lagi di list baru
        $toRemove = array_diff($existingIds, $userIds);
        if (!empty($toRemove)) {
            $this->where('ticket_id', $ticketId)
                ->whereIn('user_id', array_values($toRemove))
                ->delete();
        }

        // Insert yang belum ada
        $toAdd = array_diff($userIds, $existingIds);
        $newRows = [];
        $now     = date('Y-m-d H:i:s');
        foreach ($toAdd as $uid) {
            $newRows[] = [
                'ticket_id'   => $ticketId,
                'user_id'     => (int) $uid,
                'assigned_by' => $assignedBy,
                'assigned_at' => $now,
            ];
        }
        if (!empty($newRows)) {
            $this->insertBatch($newRows);
        }

        return array_values($toAdd);
    }
}
