<?php

namespace App\Models;

use CodeIgniter\Model;

class KbCategoryModel extends Model
{
    protected $table      = 'kb_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'icon', 'color'];
    protected $useTimestamps = true;

    public function withArticleCount()
    {
        return $this->db->query("
            SELECT c.*, COUNT(a.id) as article_count
            FROM kb_categories c
            LEFT JOIN kb_articles a ON a.category_id = c.id AND a.status = 'published'
            GROUP BY c.id
            ORDER BY c.name ASC
        ")->getResultArray();
    }
}
