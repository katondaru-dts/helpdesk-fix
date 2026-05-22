<?php
namespace App\Models;
use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'name', 'permissions', 'is_staff', 'is_technician'];
    protected $useTimestamps = false;

    /**
     * Ambil semua role yang merupakan staff (admin, support, operator, dll).
     */
    public function getStaffRoles(): array
    {
        return $this->where('is_staff', 1)->findAll();
    }

    /**
     * Ambil semua role yang merupakan teknisi (bisa ditugaskan ke tiket).
     */
    public function getTechnicianRoles(): array
    {
        return $this->where('is_technician', 1)->findAll();
    }
}
