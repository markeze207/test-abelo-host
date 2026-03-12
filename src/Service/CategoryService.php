<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Post;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

class CategoryService
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;
    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;

    /**
     *
     */
    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->postRepository = new PostRepository();
    }

    /**
     * @param int $limit
     * @param int|null $lastId
     * @param string|null $lastName
     * @return array
     */
    public function getCategoriesWithPostsPaginated(int $limit = 3, ?int $lastId = null, ?string $lastName = null): array
    {
        $categoriesData = $this->categoryRepository->getPaginatedWithPosts($limit, $lastId, $lastName);

        $totalCategories = ($lastId === null || $lastName === null)
            ? $this->categoryRepository->getTotalWithPostsCount()
            : null;

        $categories = [];
        foreach ($categoriesData as $categoryData) {
            $latestPostsData = $this->postRepository->getLatestByCategory(
                $categoryData['id'],
                3
            );

            $latestPosts = [];
            foreach ($latestPostsData as $postData) {
                $postCategories = $this->getCategoriesForPost($postData['id']);
                $postData['categories'] = $postCategories;
                $latestPosts[] = Post::fromArray($postData);
            }

            $category = Category::fromArray($categoryData);
            $category->posts = $latestPosts;
            $categories[] = $category;
        }

        return [
            'categories' => $categories,
            'total' => $totalCategories,
            'has_more' => count($categories) === $limit,
            'next_cursor' => !empty($categories) ? [
                'last_id' => end($categories)->id,
                'last_name' => end($categories)->name
            ] : null
        ];
    }


    /**
     * @param int $postId
     * @return array
     */
    private function getCategoriesForPost(int $postId): array
    {
        $categoriesData = $this->categoryRepository->getByPostId($postId);

        return array_map(
            fn($data) => Category::fromArray($data),
            $categoriesData
        );
    }

    /**
     * @param string $slug
     * @param string $sortBy
     * @param string $order
     * @param int $limit
     * @param int|null $lastId
     * @param mixed|null $lastValue
     * @return array|null
     */
    public function getCategoryBySlugWithPosts(string $slug, string $sortBy = 'created_at', string $order = 'DESC', int $limit = 10, ?int $lastId = null, $lastValue = null): ?array
    {
        $categoryData = $this->categoryRepository->findBySlug($slug);

        if (!$categoryData) {
            return null;
        }

        $id = $categoryData['id'];

        $postsData = $this->categoryRepository->getWithPostsById($id, $sortBy, $order, $limit, $lastId, $lastValue);

        // Получаем общее количество только для первой загрузки
        $totalPosts = ($lastId === null || $lastValue === null)
            ? $this->postRepository->getCountByCategory($id)
            : null;

        $posts = [];
        foreach ($postsData as $postData) {
            $categories = $this->getCategoriesForPost($postData['id']);
            $postData['categories'] = $categories;
            $posts[] = Post::fromArray($postData);
        }

        $categoryData['posts_count'] = $totalPosts ?? count($posts);
        $category = Category::fromArray($categoryData);

        return [
            'category' => $category,
            'posts' => $posts,
            'total' => $totalPosts,
            'limit' => $limit,
            'has_more' => count($posts) === $limit,
            'next_cursor' => !empty($posts) ? $this->buildNextCursor(end($posts), $sortBy, $order) : null
        ];
    }

    /**
     * @param Post $lastPost
     * @param string $sortBy
     * @param string $order
     * @return array
     */
    private function buildNextCursor(Post $lastPost, string $sortBy, string $order): array
    {
        $cursor = [
            'last_id' => $lastPost->id
        ];

        $cursor['last_value'] = match ($sortBy) {
            'views' => $lastPost->views,
            'title' => $lastPost->title,
            default => $lastPost->created_at,
        };

        return $cursor;
    }

    /**
     * @param string $slug
     * @param int $page
     * @param int $limit
     * @param string $sortBy
     * @param string $order
     * @return array|null
     */
    public function getCategoryWithPostsClassicPagination(string $slug, int $page, int $limit, string $sortBy, string $order): ?array
    {
        $categoryData = $this->categoryRepository->findBySlug($slug);
        if (!$categoryData) return null;

        $postsData = $this->postRepository->getPaginatedByCategory(
            $categoryData['id'],
            $page,
            $limit,
            $sortBy,
            $order
        );

        $totalPosts = $this->categoryRepository->getPostCount($categoryData['id']);

        $posts = [];
        foreach ($postsData as $postData) {
            $postData['categories'] = $this->categoryRepository->getByPostId($postData['id']);
            $posts[] = Post::fromArray($postData);
        }

        return [
            'category' => Category::fromArray($categoryData),
            'posts'    => $posts,
            'total'    => $totalPosts
        ];
    }
}