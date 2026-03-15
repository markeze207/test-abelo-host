<?php

namespace App\Controller;

use App\Service\CategoryService;

class CategoryController extends BaseController
{

    public function __construct(private readonly CategoryService $categoryService)
    {
        parent::__construct();
    }

    public function view(string $slug): void
    {
        if (!$this->validate(['slug' => $slug], [
            'slug' => 'required|regex:/^[a-z0-9-]+$/|max:100'
        ])) {
            $this->notFound();
            return;
        }

        $page = $this->getParam('page') ? (int)$this->getParam('page') : 1;
        $sortBy = $this->getParam('sort', 'created_at');
        $order = $this->getParam('order', 'DESC');
        $limit = 6;

        $result = $this->categoryService->getCategoryWithPostsClassicPagination(
            $slug,
            $page,
            $limit,
            $sortBy,
            $order
        );

        if (!$result) {
            $this->notFound();
            return;
        }

        $totalPosts = $result['total'];
        $totalPages = ceil($totalPosts / $limit);

        $this->render('category/view.tpl', [
            'category'    => $result['category'],
            'posts'       => $result['posts'],
            'sort_by'     => $sortBy,
            'order'       => $order,
            'page_title'  => $result['category']->name,
            'pagination'  => [
                'current_page' => $page,
                'total_pages'  => $totalPages,
                'total_items'  => $totalPosts
            ]
        ]);
    }
}