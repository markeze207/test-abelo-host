<?php

namespace App\Controller;

use App\Service\PostService;
use App\Service\CategoryService;

class HomeController extends BaseController
{
    private const CATEGORIES_PER_PAGE = 3;

    public function __construct(private readonly PostService $postService, private readonly CategoryService $categoryService)
    {
        parent::__construct();
    }

    public function index(): void
    {
        $categoriesData = $this->categoryService->getCategoriesWithPostsPaginated(self::CATEGORIES_PER_PAGE);
        $popularData = $this->postService->getPopularPostsPaginated(6);
        $latestData = $this->postService->getLatestPostsPaginated(6);

        $this->render('home/index.tpl', [
            'categories'             => $categoriesData['categories'],
            'has_more_categories'    => $categoriesData['has_more'],
            'next_categories_cursor' => $categoriesData['next_cursor'],

            'latest_posts'           => $latestData['posts'],
            'has_more_latest'        => $latestData['has_more'],
            'latest_next_cursor'     => $latestData['next_cursor'],

            'popular_posts'          => $popularData['posts'],
            'has_more_popular'       => $popularData['has_more'],
            'popular_next_cursor'    => $popularData['next_cursor'],

            'base_url'               => ''
        ]);
    }

    public function loadMoreCategories(): void
    {
        $lastId = $this->getParam('last_id') ? (int)$this->getParam('last_id') : null;
        $lastName = $this->getParam('last_name');
        $limit = self::CATEGORIES_PER_PAGE;

        $categoriesData = $this->categoryService->getCategoriesWithPostsPaginated($limit, $lastId, $lastName);

        $categories = array_map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'url' => $category->getUrl(),
                'description' => $category->description,
                'posts' => array_map(function($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'url' => $post->getUrl(),
                        'image' => $post->getImageUrl(),
                        'excerpt' => $post->getExcerpt(150),
                        'views' => $post->views,
                        'created_at' => $post->getFormattedDate('d.m.Y')
                    ];
                }, $category->posts)
            ];
        }, $categoriesData['categories']);

        $this->json([
            'success' => true,
            'categories' => $categories,
            'has_more' => $categoriesData['has_more'],
            'next_cursor' => $categoriesData['next_cursor']
        ]);
    }
}