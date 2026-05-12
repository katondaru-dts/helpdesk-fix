<?php

namespace App\Controllers;

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
        $categoryId = (int) $this->request->getGet('cat');
        $page       = max(1, (int) $this->request->getGet('page'));
        $perPage    = 8;
        $offset     = ($page - 1) * $perPage;

        $total    = $this->articleModel->countPublished($categoryId);
        $articles = $this->articleModel->getPublished($categoryId, $perPage, $offset);
        $categories = $this->categoryModel->withArticleCount();

        return view('knowledge_base/index', [
            'activePage'   => 'knowledge-base',
            'pageTitle'    => 'Knowledge Base',
            'articles'     => $articles,
            'categories'   => $categories,
            'total'        => $total,
            'currentPage'  => $page,
            'perPage'      => $perPage,
            'currentCat'   => $categoryId,
            'popular'      => $this->articleModel->getPublished(0, 5, 0),
        ]);
    }

    public function show(string $slug)
    {
        $article = $this->articleModel->getBySlug($slug);
        if (!$article) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $this->articleModel->incrementView($article['id']);

        // Related articles (same category)
        $related = $this->articleModel->getPublished((int)$article['category_id'], 4, 0);
        $related = array_filter($related, fn($a) => $a['id'] != $article['id']);

        return view('knowledge_base/show', [
            'activePage' => 'knowledge-base',
            'pageTitle'  => $article['title'],
            'article'    => $article,
            'related'    => array_values($related),
        ]);
    }

    public function search()
    {
        $keyword  = trim($this->request->getGet('q') ?? '');
        $articles = $keyword ? $this->articleModel->search($keyword) : [];

        return view('knowledge_base/search', [
            'activePage' => 'knowledge-base',
            'pageTitle'  => 'Hasil Pencarian',
            'keyword'    => $keyword,
            'articles'   => $articles,
        ]);
    }
}
