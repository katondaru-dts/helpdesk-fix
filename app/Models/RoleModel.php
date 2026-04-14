<?php
namespace App\Models;
use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'name', 'permissions'];
    protected $useTimestamps = false;
}
