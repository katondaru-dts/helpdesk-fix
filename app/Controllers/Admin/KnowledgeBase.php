<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\GeminiHelper;
use App\Helpers\MarkdownHelper;
use App\Models\KbArticleModel;
use App\Models\KbCategoryModel;

class KnowledgeBase extends BaseController
{
    protected $articleModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->articleModel  = new KbArticleModel();
        $this->categoryModel = new KbCategoryModel();
    }

    public function index()
    {
        $filters = [
            'search'      => $this->request->getGet('search') ?? '',
            'category_id' => (int) $this->request->getGet('cat'),
            'status'      => $this->request->getGet('status') ?? '',
        ];
        $page    = max(1, (int) $this->request->getGet('page'));
        $perPage = 15;
        $offset  = ($page - 1) * $perPage;

        return view('admin/knowledge_base/index', [
            'activePage' => 'admin-kb',
            'pageTitle'  => 'Admin Knowledge Base',
            'articles'   => $this->articleModel->adminList($filters, $perPage, $offset),
            'total'      => $this->articleModel->adminCount($filters),
            'categories' => $this->categoryModel->findAll(),
            'filters'    => $filters,
            'currentPage'=> $page,
            'perPage'    => $perPage,
            'stats'      => $this->getStats(),
        ]);
    }

    public function create()
    {
        return view('admin/knowledge_base/form', [
            'activePage' => 'admin-kb',
            'pageTitle'  => 'Tambah Artikel',
            'article'    => null,
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $data = $this->applyMdFile($data);
        $data['slug']       = $this->articleModel->generateSlug($data['title']);
        $data['created_by'] = session()->get('id');
        $data['use_for_ai'] = isset($data['use_for_ai']) ? 1 : 0;

        if (!empty($data['status_submit'])) $data['status'] = $data['status_submit'];
        unset($data['status_submit']);

        if ($data['use_for_ai'] && $data['status'] === 'published') {
            $data['embedding'] = $this->generateEmbedding($data['title'], $data['content']);
        }

        $this->articleModel->insert($data);
        return redirect()->to(base_url('admin/knowledge-base'))->with('success', 'Artikel berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $article = $this->articleModel->find($id);
        if (!$article) return redirect()->to(base_url('admin/knowledge-base'));

        return view('admin/knowledge_base/form', [
            'activePage' => 'admin-kb',
            'pageTitle'  => 'Edit Artikel',
            'article'    => $article,
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $data = $this->request->getPost();
        $data = $this->applyMdFile($data);
        $data['use_for_ai'] = isset($data['use_for_ai']) ? 1 : 0;
        unset($data['slug']);

        if (!empty($data['status_submit'])) $data['status'] = $data['status_submit'];
        unset($data['status_submit']);

        if ($data['use_for_ai'] && ($data['status'] ?? '') === 'published') {
            $data['embedding'] = $this->generateEmbedding($data['title'], $data['content']);
        } elseif (!$data['use_for_ai']) {
            $data['embedding'] = null;
        }

        $this->articleModel->update($id, $data);
        return redirect()->to(base_url('admin/knowledge-base'))->with('success', 'Artikel berhasil diperbarui.');
    }

    private function applyMdFile(array $data): array
    {
        $file = $this->request->getFile('md_file');
        if (!$file || !$file->isValid() || $file->getSize() === 0) return $data;

        $md = file_get_contents($file->getTempName());
        $data['content'] = MarkdownHelper::toHtml($md);

        // Ambil judul dari baris pertama jika kosong
        if (empty(trim($data['title'] ?? ''))) {
            preg_match('/^#\s+(.+)/m', $md, $m);
            if (!empty($m[1])) $data['title'] = trim($m[1]);
        }

        // Ambil excerpt dari paragraf pertama jika kosong
        if (empty(trim($data['excerpt'] ?? ''))) {
            preg_match('/^(?!#)[^\n]{20,}/m', $md, $m);
            if (!empty($m[0])) $data['excerpt'] = mb_strimwidth(trim($m[0]), 0, 200, '...');
        }

        return $data;
    }

    private function generateEmbedding(string $title, string $content): ?string
    {
        try {
            $gemini = new GeminiHelper();
            $text   = $title . "\n" . mb_substr(strip_tags($content), 0, 2000);
            $vec    = $gemini->embed($text);
            return $vec ? json_encode($vec) : null;
        } catch (\Throwable $e) {
            log_message('error', 'KB embedding error: ' . $e->getMessage());
            return null;
        }
    }

    public function delete(int $id)
    {
        $this->articleModel->delete($id);
        return redirect()->to(base_url('admin/knowledge-base'))->with('success', 'Artikel dihapus.');
    }

    public function reembed(int $id)
    {
        $article = $this->articleModel->find($id);
        if (!$article) return $this->response->setJSON(['error' => 'Artikel tidak ditemukan'], 404);

        $embedding = $this->generateEmbedding($article['title'], $article['content']);
        $this->articleModel->update($id, ['embedding' => $embedding]);

        return $this->response->setJSON(['success' => true, 'message' => 'Embedding berhasil diperbarui']);
    }

    // ── CATEGORY CRUD (JSON response) ──

    public function getCategories()
    {
        return $this->response->setJSON($this->categoryModel->withArticleCount());
    }

    public function storeCategory()
    {
        $data = $this->request->getJSON(true);
        if (empty($data['name'])) return $this->response->setJSON(['error' => 'Nama wajib diisi'], 422);
        $id = $this->categoryModel->insert(['name' => $data['name'], 'icon' => $data['icon'] ?? 'bi-folder', 'color' => $data['color'] ?? '#2563EB']);
        return $this->response->setJSON(['id' => $id, 'message' => 'Kategori ditambahkan']);
    }

    public function updateCategory(int $id)
    {
        $data = $this->request->getJSON(true);
        $this->categoryModel->update($id, array_intersect_key($data, array_flip(['name','icon','color'])));
        return $this->response->setJSON(['message' => 'Kategori diperbarui']);
    }

    public function deleteCategory(int $id)
    {
        $this->categoryModel->delete($id);
        return $this->response->setJSON(['message' => 'Kategori dihapus']);
    }

    private function getStats(): array
    {
        $db = \Config\Database::connect();
        return [
            'total'     => $db->table('kb_articles')->countAllResults(),
            'published' => $db->table('kb_articles')->where('status','published')->countAllResults(),
            'draft'     => $db->table('kb_articles')->where('status','draft')->countAllResults(),
            'views'     => (int) $db->query("SELECT COALESCE(SUM(view_count),0) as v FROM kb_articles")->getRow()->v,
        ];
    }
}
