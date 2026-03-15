<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Post;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;

class CategoryService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getCategoriesWithPostsPaginated(
        int $limit = 3,
        ?int $lastId = null,
        ?string $lastName = null
    ): array {
        $categoriesData = $this->categoryRepository->getPaginatedWithPosts($limit, $lastId, $lastName);

        $totalCategories = ($lastId === null || $lastName === null)
            ? $this->categoryRepository->getTotalWithPostsCount()
            : null;

        $categories = [];
        foreach ($categoriesData as $categoryData) {
            $latestPostsData = $this->postRepository->getLatestByCategory($categoryData['id'], 3);

            $latestPosts = [];
            foreach ($latestPostsData as $postData) {
                $postData['categories'] = $this->getCategoriesForPost($postData['id']);
                $latestPosts[]          = Post::fromArray($postData);
            }

            $category         = Category::fromArray($categoryData);
            $category->posts  = $latestPosts;
            $categories[]     = $category;
        }

        return [
            'categories'  => $categories,
            'total'       => $totalCategories,
            'has_more'    => count($categories) === $limit,
            'next_cursor' => !empty($categories) ? [
                'last_id'   => end($categories)->id,
                'last_name' => end($categories)->name,
            ] : null,
        ];
    }

    public function getCategoryWithPostsClassicPagination(
        string $slug,
        int $page,
        int $limit,
        string $sortBy,
        string $order
    ): ?array {
        $categoryData = $this->categoryRepository->findBySlug($slug);

        if (!$categoryData) {
            return null;
        }

        $postsData  = $this->postRepository->getPaginatedByCategory(
            $categoryData['id'],
            $page,
            $limit,
            $sortBy,
            $order
        );
        $totalPosts = $this->categoryRepository->getPostCount($categoryData['id']);

        $posts = [];
        foreach ($postsData as $postData) {
            $postData['categories'] = $this->getCategoriesForPost($postData['id']);
            $posts[]                = Post::fromArray($postData);
        }

        return [
            'category' => Category::fromArray($categoryData),
            'posts'    => $posts,
            'total'    => $totalPosts,
        ];
    }

    private function getCategoriesForPost(int $postId): array
    {
        return array_map(
            fn($data) => Category::fromArray($data),
            $this->categoryRepository->getByPostId($postId)
        );
    }
}