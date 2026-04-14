<?php
namespace App\Models;
use CodeIgniter\Model;
class TicketRatingModel extends Model
{
    protected $table = "ticket_ratings";
    protected $primaryKey = "id";
    protected $allowedFields = ["ticket_id", "rated_by", "rating", "feedback", "rated_at"];
    protected $useTimestamps = true;
    protected $createdField = 'rated_at';
    protected $updatedField = '';
}
