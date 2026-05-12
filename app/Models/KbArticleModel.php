<?php

namespace App\Models;

use CodeIgniter\Model;

class KbArticleModel extends Model
{
    protected $table      = 'kb_articles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_id', 'title', 'slug', 'excerpt', 'content',
        'tags', 'read_time', 'status', 'use_for_ai',
        'view_count', 'embedding', 'created_by'
    ];
    protected $useTimestamps = true;

    // Artikel published dengan info kategori
    public function getPublished(int $categoryId = 0, int $limit = 10, int $offset = 0)
    {
        $builder = $this->db->table('kb_articles a')
            ->select('a.id, a.category_id, a.title, a.slug, a.excerpt, a.tags, a.read_time, a.status, a.view_count, a.created_at, a.updated_at, c.name as category_name, c.icon as category_icon, c.color as category_color')
            ->join('kb_categories c', 'c.id = a.category_id')
            ->where('a.status', 'published');

        if ($categoryId) $builder->where('a.category_id', $categoryId);

        return $builder->orderBy('a.created_at', 'DESC')->limit($limit, $offset)->get()->getResultArray();
    }

    public function countPublished(int $categoryId = 0): int
    {
        $builder = $this->db->table('kb_articles')->where('status', 'published');
        if ($categoryId) $builder->where('category_id', $categoryId);
        return $builder->countAllResults();
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->db->table('kb_articles a')
            ->select('a.*, c.name as category_name, c.icon as category_icon, c.color as category_color')
            ->join('kb_categories c', 'c.id = a.category_id')
            ->where('a.slug', $slug)
            ->where('a.status', 'published')
            ->get()->getRowArray();
    }

    public function search(string $keyword, int $limit = 10): array
    {
        return $this->db->query("
            SELECT a.*, c.name as category_name,
                MATCH(a.title, a.excerpt, a.content) AGAINST(? IN BOOLEAN MODE) as relevance
            FROM kb_articles a
            JOIN kb_categories c ON c.id = a.category_id
            WHERE a.status = 'published'
              AND MATCH(a.title, a.excerpt, a.content) AGAINST(? IN BOOLEAN MODE)
            ORDER BY relevance DESC
            LIMIT ?
        ", [$keyword, $keyword, $limit])->getResultArray();
    }

    // Ambil semua artikel AI-enabled beserta embedding untuk RAG
    public function getForRag(): array
    {
        return $this->db->query(
            "SELECT id, title, slug, excerpt, content, embedding
             FROM kb_articles
             WHERE status = 'published' AND use_for_ai = 1 AND embedding IS NOT NULL"
        )->getResultArray();
    }

    public function incrementView(int $id): void
    {
        $this->db->query("UPDATE kb_articles SET view_count = view_count + 1 WHERE id = ?", [$id]);
    }

    // Admin: semua artikel dengan filter
    public function adminList(array $filters = [], int $limit = 15, int $offset = 0): array
    {
        $builder = $this->db->table('kb_articles a')
            ->select('a.*, c.name as category_name, u.name as author_name')
            ->join('kb_categories c', 'c.id = a.category_id')
            ->join('users u', 'u.id = a.created_by', 'left');

        if (!empty($filters['search']))
            $builder->like('a.title', $filters['search']);
        if (!empty($filters['category_id']))
            $builder->where('a.category_id', $filters['category_id']);
        if (!empty($filters['status']))
            $builder->where('a.status', $filters['status']);

        return $builder->orderBy('a.updated_at', 'DESC')->limit($limit, $offset)->get()->getResultArray();
    }

    public function adminCount(array $filters = []): int
    {
        $builder = $this->db->table('kb_articles a');
        if (!empty($filters['search'])) $builder->like('a.title', $filters['search']);
        if (!empty($filters['category_id'])) $builder->where('a.category_id', $filters['category_id']);
        if (!empty($filters['status'])) $builder->where('a.status', $filters['status']);
        return $builder->countAllResults();
    }

    public function generateSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        $base = $slug;
        $i = 1;
        while ($this->db->table('kb_articles')->where('slug', $slug)->countAllResults()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
