<?php

namespace App\Controller;

use App\Service\PostService;
use App\Service\CategoryService;

class HomeController extends BaseController
{
    private PostService $postService;
    private CategoryService $categoryService;

    private const CATEGORIES_PER_PAGE = 3;

    public function __construct()
    {
        parent::__construct();
        $this->postService = new PostService();
        $this->categoryService = new CategoryService();
    }

    public function index(): void
    {
        // Валидация параметра page
        $page = (int)($this->getParam('page', 1));

        if (!$this->validate(['page' => $page], [
            'page' => 'numeric|min:1|integer'
        ])) {
            $page = 1;
        }

        // Получаем первую страницу категорий
        $categoriesData = $this->categoryService->getCategoriesWithPostsPaginated($page, self::CATEGORIES_PER_PAGE);
        $popularPosts = $this->postService->getPopularPosts(6);
        $latestPosts = $this->postService->getHomepagePosts(6);

        $this->render('home/index.tpl', [
            'categories' => $categoriesData['categories'],
            'categories_total' => $categoriesData['total'],
            'categories_per_page' => self::CATEGORIES_PER_PAGE,
            'has_more_categories' => $categoriesData['total'] > self::CATEGORIES_PER_PAGE,
            'popular_posts' => $popularPosts,
            'latest_posts' => $latestPosts,
            'popular_total' => $this->postService->getTotalPublishedCount(),
            'latest_total' => $this->postService->getTotalPublishedCount(),
            'page_title' => 'Главная'
        ]);
    }

    public function loadMoreCategories(): void
    {
        // Валидация параметра page
        $page = (int)($this->getParam('page', 1));

        if (!$this->validate(['page' => $page], [
            'page' => 'required|numeric|min:1|integer'
        ])) {
            $this->json([
                'success' => false,
                'error' => 'Неверный номер страницы'
            ], 400);
            return;
        }

        $categoriesData = $this->categoryService->getCategoriesWithPostsPaginated($page, self::CATEGORIES_PER_PAGE);

        $categories = array_map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'url' => $category->getUrl(),
                'posts' => array_map(function($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'excerpt' => $post->excerpt ?? '',
                        'image' => $post->getImageUrl(),
                        'url' => $post->getUrl(),
                        'views' => $post->views,
                        'created_at' => $post->getFormattedDate('d.m.Y')
                    ];
                }, $category->posts)
            ];
        }, $categoriesData['categories']);

        $this->json([
            'success' => true,
            'categories' => $categories,
            'has_more' => ($page * self::CATEGORIES_PER_PAGE) < $categoriesData['total'],
            'next_page' => $page + 1,
            'total' => $categoriesData['total'],
            'loaded' => count($categories)
        ]);
    }
}