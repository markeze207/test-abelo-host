<?php

namespace App\Controller;

use App\Service\CategoryService;

class CategoryController extends BaseController
{
    private CategoryService $categoryService;

    public function __construct()
    {
        parent::__construct();
        $this->categoryService = new CategoryService();
    }

    public function view(string $slug): void
    {
        if (!$this->validate(['slug' => $slug], [
            'slug' => 'required|regex:/^[a-z0-9-]+$/|max:100'
        ])) {
            $this->notFound();
            return;
        }

        $params = [
            'sort' => $this->getParam('sort', 'created_at'),
            'order' => $this->getParam('order', 'DESC'),
            'page' => (int)$this->getParam('page', 1)
        ];

        if (!$this->validate($params, [
            'sort' => 'in:views,created_at,title',
            'order' => 'in:ASC,DESC',
            'page' => 'integer|min:1'
        ])) {
            $sortBy = 'created_at';
            $order = 'DESC';
            $page = 1;
        } else {
            $sortBy = $params['sort'];
            $order = $params['order'];
            $page = max(1, $params['page']);
        }

        $perPage = 6;

        $result = $this->categoryService->getCategoryBySlugWithPosts(
            $slug,
            $sortBy,
            $order,
            $page,
            $perPage
        );

        if (!$result) {
            $this->notFound();
            return;
        }

        $category = $result['category'];
        $posts = $result['posts'];

        $this->render('category/view.tpl', [
            'category' => $category,
            'posts' => $posts,
            'sort_by' => $sortBy,
            'order' => $order,
            'pagination' => [
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
                'total' => $result['total'],
                'per_page' => $result['per_page']
            ],
            'page_title' => $category->name
        ]);
    }
}