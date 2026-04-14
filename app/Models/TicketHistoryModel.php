<?php
namespace App\Models;
use CodeIgniter\Model;
class TicketHistoryModel extends Model
{
    protected $table = "ticket_history";
    protected $primaryKey = "id";
    protected $allowedFields = ["ticket_id", "status", "notes", "changed_by", "changed_at"];
    protected $useTimestamps = false;
}
